<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Support\Facades\DB;

class ExamPoin extends Model
{
    use HasFactory, Uuid;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $appends = ['is_completed', 'total_exam', 'score'];

    public function getUserPoinAttribute()
    {
        return ExamPoin::where('user_id', $this->user_id)->where('exam_id', $this->exam_id)->get();
    }

    public function getHighestScoreAttribute()
    {
        $exam_page_count = $this->total_exam;
        if ($exam_page_count == 0) {
            return 0;
        }

        return $this->max_poin / $exam_page_count * 100;
    }

    public function getScoreAttribute()
    {
        $exam_page_count = $this->total_exam;
        if ($exam_page_count == 0) {
            return 0;
        }

        $score = 0;

        if ($this->average_poin) {
            $score = $this->average_poin / $exam_page_count * 100;
        } else {
            $score = $this->poin / $exam_page_count * 100;
        }

        // Membatasi nilai maksimal score tidak lebih dari 100
        if ($score > 100) {
            $score = 100;
        }

        return $score;
    }


    public function scopeSearch($query, $data)
    {
        if ($data->groupBy) {
            $query->groupBy($data->groupBy);
            if ($data->groupBy == 'user_id') {
                $query->select('user_id', DB::raw('avg(poin) as average_poin'), DB::raw('count(user_id) as number_of_attempt'), DB::raw('MAX(poin) as max_poin'));
            }
        }

        if ($data->exam_id) {
            $query->where('exam_id', $data->exam_id)->groupBy('exam_id')->addSelect('exam_id');
        }

        if ($data->with_user) {
            $query->with('user');
        }

        if ($data->with_exam) {
            $query->with('exam');
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

        // if ($data->sortBy || $data->orderBy) {
        //     if (empty($data->orderBy)) {
        //         $data->orderBy = 'asc';
        //     }
        //     $query->orderBy($data->sortBy, $data->orderBy);
        // }
        return $query;
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getIsCompletedAttribute()
    {
        return $this->finished_at ? 'true' : 'false';
    }

    public function examPage()
    {
        $exam = $this->exam()->first();
        if ($exam->use_question_bank && $this->question_bank_id) {
            return $this->hasMany(ExamPage::class, 'exam_id', 'exam_id')->where('question_bank_id', $this->question_bank_id)->get();
        }

        return $this->hasMany(ExamPage::class, 'exam_id', 'exam_id');
    }

    public function getTotalExamAttribute()
    {
        return $this->examPage()->count();
    }

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class, 'question_bank_id');
    }
}
