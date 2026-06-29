<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
// use App\Traits\Uuid;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = false;

    protected $withoutAppends = false;

    public function scopeWithoutAppends($query)
    {
        $this->withoutAppends = true;

        return $query;
    }

    // protected $keyType = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'courses_id',
    //     'name',
    //     'learn_time',
    //     'sorter',
    //     'rating',
    // ];

    protected $guarded = [];

    protected $appends = ['is_completed', 'is_feedbacked', 'tracking'];

    protected $search = [
        'name'
    ];

    public function scopeSearch($query, $data)
    {
        $query->where('courses_id', $data->courses_id);

        if ($data->search) {
            for ($i = 0; $i < count($this->search); $i++) {
                if ($i == 0) {
                    $query->Where($this->search[$i], 'like', '%' . $data->search . '%');
                } else {
                    $query->orWhere($this->search[$i], 'like', '%' . $data->search . '%');
                }
            }
        }


        if ($data->sortBy || $data->orderBy) {
            if (empty($data->orderBy)) {
                $data->orderBy = 'asc';
            }
            $query->orderBy($data->sortBy, $data->orderBy);
        } else {
            $query->orderBy('sorter', 'asc');
        }
        return $query;
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'courses_id', 'id');
    }

    public function exam()
    {
        return $this->hasOne(Exam::class, 'lesson_id', 'id');
    }

    public function lessonPage()
    {
        return $this->hasMany(LessonPage::class, 'lessons_id');
    }

    public function lessonRating()
    {
        return $this->hasMany(LessonRating::class, 'lessons_id');
    }

    public function userLesson()
    {
        return $this->hasMany(UserLesson::class, 'lessons_id');
    }

    public function currentUserLesson()
    {
        if (Auth::user()) {
            return $this->hasMany(UserLesson::class, 'lessons_id')->where('users_id', Auth::user()->id);
        } else {
            return null;
        }
    }

    public function getTrackingAttribute()
    {
        if ($this->withoutAppends) {
            return null;
        }

        if (!Auth::user()) {
            return null;
        }

        if (Auth::user()->hasAnyRole('Super-Admin')) {
            $number_user_completed = null;
            if ($this->is_quiz) {
                if ($this->exam) {
                    $number_user_completed = $this->exam->examPoin()->groupBy('user_id')->count();
                }
            } else {
                $number_user_completed = $this->userLesson()->count();
            }
            return [
                'number_user_completed' => $number_user_completed,
            ];
        } else {
            return null;
        }
    }

    public function getIsCompletedAttribute()
    {
        if ($this->withoutAppends) {
            return null;
        }
        if ($this->is_quiz) {
            $exam = $this->exam()->first();
            if ($exam) {
                return $exam->getExamDoneAttribute() ? 'true' : 'false';
            } else {
                return 'false';
            }
        } else {
            return $this->currentUserLesson()->first() ? 'true' : 'false';
        }
    }

    public function getIsFeedbackedAttribute()
    {
        if ($this->withoutAppends) {
            return null;
        }
        $userID = request()->user()->id;
        return $this->lessonRating->where('users_id', $userID)->first() ? 'true' : 'false';
    }

    // this is the recommended way for declaring event handlers
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            try {
                $model->id = (string) Str::uuid(); // generate uuid
                // Change id with your primary key
            } catch (\Throwable $th) {
                abort(500, $th->getMessage());
            }
        });

        self::deleting(function ($lesson) { // before delete() method call this
            $lesson->lessonPage()->each(function ($photo) {
                $photo->delete(); // <-- direct deletion
            });
            $lesson->lessonRating()->each(function ($post) {
                $post->delete(); // <-- raise another deleting event on Post to delete comments
            });
            $lesson->userLesson()->each(function ($post) {
                $post->delete(); // <-- raise another deleting event on Post to delete comments
            });
        });
    }

    public function userLessonAnswer()
    {
        if (Auth::user()) {
            return $this->hasMany(UserLessonAnswer::class, 'lessons_id')->where('users_id', Auth::user()->id);
        } else {
            return null;
        }
    }
}
