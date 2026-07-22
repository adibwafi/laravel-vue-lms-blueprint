<?php

namespace App\Http\Controllers\API;

use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\BaseController;
use App\Http\Resources\PaginationResource;

class ExamAnswerController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAdmin(Request $request)
    {
        $data = new PaginationResource(ExamAnswer::search($request)->with('examPage')->with('user')->paginate($request->limit));

        $data->each(function ($d) {
            $d->append('correct_answer');
            $d->append('user_answer');
            $d->append('is_correct');
            $d->examPage->append('question');
        });

        $users = [];
        if ($request->exam_id) {
            $allExamAnswer = ExamAnswer::with('user')->whereHas('examPage', function ($query) use ($request) {
                $query->where('exam_id', $request->exam_id);
            })->get();

            $users = $allExamAnswer->map(function ($d) {
                return $d->user;
            });

            $users = $users->unique('id')->flatten()->toArray();
        }

        return $this->ressSuccess([
            'message' => 'Successfully get All Exam Answer',
            'data' => [
                'exam_answer' => $data,
                'user' => $users,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'exam_poin_id' => 'required',
            'exam_page_id' => 'required',
            'data_answer' => 'required',
        ]);

        try {
            $data = $user->examAnswer()->create($request->all());
            return $this->ressSuccess([
                'message' => 'Successfully create Exam Poin',
                'data' => [
                    'ExamAnswer' => $data,
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExamAnswer  $examAnswer
     * @return \Illuminate\Http\Response
     */
    public function show(ExamAnswer $examAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExamAnswer  $examAnswer
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamAnswer $examAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamAnswer  $examAnswer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExamAnswer $examAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExamAnswer  $examAnswer
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExamAnswer $examAnswer)
    {
        //
    }

    public function lastAnswer(Request $request, $exam_poin_id)
    {
        try {
            $user = $request->user();
            $data =  ExamAnswer::where([
                'user_id' => $user->id,
                'exam_poin_id' => $exam_poin_id,
            ])->get();

            return $this->ressSuccess([
                'message' => 'Successfully get last answer',
                'data' => [
                    'ExamAnswer' => $data,
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function getJournalReflection(Request $request, $lesson_id)
    {
        $query = DB::table('exam_answers as ea')
          ->selectRaw('ea.user_id, u.fullname, u.email')
          ->join('users as u', 'ea.user_id', '=', 'u.id')
          ->join('exam_pages as ep', 'ea.exam_page_id', '=', 'ep.id')
          ->join('exams as e', 'ep.exam_id', '=', 'e.id')
          ->whereRaw('e.lesson_id = ?', [$lesson_id]);

        if ($request->search) {
          $searchTerm = '%' . strtolower($request->search) . '%';
          $query->whereRaw('(LOWER(u.fullname) LIKE ? OR LOWER(u.email) LIKE ?)', [$searchTerm, $searchTerm]);
        }

        $data = new PaginationResource($query->groupBy('ea.user_id', 'u.fullname', 'u.email')
          ->orderByRaw('MAX(ea.created_at) desc')
          ->paginate($request->limit));

        $dataObj = json_decode(json_encode($data));
        $userIds = array_map(function($o) { return $o->user_id;}, $dataObj->rows);
        $answers = DB::table('exam_answers as ea')
          ->selectRaw('ea.*, ep.data as question')
          ->join('exam_pages as ep', 'ea.exam_page_id', '=', 'ep.id')
          ->join('exams as e', 'ep.exam_id', '=', 'e.id')
          ->whereRaw('e.lesson_id = ?', [$lesson_id])
          ->whereIn('ea.user_id', $userIds)
          ->get();
        $answersArr = json_decode(json_encode($answers));

        foreach ($dataObj->rows as $d) {
          $userId = $d->user_id;
          $filteredAnswers = array_filter($answersArr, function($o) use ($userId){
            return $o->user_id == $userId;
          });
          $d->answers = array_values($filteredAnswers);
        }

        return $this->ressSuccess([
            'message' => 'Successfully get All Exam Answer',
            'data' => [
                'exam_answer' => $dataObj,
            ]
        ]);
    }
}
