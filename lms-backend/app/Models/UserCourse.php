<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCourse extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'courses_id',
        'users_id',
        'completed_at',
    ];

    protected $appends = [
        'user_complete',
        'user_progress_percentage'
    ];

    public function scopeSearch($query, $data)
    {
        if ($data->user_id) {
            $query->where('users_id', $data->user_id);
        }

        if ($data->course_id) {
            $query->where('courses_id', $data->course_id);
        }

        if ($data->with_course) {
            $query->with('course');
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
        if (!$this->course()->first()) {
            return null;
        }

        $user_id = $this->user()->first()->id;
        return $this->course()->first()->getIsCompletedAttribute($user_id);
    }

    public function getUserProgressPercentageAttribute()
    {
        if (!$this->course()->first()) {
            return null;
        }

        $user_id = $this->user()->first()->id;
        return $this->course()->first()->getRatioCompletedAttribute($user_id) * 100;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'courses_id', 'id');
    }
}
