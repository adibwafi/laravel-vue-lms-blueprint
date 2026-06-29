<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Dotenv\Parser\Lexer;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLesson extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];

    public function scopeSearch($query, $data)
    {
        if ($data->user_id) {
            $query->where('users_id', $data->user_id);
        }

        if ($data->lesson_id) {
            $query->where('lessons_id', $data->lesson_id);
        }

        if ($data->with_lesson) {
            $query->with('lesson');
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

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lessons_id', 'id');
    }
}
