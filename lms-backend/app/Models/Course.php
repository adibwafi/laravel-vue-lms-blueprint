<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Course extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'uuid';
    protected $appends = ['lesson_finish', 'lesson_total', 'exam_total', 'exam_finish', 'is_completed', 'tracking', 'ratio_completed'];
    protected $hidden = [];

    protected $guarded = [];

    protected $withoutAppends = false;

    public function scopeWithoutAppends($query)
    {
        $this->withoutAppends = true;

        return $query;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'categories_id',
    //     'tutors_id',
    //     'name',
    //     'banner',
    //     'description',
    //     'what_learn',
    //     'skill',
    //     'requirements',
    //     'price',
    //     'rating',
    //     'status',
    //     'link_payment',
    // ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'what_learn' => 'array',
        'skill' => 'array',
        'requirements' => 'array',
    ];

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function scopeSearch($query, $data)
    {
        if ($data->search) {
            for ($i = 0; $i < count($this->getTableColumns()); $i++) {
                if ($i == 0) {
                    $query->where($this->getTableColumns()[$i], 'like', '%' . $data->search . '%');
                } else {
                    $query->orWhere($this->getTableColumns()[$i], 'like', '%' . $data->search . '%');
                }
            }
        }

        if (!empty($data->categories_id)) {
            $query->where('categories_id', $data->categories_id);
        }

        if ($data->sortBy || $data->orderBy) {
            if (empty($data->orderBy)) {
                $data->orderBy = 'asc';
            }
            $query->orderBy($data->sortBy, $data->orderBy);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        return $query;
    }

    public function tutor()
    {
        return $this->belongsToMany(Tutor::class, 'course_tutor');
    }

    public function userCourse()
    {
        return $this->hasMany(UserCourse::class, 'courses_id', 'id');
    }

    public function lesson()
    {
        return $this->hasMany(Lesson::class, 'courses_id');
    }

    public function exam()
    {
        return $this->hasMany(Exam::class, 'course_id');
    }

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'categories_id', 'id');
    }

    public function getCompletedUser()
    {
        $userCourse = $this->userCourse()->with('user')->get();
        $sum = 0;
        foreach ($userCourse as $uc) {
            $this->getIsCompletedAttribute($uc->user->id) ? $sum++ : null;
        }
        return $sum;
    }

    public function getAverageCompleted($number_enrolled_user)
    {
        $lessons = $this->lesson()->get();
        $sum = 0;
        foreach ($lessons as $l) {
            $sum += $l->userLesson()->count();
        }
        $divider = $this->lesson()->count() * $number_enrolled_user;
        if ($divider != 0) {
            return $sum / $divider * 100;
        }
        return 0;
    }

    public function getTrackingAttribute()
    {
        if ($this->withoutAppends) {
            return null;
        }


        if (!Auth::user()) {
            return null;
        }


        // if (Auth::user()->hasAnyRole('Super-Admin')) {
        //     $courseCategory = $this->courseCategory()->first();
        //     if ($courseCategory) {
        //         $number_enrolled_user_in_course_categories = $courseCategory->getNumberEnrolledUser();
        //         $number_enrolled_user = $this->userCourse()->count();
        //         $number_completed_user = $this->getCompletedUser();
        //         $average_percentage_completed = $this->getAverageCompleted($number_enrolled_user);
        //         return [
        //             'number_enrolled_user' => $number_enrolled_user,
        //             'number_unenrolled_user' => $number_enrolled_user_in_course_categories - $number_enrolled_user,
        //             'number_completed_user' => $number_completed_user,
        //             'average_percentage_completed' => $average_percentage_completed
        //         ];
        //     } else {
        //         return null;
        //     }
        // } else {
        //     return null;
        // }
    }

    public function getRatioCompletedAttribute($userID = null)
    {
        if ($this->withoutAppends) {
            return null;
        }
        if (request()->user()) {
            if (!$userID) {
                $userID = request()->user()->id;
            }

            $divisor = $this->getLessonTotalAttribute()  + $this->getExamTotalAttribute();

            if ($divisor == 0) {
                return 0;
            }

            $dividend = $this->getLessonFinishAttribute($userID) + $this->getExamFinishAttribute($userID);

            return $dividend / $divisor;
        }
    }


    public function getIsCompletedAttribute($userID = null)
    {
        if ($this->withoutAppends) {
            return null;
        }
        if (request()->user()) {
            if (!$userID) {
                $userID = request()->user()->id;
            }
            if ($this->getLessonTotalAttribute()  + $this->getExamTotalAttribute() === 0) {
                return false;
            }

            if (
                ($this->getLessonFinishAttribute($userID) + $this->getExamFinishAttribute($userID)) /
                ($this->getLessonTotalAttribute()  + $this->getExamTotalAttribute()) >= 1
            ) {
                return true;
            }
            return false;
        }
    }

    public function getLessonFinishAttribute($userID = null)
    {
        if ($this->withoutAppends) {
            return null;
        }
        if (request()->user()) {
            if (!$userID) {
                $userID = request()->user()->id;
            }
            $c = 0;
            $lessons = $this->lesson()->get();
            if ($lessons->isNotEmpty()) {
                foreach ($lessons as $lesson) {
                    $c += $lesson->userLesson()->where('users_id', $userID)->first() ? 1 : 0;
                }
                return $c;
            }
            return $c;
        }
    }

    public function getLessonTotalAttribute()
    {
        if ($this->withoutAppends) {
            return null;
        }
        return $this->lesson()->count();
    }

    public function getExamTotalAttribute()
    {
        if ($this->withoutAppends) {
            return null;
        }
        return $this->exam()->count();
    }

    public function getExamFinishAttribute($userID = null)
    {
        if ($this->withoutAppends) {
            return null;
        }

        if (request()->user()) {
            if (!$userID) {
                $userID = request()->user()->id;
            }
            $c = 0;
            $exams = $this->exam()->get();
            if ($exams->isNotEmpty()) {
                $validExam = false;
                foreach ($exams as $exam) {
                    $highestGrade = $exam->getHighestGradeAttribute();
                    $finishedAtleastOneExam = $exam->examPoin()->where('user_id', $userID)->where('finished_at', '!=', null)->first() ? true : false;
                    if ($finishedAtleastOneExam) {
                        $validExam = true;
                        if ($exam->kkm != null) {
                            if ($highestGrade < $exam->kkm) {
                                $validExam = false;
                            }
                        }

                        if ($exam->max_attempt != null && $exam->getAttemptLeftAttribute() == 0) {
                            $validExam = true;
                        }
                    }

                    if ($validExam) {
                        $c++;
                    }
                }
                return $c;
            }
            return $c;
        }
    }

    public function userLessonAnswer()
    {
        $userID = null;
        if (request()->user()) {
            $userID = request()->user()->id;
        }
        return $this->hasMany(UserLessonAnswer::class, 'courses_id', 'id')->where('users_id', $userID);
    }
}
