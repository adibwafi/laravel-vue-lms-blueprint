<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        Uuid,
        SoftDeletes,
        HasRoles;


    public $incrementing = false;

    protected $keyType = 'uuid';

    protected static $withTracking = false;

    public function scopeWithTracking($query)
    {
        self::$withTracking = true;

        return $query;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'status',
        'password',
        'phone',
        'image',
        'is_blocked',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['tracking'];


    private static function addCourseQuery($query)
    {
        $query->with(['course' => function ($query) {
            $query->select('id', 'name', 'categories_id')->withoutAppends()->without('tutor');
            $query->with(['courseCategory' => function ($query) {
                $query->select('id', 'name')->withoutAppends();
            }]);
        }]);
    }

    public function scopeSearch($query, $data)
    {
        if ($data->with_tracking) {
            $query->withTracking();
        }

        if ($data->with_tracking_detail) {
            $query
                ->with(['userCourseCategory' => function ($query) {
                    $query->with(['courseCategory' => function ($query) {
                        $query->without('tutor')->withoutAppends();
                    }]);
                }])
                ->with([
                    'userCourse' => function ($query) {
                        $query->with(['course' => function ($query) {
                            $query->without('tutor')->withoutAppends();
                        }]);
                    }
                ])
                ->with([
                    'examPoinQuiz' => function ($query) {
                        $query->with(['exam' => function ($query) {
                            $query
                                ->select('id', 'name', 'kkm', 'lesson_id') // Select necessary columns
                                ->with(['lesson' => function ($query) {
                                    $query
                                        ->select('id', 'courses_id', 'name') // Select necessary columns
                                        ->with(['course' => function ($query) {
                                            $query->select('id', 'name', 'categories_id') // Only needed fields from course
                                                ->with([
                                                    'courseCategory' => function ($query) {
                                                        $query->select('id', 'name'); // Only needed fields from courseCategory
                                                    }
                                                ]);
                                        }]);
                                }]);
                        }]);
                    }
                ])
                ->with(['examPoinExam' => function ($query) {
                    $query->with(['exam' => function ($query) {
                        self::addCourseQuery($query);
                    }]);
                }]);
            // error_log("Query: " . json_encode($query->toSql()));
        }

        if ($data->admin_only) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', 'super-admin');
            });
        }

        if ($data->search) {
            $searchFields = ['id', 'fullname', 'email', 'phone'];
            $query->where(function ($q) use ($data, $searchFields) {
                foreach ($searchFields as $i => $field) {
                    if ($i === 0) {
                        $q->where($field, 'like', '%' . $data->search . '%');
                    } else {
                        $q->orWhere($field, 'like', '%' . $data->search . '%');
                    }
                }
            });
        }

        if ($data->sortBy || $data->orderBy) {
            if (empty($data->orderBy)) {
                $data->orderBy = 'asc';
            }
            $query->orderBy($data->sortBy, $data->orderBy);
        }
        // error_log("Query: " . json_encode($query->toSql()));
        return $query;
    }

    public function getTrackingAttribute()
    {
        if (!self::$withTracking) {
            return null;
        }
        $number_course_category_enrolled = $this->userCourseCategory()->count();
        $number_course_enrolled = $this->userCourse()->count();
        $number_lesson_completed = $this->userLesson()->count();
        $number_exam_completed = $this->examPoin()->with('exam')->whereHas('exam', function ($query) {
            $query->where('is_quiz', false);
        })->distinct('exam_id')->count();
        $number_quiz_completed = $this->examPoin()->with('exam')->whereHas('exam', function ($query) {
            $query->where('is_quiz', true);
        })->distinct('exam_id')->count();
        $average_exam_score = $this->examPoin()->with('exam')->whereHas('exam', function ($query) {
            $query->where('is_quiz', false);
        })->get()->avg('score');
        $average_quiz_score = $this->examPoin()->with('exam')->whereHas('exam', function ($query) {
            $query->where('is_quiz', true);
        })->get()->avg('score');
        // error_log("Get Tracking Attribute");

        return [
            'number_course_category_enrolled' => $number_course_category_enrolled,
            'number_course_enrolled' => $number_course_enrolled,
            'number_lesson_completed' => $number_lesson_completed,
            'number_exam_completed' => $number_exam_completed,
            'number_quiz_completed' => $number_quiz_completed,
            'average_quiz_score' => $average_quiz_score ? $average_quiz_score : null,
            'average_exam_score' => $average_exam_score ? $average_exam_score : null,
        ];
    }

    public function otp()
    {
        return $this->hasMany(UserVerification::class, 'users_id');
    }

    public function userResetPass()
    {
        return $this->hasMany(UserResetPassword::class, 'users_id');
    }

    public function lessonPageRating()
    {
        return $this->hasMany(LessonPageRating::class, 'users_id');
    }

    public function lessonRating()
    {
        return $this->hasMany(LessonRating::class, 'users_id');
    }

    public function userCourse()
    {
        return $this->hasMany(UserCourse::class, 'users_id');
    }

    public function userLesson()
    {
        return $this->hasMany(UserLesson::class, 'users_id');
    }

    public function userCourseCategory()
    {
        return $this->hasMany(UserCourseCategory::class, 'user_id')->with(['courseCategory', 'courseCategory.tutor']);
    }

    public function examPoinQuiz()
    {
        return $this->hasMany(ExamPoin::class, 'user_id')->with('exam')->whereHas('exam', function ($query) {
            $query->where('is_quiz', true);
        });
    }

    public function examPoinExam()
    {
        return $this->hasMany(ExamPoin::class, 'user_id')->with('exam')->whereHas('exam', function ($query) {
            $query->where('is_quiz', false);
        });
    }

    public function examPoin()
    {
        return $this->hasMany(ExamPoin::class, 'user_id');
    }

    public function examAnswer()
    {
        return $this->hasMany(ExamAnswer::class, 'user_id');
    }

    public function userCertificate()
    {
        return $this->hasMany(Certificate::class, 'user_id', 'id');
    }
}
