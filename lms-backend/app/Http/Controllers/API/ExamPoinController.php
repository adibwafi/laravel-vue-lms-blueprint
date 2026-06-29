<?php

namespace App\Http\Controllers\API;

use App\Models\ExamPoin;
use App\Models\ExamConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\BaseController;
use App\Http\Resources\PaginationResource;
use App\Models\Exam;
use Illuminate\Support\Carbon;

class ExamPoinController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ExamPoin = new PaginationResource(ExamPoin::search($request)->paginate($request->limit));
        if ($request->groupBy === 'user_id') {
            $ExamPoin->each(function ($ep) {
                $ep->append('user_poin');
                $ep->append('highest_score');
            });
        }
        return $this->ressSuccess([
            'message' => 'Successfully get Exam Poin',
            'data' => [
                'ExamPoin' => $ExamPoin,
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

    private function randomWithProbability(array $set, $length = 10000)
    {
        $left = 0;
        foreach ($set as $num => $right) {
            $set[$num] = $left + $right * $length;
            $left = $set[$num];
        }
        $test = mt_rand(1, $length);
        $left = 1;
        foreach ($set as $num => $right) {
            if ($test >= $left && $test <= $right) {
                return $num;
            }
            $left = $right;
        }
        return null; //debug, no event realized
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
        $exConfig = ExamConfig::first();
        $request->validate([
            'exam_id' => 'required',
            'poin' => 'required',
        ]);

        $exam = Exam::where(['id' => $request->exam_id])->first();
        if (empty($exam)) {
            return $this->ressError([
                'message' => 'Exam not found!',
                'data' => []
            ]);
        }

        $random_question_bank = null;
        if ($exam->use_question_bank) {
            // requirement: The user must picked the same question bank if the user has done exam with question bank before 
            // pick question bank with the same name as the user has done before
            $question_bank = $exam->questionBank()->get()->toArray();

            $user_exam_poin_with_question_bank = ExamPoin::with('questionBank')->where(['user_id' => $user->id])->whereNotNull('question_bank_id')->get();
            $user_question_bank_name = $user_exam_poin_with_question_bank->pluck('questionBank.name')->toArray();
            // Trim and lowercase the question bank name
            $user_question_bank_name = array_map(function ($name) {
                return strtolower(trim($name));
            }, $user_question_bank_name);

            $question_bank_same_name = array_values(array_filter($question_bank, function ($question_bank) use ($user_question_bank_name) {
                $question_bank_name_trimmed = strtolower(trim($question_bank['name']));
                return in_array($question_bank_name_trimmed, $user_question_bank_name);
            }));
            if (!empty($question_bank_same_name)) {
                $random_question_bank = $question_bank_same_name[0];
            }
            // dd($user_question_bank_name, $user_question_bank_name, $question_bank_same_name, $random_question_bank);

            // If the user has not done exam with question bank before, pick question bank with the least picked frequency
            if ($random_question_bank == null) {
                // Random and prioritize unpick question bank
                $sum = 0;
                $probabilities_picked = [];
                // Calculate frequency of each question bank
                foreach ($question_bank as $key => $value) {
                    $picked = ExamPoin::where(['question_bank_id' => $value['id']])->count();
                    // Max(1, $picked) is to prevent division by zero
                    $question_bank[$key]['picked'] = max(1, $picked);
                    $sum += $question_bank[$key]['picked'];
                }
                // Sort by frequency
                array_multisort(array_column($question_bank, 'picked'), SORT_ASC, $question_bank);

                // Get ratio of each question bank
                foreach ($question_bank as $key => $value) {
                    $question_bank[$key]['pie'] = $question_bank[$key]['picked'] / $sum;
                    $probabilities_picked[] = $question_bank[$key]['pie'];
                }
                // Reverse the probability, so the least picked question bank will have the highest probability
                $probabilities_picked = array_reverse($probabilities_picked);

                $random_index = $this->randomWithProbability($probabilities_picked);
                $random_question_bank = $question_bank[$random_index];
            }
        }

        try {
            $data = $user->examPoin()->create([
                'exam_id' => $request->exam_id,
                'poin' => $request->poin,
                'time_finish' => Carbon::now()->addMinutes($exConfig->time_exam),
                'question_bank_id' => $random_question_bank ? $random_question_bank["id"] : null,
            ]);
            return $this->ressSuccess([
                'message' => 'Successfully create Exam Poin',
                'data' => [
                    'ExamPoin' => $data,
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
     * @param  \App\Models\ExamPoin  $examPoin
     * @return \Illuminate\Http\Response
     */
    public function showSpecific($exam_poin_id)
    {
        $uid = request()->user()->id;
        $exam = ExamPoin::where(['id' => $exam_poin_id, 'user_id' => $uid])->orderBy('created_at', 'DESC')->first();
        if (empty($exam)) {
            return $this->ressError([
                'message' => 'Exam Point not found!',
                'data' => []
            ]);
        }
        try {
            return $this->ressSuccess([
                'message' => 'Show Exam',
                'data' => [
                    'exam' => $exam,
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
     * @param  \App\Models\ExamPoin  $examPoin
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $uid = request()->user()->id;
        $exam = ExamPoin::where(['exam_id' => $id, 'user_id' => $uid])->orderBy('created_at', 'DESC')->first();
        if (empty($exam)) {
            return $this->ressError([
                'message' => 'Exam Point not found!',
                'data' => []
            ]);
        }
        try {
            return $this->ressSuccess([
                'message' => 'Show Exam',
                'data' => [
                    'exam' => $exam,
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExamPoin  $examPoin
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamPoin $examPoin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamPoin  $examPoin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $exampoin = ExamPoin::find($id);

        $request->validate([
            'poin' => 'required|numeric|between:0,100',
            'finished_at' => 'nullable',
        ]);

        if (empty($exampoin)) {
            return $this->ressError([
                'message' => 'Data exam cannot find!',
                'data' => []
            ]);
        }

        try {
            $exampoin->update($request->all());
            return $this->ressSuccess([
                'message' => 'Successfully update Exam',
                'data' => [
                    'ExamPoin' => $exampoin,
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExamPoin  $examPoin
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExamPoin $examPoin)
    {
        //
    }
}
