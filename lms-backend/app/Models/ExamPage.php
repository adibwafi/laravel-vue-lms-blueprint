<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Error;

class ExamPage extends Model
{
    use HasFactory, Uuid;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];

    public function scopeSearch($query, $data)
    {
        $query->where('exam_id', $data->exam_id);
        if ($data->question_bank_id) {
            $query->where('question_bank_id', $data->question_bank_id);
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

    public function getQuestionAttribute()
    {
        $content = json_decode($this->data, true)['content'];
        return $content;
    }

    public function getAllUserAnswerAttribute()
    {
        $total_answer = $this->examAnswer->count();
        if ($this->content_type == "multiple_choice") {
            $exam_answer = $this->examAnswer()->get();
            $choices = json_decode($this->data, true)["choices"];
            $answer_choices = [];
            foreach ($choices as $choice) {
                $answer_choices[$choice["answer"]] = 0;
            }

            foreach ($exam_answer as $answer) {
                $answer_data = json_decode($answer->data_answer, true);
                foreach ($answer_data as $data) {
                    if (filter_var($data['selected'], FILTER_VALIDATE_BOOLEAN)) {
                        $answer_choices[$data['answer']]++;
                        break;
                    }
                }
            }

            foreach ($answer_choices as $key => $value) {
                $percentage = 0;
                if ($total_answer == 0) {
                    $percentage = 0;
                } else {
                    $percentage  = round(($value / $total_answer) * 100);
                }
                $correct = false;
                foreach ($choices as $choice) {
                    if ($choice["answer"] === $key) {
                        $correct = filter_var($choice["checked"], FILTER_VALIDATE_BOOLEAN);
                    }
                }
                $answer_choices[$key] = [
                    'number_answer' => $value,
                    'percentage_answer' => $percentage,
                    'correct_answer' => $correct
                ];
            }

            return $answer_choices;
        } else if ($this->content_type == "answer") {
            $exam_answer = $this->examAnswer()->get()->toArray();
            $answers = array_map(function ($answer) {
                $answer_data = json_decode($answer['data_answer'], true);
                return $answer_data["user_answer"];
            }, $exam_answer);

            $answer_frequency = array_count_values($answers);

            $correctAnswerDataDirty =  explode(",",  json_decode($this->data, true)['keyword']);
            $correctAnswerData =  array_map(function ($item) {
                return trim($item);
            }, $correctAnswerDataDirty);

            $answer_frequency_data = [];

            foreach ($answer_frequency as $key => $freq) {
                $percentage = 0;
                if ($total_answer == 0) {
                    $percentage = 0;
                } else {
                    $percentage = round(($freq / $total_answer) * 100);
                }
                $answer_frequency_data[$key] =  [
                    'number_answer' => $freq,
                    'percentage_answer' => $percentage,
                    'correct_answer' => in_array($key, $correctAnswerData)
                ];
            }


            $answer_frequency_data = collect($answer_frequency_data)->sortBy('number_answer')->reverse()->slice(0, 10)->toArray();

            return $answer_frequency_data;
        } else if ($this->content_type == "fill_blank") {
            $exam_answer = $this->examAnswer()->get();
            $options = json_decode($this->data, true)["answer"];
            $answer_choices = [];
            foreach ($options as $option) {
                $answer_choices[$option["key"]] = 0;
            }

            foreach ($exam_answer as $answer) {
                $answer_data = json_decode($answer->data_answer, true);
                foreach ($answer_data as $data) {
                    if ($data["user_answer_key"] == $data["correct_answer_key"]) {
                        $answer_choices[$data["user_answer_key"]]++;
                    }
                }
            }

            foreach ($answer_choices as $key => $value) {
                $percentage = 0;
                if ($total_answer == 0) {
                    $percentage = 0;
                } else {
                    $percentage  = round(($value / $total_answer) * 100);
                }
                $correctAnswer = '';
                foreach ($options as $option) {
                    if ($option['key'] === $key) {
                        $correctAnswer = $option['value'];
                    }
                }

                $answer_choices[$key] = [
                    'correct_answer' => $correctAnswer,
                    'number_correct' => $value,
                    'percentage_correct' => $percentage
                ];
            }

            return $answer_choices;
        } else {
            return [];
        }
    }

    public function getPercentageCorrectAnswerAttribute()
    {
        $exam_answers = $this->examAnswer()->get();
        $total_answer = $this->examAnswer()->count();
        $correct_answer = 0;
        if ($total_answer == 0) {
            return 0;
        }

        foreach ($exam_answers as $exam_answer) {
            if ($exam_answer->getIsCorrectAttribute()) {
                $correct_answer++;
            }
        }

        return round(($correct_answer / $total_answer) * 100);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function examAnswer()
    {
        return $this->hasMany(ExamAnswer::class, 'exam_page_id');
    }
}
