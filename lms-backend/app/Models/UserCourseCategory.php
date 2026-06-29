<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class UserCourseCategory extends Model
{
    use HasFactory, Uuid;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $appends = [
        'user_complete',
        'user_progress_percentage'
    ];

    public function scopeSearch($query, $data)
    {
        if ($data->user_id) {
            $query->where('user_id', $data->user_id);
        }

        if ($data->course_category_id) {
            $query->where('course_category_id', $data->course_category_id);
        }

        if ($data->with_course_category) {
            $query->with('courseCategory');
        }

        if ($data->with_user) {
            $query->with('user');
        }

        if ($data->search) {
            array_push($this->fillable, "id");
            for ($i = 0; $i < count($this->fillable); $i++) {
                if ($i == 0) {
                    $query->where($this->fillable[$i], 'like', '%' . $data->search . '%');
                } else {
                    $query->orWhere($this->fillable[$i], 'like', '%' . $data->search . '%');
                }
            }
        }

        if ($data->sortBy || $data->orderBy) {
            if (empty($data->orderBy)) {
                $data->orderBy = 'asc';
            }
            $query->orderBy($data->sortBy, $data->orderBy);
        }
        return $query;
    }

    public function getUserCompleteAttribute()
    {
        $user_id = $this->user()->first()->id;
        return $this->courseCategory()->first()->getIsCompletedAttribute($user_id);
    }

    public function getUserProgressPercentageAttribute()
    {
        $user_id = $this->user_id;
        // dd($user_id, $this->courseCategory()->first()->getRatioCompletedAttribute($user_id), $this->courseCategory()->first()->toArray());
        return $this->courseCategory()->first()->getRatioCompletedAttribute($user_id) * 100;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id', 'id');
    }
}
