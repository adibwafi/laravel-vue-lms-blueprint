<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class QuestionBank extends Model
{
    use HasFactory, Uuid;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];

    public function scopeSearch($query, $data)
    {
        if ($data->exam_id) {
            $query->where('exam_id', $data->exam_id);
        }

        function getExamQuery($data)
        {
            return function ($query) use ($data) {
                $query->select('id', 'name', 'course_id')->withoutAppends();
                if (filter_var($data->withCourse, FILTER_VALIDATE_BOOL)) {
                    $query->with(['course' => getCourseQuery($data)]);
                }
            };
        }

        function getCourseQuery($data)
        {
            return function ($query) use ($data) {
                $query->select('id', 'name', 'categories_id')->withoutAppends()->without('tutor');
                if (filter_var($data->withCourseCategory, FILTER_VALIDATE_BOOL)) {
                    $query->with(['courseCategory' => getCourseCategoryQuery()]);
                }
            };
        }

        function getCourseCategoryQuery()
        {
            return function ($query) {
                $query->select('id', 'name')->withoutAppends();
            };
        }

        if (filter_var($data->withExam, FILTER_VALIDATE_BOOL)) {
            $query->with(['exam' => getExamQuery($data)]);
        }

        if ($data->search) {
            for ($i = 0; $i < count($this->getTableColumns()); $i++) {
                if ($i == 0) {
                    $query->where($this->getTableColumns()[$i], 'like', '%' . $data->search . '%');
                } else {
                    $query->orWhere($this->getTableColumns()[$i], 'like', '%' . $data->search . '%');
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

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }

    public function examPage()
    {
        return $this->hasMany(ExamPage::class, 'question_bank_id', 'id');
    }

    public function examPoin()
    {
        return $this->hasMany(ExamPoin::class, 'question_bank_id', 'id');
    }
}
