<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonPageRating extends Model
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
        'lesson_pages_id',
        'users_id',
        'rating',
        'feedback',
    ];

    public function scopeSearch($query, $data)
    {
        $query->where('lesson_pages_id', $data->lesson_pages_id);
        if ($data->search) {
            array_push($this->fillable, "id");
            for ($i = 0; $i < count($this->fillable); $i++) {
                if ($i == 0) {
                    $query->orWhere($this->fillable[$i], 'like', '%'.$data->search.'%');
                } else {
                    $query->orWhere($this->fillable[$i], 'like', '%'.$data->search.'%');
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

    public function lessonPage()
    {
        return $this->belongsTo(LessonPage::class, 'lessons_page_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(Lesson::class, 'users_id', 'id');
    }
}
