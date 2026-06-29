<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Exception;

class ExamAnswer extends Model
{
    use HasFactory, Uuid;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];

    public function scopeSearch($query, $data)
    {
        if ($data->exam_id) {
            $query->whereHas('examPage', function ($query) use ($data) {
                $query->where('exam_id', $data->exam_id);
            });
        }

        if ($data->exam_page_id) {
            $query->where('exam_page_id', $data->exam_page_id);
        }

        if ($data->user_id) {
            $query->where('user_id', $data->user_id);
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
        } else {
            $query->orderBy('exam_page_id', 'asc');
        }
        return $query;
    }

    public function getCorrectAnswerAttribute()
    {
        $examPage = $this->examPage()->first();

        if ($this->data_answer) {
            try {
                $answer_data = json_decode($this->data_answer, true);
            } catch (Exception $e) {
                return null;
            }
        } else {
            return null;
        }

        if ($examPage->content_type == "multiple_choice") {
            $correct_answer = [];
            foreach ($answer_data as $answer) {
                if ($answer['checked'] == true) {
                    array_push($correct_answer, $answer['answer']);
                }
            }
            return $correct_answer;
        } else if ($examPage->content_type == "answer") {
            $correct_answer = $answer_data["correct_answer"];
            return $correct_answer;
        } else if ($examPage->content_type == "fill_blank") {
            $correct_answer = [];
            foreach ($answer_data as $answer) {
                array_push($correct_answer, sprintf(
                    "%s -> %s",
                    $answer['correct_answer_key'],
                    $answer['correct_answer']
                ));
            }
            return $correct_answer;
        } else {
            return null;
        }
    }

    public function getUserAnswerAttribute()
    {
        $examPage = $this->examPage()->first();

        if ($this->data_answer) {
            try {
                $answer_data = json_decode($this->data_answer, true);
            } catch (Exception $e) {
                return null;
            }
        } else {
            return null;
        }

        if ($examPage->content_type == "multiple_choice") {
            $user_answer = [];
            foreach ($answer_data as $answer) {
                if (filter_var($answer['selected'], FILTER_VALIDATE_BOOL)) {
                    array_push($user_answer, $answer['answer']);
                }
            }
            return  $user_answer;
        } else if ($examPage->content_type == "answer") {
            $user_answer = $answer_data["user_answer"];
            return [$user_answer];
        } else if ($examPage->content_type == "fill_blank") {
            $user_answer = [];
            foreach ($answer_data as $answer) {
                array_push($user_answer, sprintf(
                    "%s -> %s",
                    $answer['user_answer_key'],
                    $answer['user_answer']
                ));
            }
            return $user_answer;
        } else {
            return null;
        }
    }
    public function getIsCorrectAttribute()
    {
        $examPage = $this->examPage()->first();

        if ($this->data_answer) {
            try {
                $answer_data = json_decode($this->data_answer, true);
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }

        if ($examPage->content_type == "multiple_choice") {
            $correct = false;
            foreach ($answer_data as $answer) {
                if (filter_var($answer['selected'], FILTER_VALIDATE_BOOLEAN) && filter_var($answer['checked'], FILTER_VALIDATE_BOOLEAN)) {
                    $correct = true;
                    break;
                }
            }
            return $correct;
        } else if ($examPage->content_type == "answer") {
            $correct = false;
            if (in_array($answer_data["user_answer"], $answer_data["correct_answer"])) {
                $correct = true;
            }
            return $correct;
        } else if ($examPage->content_type == "fill_blank") {
            $correct = true;
            foreach ($answer_data as $answer) {
                if ($answer['correct_answer_key'] != $answer['user_answer_key'] || $answer['correct_answer'] != $answer['user_answer']) {
                    $correct = false;
                    break;
                }
            }
            return $correct;
        } else {
            return true;
        }
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }

    public function examPage()
    {
        return $this->belongsTo(ExamPage::class, 'exam_page_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
