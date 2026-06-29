<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Exam extends Model
{
    use HasFactory, Uuid;

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $guarded = [];

    protected $appends = ['total_exam', 'highest_grade', 'attempt_left', 'pass_kkm', 'exam_done', 'can_go_to_exam', 'tracking'];

    protected static $withoutAppends = false;

    public function scopeWithoutAppends($query)
    {
        self::$withoutAppends = true;

        return $query;
    }

    private static function addCourseQuery($query, $data)
    {
        $query->with(['course' => function ($query) use ($data) {
            $query->select('id', 'name', 'categories_id')->withoutAppends()->without('tutor');
            if ($data->with_course_category) {
                $query->with(['courseCategory' => function ($query) {
                    $query->select('id', 'name')->withoutAppends();
                }]);
            }
        }]);
    }

    public function scopeSearch($query, $data)
    {
        if ($data->course_id) {
            $query->where('course_id', $data->course_id);
        }

        if ($data->with_lesson) {
            $query->with(['lesson' => function ($query) use ($data) {
                $query->select('id', 'name', 'courses_id')->without('userLesson')->with('course')->withoutAppends();
                if ($data->with_course) {
                    self::addCourseQuery($query, $data);
                }
            }]);
        }

        if ($data->with_course) {
            self::addCourseQuery($query, $data);
        }


        if ($data->search) {
            array_push($this->fillable, "id");

            $searchableColumns = ['name']; // Kolom-kolom yang ingin dicari

            for ($i = 0; $i < count($searchableColumns); $i++) {
                if ($i == 0) {
                    $query->where($searchableColumns[$i], 'like', '%' . $data->search . '%');
                } else {
                    $query->orWhere($searchableColumns[$i], 'like', '%' . $data->search . '%');
                }
            }

            $query->orWhereHas('lesson', function ($q) use ($data) {
                $q->whereHas('course', function ($qc) use ($data) {
                    $qc->where('name', 'like', "%{$data->search}%");
                });
            });
        }
        // error_log("Query: " . json_encode($query->toSql()));
        if ($data->sortBy || $data->orderBy) {
            if (empty($data->orderBy)) {
                $data->orderBy = 'asc';
            }
            $query->orderBy($data->sortBy, $data->orderBy);
        }
        return $query;
    }

    // public function getAveragePoin()
    // {
    //     $dividend = $this->allExamPoin()->sum('poin');
    //     $divisor = $this->allExamPoin()->count();
    //     if ($divisor == 0) {
    //         return 0;
    //     }
    //     return $dividend / $divisor;
    // }

    public function getAverageScore()
    {
        // $dividend = $this->getAveragePoin();
        // $divisor = $this->examPage()->count();

        $dividend = 0;
        $exam_poin = $this->allExamPoin()->get();
        foreach ($exam_poin as $key => $value) {
            if ($value->total_exam == 0) {
                continue;
            }
            $poin = intval($value->poin) / intval($value->total_exam) * 100;
            $dividend += $poin;
        }

        $divisor = $this->allExamPoin()->count();
        if ($divisor == 0) {
            return 0;
        }
        return $dividend / $divisor;
    }

    // public function getAverageHighestPoin()
    // {
    //     $result = floatval(DB::table(function ($query) {
    //         $query->select(DB::raw("MAX(poin) as max_poin"))->from('exam_poins')->groupBy('user_id')->where('exam_id', $this->id);
    //     }, 'exam_poins')->avg('max_poin'));
    //     return $result;
    // }


    public function getAverageHighestScore()
    {
        // $dividend = $this->getAverageHighestPoin();
        // $divisor = $this->examPage()->count();
        $examPoin = $this->allExamPoin()->get();
        $examPoinUserUnique = [];
        foreach ($examPoin as $key => $value) {
            if ($value->total_exam == 0) {
                continue;
            }

            $score = intval($value->poin) / intval($value->total_exam) * 100;
            if (!array_key_exists($value->user_id, $examPoinUserUnique)) {
                $examPoinUserUnique[$value->user_id] = $value;
            } else {
                $examPoinOld = $examPoinUserUnique[$value->user_id];
                if ($examPoinOld->total_exam == 0) {
                    continue;
                }

                $oldScore = $examPoinOld->poin / $examPoinOld->total_exam * 100;
                if ($oldScore < $score) {
                    $examPoinUserUnique[$value->user_id] = $value;
                }
            }
        }

        $dividend = 0;
        foreach ($examPoinUserUnique as $key => $value) {
            if ($value->total_exam == 0) {
                continue;
            }
            $dividend += $value->poin / $value->total_exam * 100;
        }
        $divisor = count($examPoinUserUnique);

        if ($divisor == 0) {
            return 0;
        }
        return $dividend / $divisor;
    }

    public function getTrackingAttribute()
    {
        $number_user_completed = $this->allExamPoin()->distinct('user_id')->count('user_id');
        $average_score = $this->getAverageScore();
        $average_highest_score = $this->getAverageHighestScore();

        return [
            'number_user_completed' => $number_user_completed,
            'average_score' => $average_score,
            'average_highest_score' => $average_highest_score
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id')->where(function ($query) {
            $query->where('status', 'released')->orWhere('status', 'disabled');
        })->with('tutor');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function examPage()
    {
        return $this->hasMany(ExamPage::class, 'exam_id');
    }

    public function getTotalExamAttribute()
    {
        if (self::$withoutAppends) {
            return null;
        }
        return $this->examPage()->count();
    }

    public function getCanGoToExamAttribute()
    {
        if (self::$withoutAppends) {
            return null;
        }
        $attemptLeft = $this->getAttemptLeftAttribute();

        return $attemptLeft > 0 || $this->max_attempt == null;
    }

    public function getPassKkmAttribute()
    {
        if (self::$withoutAppends) {
            return null;
        }
        $highest_grade = $this->getHighestGradeAttribute();
        if ($highest_grade === null) {
            return null;
        }

        if ($this->kkm === null) {
            return true;
        }

        return $highest_grade >= $this->kkm;
    }


    public function getExamDoneAttribute()
    {
        if (self::$withoutAppends) {
            return null;
        }
        $examPoints = $this->examPoin();
        if ($examPoints->count() > 0) {
            if ($examPoints->first()->finished_at) {
                if ($this->getPassKkmAttribute()  === true || $this->getAttemptLeftAttribute() === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getHighestGradeAttribute()
    {
        if (self::$withoutAppends) {
            return null;
        }
        $examPoints = $this->examPoin();
        if ($examPoints->count() == 0) {
            return null;
        }
        $max = 0;
        $examPointsArray = $examPoints->get();
        foreach ($examPointsArray as $key => $value) {
            if ($value->total_exam == 0) {
                return 0;
            }

            $poin = intval($value->poin) / intval($value->total_exam) * 100;
            if ($poin > 100) {
                $poin = 100;
            }
            if ($poin > $max) {
                $max = $poin;
            }
        }
        return $max;
    }

    public function getAttemptLeftAttribute()
    {
        if (self::$withoutAppends) {
            return null;
        }
        if ($this->max_attempt == null) {
            return null;
        }
        $examPoints = $this->examPoin()->where('finished_at', '!=', null);
        $attempt = $examPoints->count();
        $attemptLeft = $this->max_attempt - $attempt;
        $attemptLeft = max($attemptLeft, 0);
        return $attemptLeft;
    }

    public function allExamPoin()
    {
        return $this->hasMany(ExamPoin::class, 'exam_id');
    }

    public function questionBank()
    {
        return $this->hasMany(QuestionBank::class, 'exam_id');
    }

    public function examPoin($userID = null)
    {
        if ($userID == null) {
            $userID = request()->user()->id;
        }
        return $this->hasMany(ExamPoin::class, 'exam_id')->where('user_id', $userID);
    }

    public function examPoinUser($user = null)
    {
        if ($user == null) {
            $user = request()->user();
        }
        $user = request()->user();
        return $this->hasMany(ExamPoin::class, 'exam_id')->where('user_id', $user->id);
    }
}
