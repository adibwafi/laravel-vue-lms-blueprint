<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    public $incrementing = false;

    protected $appends = ['tutor'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public function scopeSearch($query, $data)
    {
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

    public function course_category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getTutorAttribute()
    {
        $course_category = $this->course_category()->first();
        return $course_category->tutor()->get();
    }
}
