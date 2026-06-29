<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class LessonPage extends Model
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
        'lessons_id',
        'name',
        'content_type',
        'sorter',
        'data',
        'media_type',
        'media_link',
        'tooltips',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'tooltips' => 'array',
    ];

    protected $search = [
        'name'
    ];

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function scopeSearch($query, $data)
    {
        $query->where('lessons_id', $data->lessons_id);
        if ($data->search) {
            for ($i = 0; $i < count($this->search); $i++) {
                if($this->search[$i] !=  'lessons_id'){
                    if ($i == 0) {
                        $query->Where($this->search[$i], 'like', '%' . $data->search . '%');
                    } else {
                        $query->orWhere($this->search[$i], 'like', '%' . $data->search . '%');
                    }
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

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lessons_id', 'id');
    }

    public function lessonPageRating()
    {
        return $this->hasMany(LessonPageRating::class, 'lesson_pages_id');
    }
}
