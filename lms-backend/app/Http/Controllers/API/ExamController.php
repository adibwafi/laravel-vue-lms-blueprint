<?php

namespace App\Http\Controllers\API;

use App\Models\Exam;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PaginationResource;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\ExamRequest;
use App\Models\ExamPage;

class ExamController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = new PaginationResource(Exam::search($request)->with('examPoin')->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get Exam',
            'data' => [
                'exam' => $data,
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
    public function store(ExamRequest $request)
    {
        $request->validated();

        try {
            $data = Exam::create($request->all());
            return $this->ressSuccess([
                'message' => 'Successfully create Exam',
                'data' => [
                    'Exam' => $data,
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
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $exam = Exam::with(['course', 'lesson'])->find($id);
        if (empty($exam)) {
            return $this->ressError([
                'message' => 'Exam not found!',
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

    public function showUser(Request $request, $id)
    {
        $user = $request->user();
        $exam = Exam::with('examPoinUser')->find($id);
        if (empty($exam)) {
            return $this->ressError([
                'message' => 'Exam not found!',
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
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function edit(Exam $exam)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function update(ExamRequest $request, $id)
    {
        $exam = Exam::find($id);
        $request->validated();


        if (empty($exam)) {
            return $this->ressError([
                'message' => 'Data exam cannot find!',
                'data' => []
            ]);
        }

        try {
            $exam->update($request->all());
            return $this->ressSuccess([
                'message' => 'Successfully update Exam',
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
     * Import Exam Page from Another Exam
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $input = $request->toArray();
        $import_exam_id = $input['import_exam_id'];
        if (empty($import_exam_id)) {
            return $this->ressError([
                'message' => 'Imported Exam ID is required!',
                'data' => []
            ]);
        }

        $current_exam_id = $input['current_exam_id'];
        if (empty($current_exam_id)) {
            return $this->ressError([
                'message' => 'Current Exam ID is required!',
                'data' => []
            ]);
        }

        if ($import_exam_id == $current_exam_id) {
            return $this->ressError([
                'message' => 'Cannot import from same exam!',
                'data' => []
            ]);
        }


        $currentExam = Exam::where('id', $current_exam_id)->first();
        if (empty($currentExam)) {
            return $this->ressError([
                'message' => 'Current Exam not found!',
                'data' => []
            ]);
        }

        $importExam = Exam::where('id', $import_exam_id)->with('examPage')->first();
        if (empty($importExam)) {
            return $this->ressError([
                'message' => 'Import Exam not found!',
                'data' => []
            ]);
        }

        try {
            $examPages = $importExam->examPage()->orderBy('sorter')->get();
            $newExamPages = $examPages->map(function ($examPage) {
                $newExamPage = $examPage->replicate();
                return $newExamPage;
            });

            $Query = ExamPage::where('exam_id', $currentExam->id);
            $ExamPage = $Query->latest('sorter')->first();
            if ($ExamPage) {
                $sorter = intval($ExamPage->sorter) + 1;
            } else {
                $sorter = 1;
            }


            $newExamPages->each(function ($examPage) use ($currentExam, &$sorter) {
                $examPage->exam_id = $currentExam->id;
                $examPage->sorter = $sorter;
                $examPage->save();

                $sorter++;
            });

            return $this->ressSuccess([
                'message' => 'Successfully import Question Bank',
                'data' => [
                    'CurrentExam' => $currentExam,
                    'ImportExam' => $importExam,
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
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $exam = Exam::find($id);
        if (empty($exam)) {
            return $this->ressError([
                'message' => 'Cannot find data',
                'data' => []
            ]);
        }

        if ($exam->examPage->isNotEmpty()) {
            return $this->ressSuccess([
                'message' => 'Cannot delete Exam because have relationship with Exam Page',
                'data' => []
            ]);
        }
        if ($exam->examPoin->isNotEmpty()) {
            return $this->ressSuccess([
                'message' => 'Cannot delete Exam because have relationship with Exam Poin',
                'data' => []
            ]);
        }

        try {
            $exam->delete();
            return $this->ressSuccess([
                'message' => 'Successfully delete Exam',
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
