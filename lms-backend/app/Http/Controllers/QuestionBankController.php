<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PaginationResource;
use App\Http\Controllers\API\BaseController;
use App\Models\ExamPage;

class QuestionBankController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $questionBank = new PaginationResource(QuestionBank::search($request)->paginate($request->limit));

        return $this->ressSuccess([
            'message' => 'Successfully get Question Bank',
            'data' => [
                'QuestionBanks' => $questionBank,
            ]
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->toArray();
        try {
            $questionBank = QuestionBank::create($input);
            return $this->ressSuccess([
                'message' => 'Successfully create Question Bank',
                'data' => [
                    'QuestionBank' => $questionBank,
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
     * Import Question Bank
     *
     * @param  \App\Models\QuestionBank  $questionBank
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $input = $request->toArray();

        $questionBank = QuestionBank::where('id', $input['question_bank_id'])->first();
        if (empty($questionBank)) {
            return $this->ressError([
                'message' => 'Question Bank not found!',
                'data' => []
            ]);
        }

        try {
            $newQuestionBank = $questionBank->replicate();
            $newQuestionBank->exam_id = $input['exam_id'];
            $newQuestionBank->save();

            $examPages = $questionBank->examPage()->get();
            $newExamPages = $examPages->map(function ($examPage) {
                $newExamPage = $examPage->replicate();
                return $newExamPage;
            });
            $newExamPages->each(function ($examPage) use ($newQuestionBank) {
                $examPage->question_bank_id = $newQuestionBank->id;
                $examPage->exam_id = $newQuestionBank->exam_id;
                $examPage->save();
            });

            return $this->ressSuccess([
                'message' => 'Successfully import Question Bank',
                'data' => [
                    'QuestionBank' => $newQuestionBank,
                    'ExamPage' => $newExamPages,
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
     * @param  \App\Models\QuestionBank  $questionBank
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $QuestionBank = QuestionBank::with(['exam'])->find($id);

        if (empty($QuestionBank)) {
            return $this->ressError([
                'message' => 'Question Bank not found!',
                'data' => []
            ]);
        }
        try {
            return $this->ressSuccess([
                'message' => 'Show Question Bank',
                'data' => [
                    'QuestionBank' => $QuestionBank,
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuestionBank  $questionBank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->toArray();
        $QuestionBank = QuestionBank::find($id);
        if (empty($QuestionBank)) {
            return $this->ressError([
                'message' => 'Cannot find Question Bank data!',
                'data' => []
            ]);
        }

        try {
            $QuestionBankUpdate = QuestionBank::updateOrCreate(['id' => $QuestionBank->id], $input);
            return $this->ressSuccess([
                'message' => 'Successfully update Question Bank',
                'data' => [
                    'QuestionBank' => $QuestionBankUpdate,
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
     * @param  \App\Models\QuestionBank  $questionBank
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $QuestionBank = QuestionBank::find($id);
        if (empty($QuestionBank)) {
            return $this->ressError([
                'message' => 'Cannot find data',
                'data' => []
            ]);
        }
        try {
            $QuestionBank->examPage()->delete();
            $QuestionBank->delete();
            return $this->ressSuccess([
                'message' => 'Successfully delete Question Bank',
                'data' => []
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
}
