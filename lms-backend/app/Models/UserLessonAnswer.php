<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Traits\Uuid;
use Dotenv\Parser\Lexer;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLessonAnswer extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $appends = ['user_course_category', 'lesson'];

    public function scopeSearch($query, $data)
    {
        if ($data->courses_id) {
            $query->where('courses_id', $data->courses_id);
        }

        if ($data->with_user) {
            $query->with('user');
        }

        if ($data->with_course) {
            $query->with('course');
        }

        if ($data->search) {
            for ($i = 0; $i < count($this->fillable); $i++) {
                if ($i == 0) {
                    $query->where($this->fillable[$i], 'like', '%' . $data->search . '%');
                } else {
                    $query->orWhere($this->fillable[$i], 'like', '%' . $data->search . '%');
                }
            }
            $query->whereHas('user', function ($q) use ($data) {
                $q->where('fullname', 'like', "%{$data->search}%")
                    ->orWhere('email', 'like', "%{$data->search}%");
            });
        }

        if ($data->sortBy || $data->orderBy) {
            if (empty($data->orderBy)) {
                $data->orderBy = 'asc';
            }
            $query->orderBy($data->sortBy, $data->orderBy);
        }
        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    // public function lesson()
    // {
    //     return $this->belongsTo(Lesson::class, 'lessons_id', 'id');
    // }

    public function getLessonAttribute()
    {
        return Lesson::find($this->lessons_id);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'courses_id', 'id');
    }

    public function getUserCourseCategoryAttribute()
    {
        $user = $this->user()->first();
        $course = $this->course()->first();
        return UserCourseCategory::where('user_id', $user->id)->where('course_category_id', $course->categories_id)->first();
    }
}
