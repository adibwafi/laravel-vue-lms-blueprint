<?php

namespace App\Http\Controllers\API;

use App\Models\Exam;
use App\Models\ExamPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ExamPageRequest;
use App\Http\Resources\PaginationResource;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExamPageController extends BaseController
{
    private function convertStringToNumber(string $string)
    {
        $sum = 0;
        $arr1 = str_split($string);
        foreach ($arr1 as $item) {
            $sum += ord($item);
        }
        return $sum;
    }

    private function fisherYatesShuffle(&$items, int $seed)
    {
        @mt_srand($seed);
        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = @mt_rand(0, $i);
            $tmp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $tmp;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = new PaginationResource(ExamPage::search($request)->paginate($request->limit));
        $data = $data->toArray($request);
        if ($request->exam_id) {
            $exam = Exam::find($request->exam_id);
            if (empty($exam)) {
                return $this->ressError([
                    'message' => 'Exam not found',
                    'data' => []
                ]);
            }


            if ($exam->randomize) {
                $random_seed = $request->random_seed;
                if (!$random_seed) {
                    $random_seed = Str::random(64);
                }
                $this->fisherYatesShuffle($data["rows"], $this->convertStringToNumber($random_seed));
            }
        }
        return $this->ressSuccess([
            'message' => 'Successfully get Exam Page',
            'data' => [
                'exam' => $data,
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAdmin(Request $request)
    {
        $data = new PaginationResource(ExamPage::search($request)->paginate($request->limit));

        if (filter_var($request->withTracking, FILTER_VALIDATE_BOOLEAN)) {
            $data->each(function ($d) {
                $d->append('all_user_answer');
                $d->append('percentage_correct_answer');
                $d->makeHidden('examAnswer');
            });
        }

        return $this->ressSuccess([
            'message' => 'Successfully get Exam Page',
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
    public function store(ExamPageRequest $request)
    {
        $request->validated();
        $input = $request->toArray();

        if ($request->media_type == 'image') {
            if ($request->file('media_link')->isValid()) {
                $input['media_link'] = $request->media_link->store('images/exam-page', 's3');
            }
        }

        $Query = ExamPage::where('exam_id', $input['exam_id']);
        if (array_key_exists('question_bank_id', $input)) {
            $Query->where('question_bank_id', $input['question_bank_id']);
        }

        $ExamPage = $Query->latest('sorter')->first();
        if ($ExamPage) {
            $sorter = intval($ExamPage->sorter) + 1;
        } else {
            $sorter = 1;
        }
        $input['sorter'] = $sorter;
        try {

            $ExamPage = ExamPage::create($input);

            $ExamPage['media_link'] = $ExamPage->media_type == 'image'  ? Storage::disk('s3')->url($ExamPage->media_link) : $ExamPage->media_link;
            return $this->ressSuccess([
                'message' => 'Successfully create Exam Page',
                'data' => [
                    'ExamPage' => $ExamPage,
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
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
     * @param  \App\Models\ExamPage  $examPage
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ExamPage = ExamPage::find($id);
        if (empty($ExamPage)) {
            return $this->ressError([
                'message' => 'Exam Page not found!',
                'data' => []
            ]);
        }
        try {
            $ExamPage['media_link'] = $ExamPage->media_type == 'image'  ?
                $ExamPage->media_link : $ExamPage->media_link;
            return $this->ressSuccess([
                'message' => 'Show Exam Page',
                'data' => [
                    'ExamPage' => $ExamPage,
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
     * @param  \App\Models\ExamPage  $examPage
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamPage $examPage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamPage  $examPage
     * @return \Illuminate\Http\Response
     */
    public function update(ExamPageRequest $request, $id)
    {
        $request->validated();
        $input = $request->toArray();
        $ExamPage = ExamPage::find($id);
        if (empty($ExamPage)) {
            return $this->ressError([
                'message' => 'Data Exam Page cannot find!',
                'data' => []
            ]);
        }

        if ($request->media_type == 'image') {
            if ($request->file('media_link') != null && $request->file('media_link')->isValid()) {
                $input['media_link'] = $request->media_link->store('images/lesson-page', 's3');
            } else {
                $input['media_link'] = $ExamPage->media_link;
            }
        }

        try {
            $ExamPageUpdate = ExamPage::updateOrCreate(['id' => $ExamPage->id], $input);
            $ExamPageUpdate['media_link'] = $ExamPageUpdate->media_type == 'image'  ?
                $ExamPageUpdate->media_link : $ExamPageUpdate->media_link;

            return $this->ressSuccess([
                'message' => 'Successfully update Lesson Page',
                'data' => [
                    'ExamPage' => $ExamPageUpdate,
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
     * @param  \App\Models\ExamPage  $examPage
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ExamPage = ExamPage::find($id);
        if (empty($ExamPage)) {
            return $this->ressError([
                'message' => 'Cannot find data',
                'data' => []
            ]);
        }
        try {

            $oldSort = $ExamPage->sorter;
            $ExamPage->update(['sorter' => 0]);
            ExamPage::where('exam_id', $ExamPage->exam_id)->where('sorter', '>', $oldSort)->decrement('sorter', 1);
            $ExamPage->delete();

            return $this->ressSuccess([
                'message' => 'Successfully delete Lesson Page',
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
