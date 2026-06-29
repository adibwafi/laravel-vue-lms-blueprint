<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonRating extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lessons_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(Lesson::class, 'users_id', 'id');
    }
}
