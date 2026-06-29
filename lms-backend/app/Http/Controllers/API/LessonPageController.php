<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\LessonPage;
use Illuminate\Http\Request;
use App\Http\Requests\LessonPageRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PaginationResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LessonPageController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $LessonPages = new PaginationResource(LessonPage::search($request)->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get Lesson Page',
            'data' => [
                'LessonPages' => $LessonPages,
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
     * @param  \Illuminate\Http\LessonPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LessonPageRequest $request)
    {
        $request->validated();
        $input = $request->toArray();

        if ($request->media_type == 'image') {
            if ($request->file('media_link')->isValid()) {
                $input['media_link'] = $request->media_link->store('images/lesson-page', 's3');
            }
        }

        if ($request->media_type == 'pdf') {
            if ($request->file('media_link')->isValid()) {
                $input['media_link'] = $request->media_link->store('document/lesson-page', 's3');
            } else {
                return $this->ressError([
                    'message' => 'File not valid',
                    'data' => []
                ]);
            }
        }

        $LessonPage = LessonPage::where('lessons_id', $input['lessons_id'])->latest('sorter')->first();
        if ($LessonPage) {
            $sorter = intval($LessonPage->sorter) + 1;
        } else {
            $sorter = 1;
        }
        $input['sorter'] = $sorter;
        try {
            DB::beginTransaction();
            $LessonPage = LessonPage::create($input);
            // $LessonPage['media_link'] = Storage::disk('s3')->url($LessonPage->media_link);
            $lesson = $LessonPage->lesson;
            $data = json_decode($LessonPage->data);
            switch ($LessonPage->media_type) {
                case 'image':
                    $lesson->learn_time = intval($lesson->learn_time) + strlen($data->content) + 2;
                    break;
                case 'video':
                    $lesson->learn_time = intval($lesson->learn_time) + strlen($data->content) + (5 * 60);
                    break;
                case 'pdf':
                    $lesson->learn_time = intval($lesson->learn_time) + strlen($data->content) + (10 * 60);
                    break;
                default:
                    $lesson->learn_time = intval($lesson->learn_time) + strlen($data->content);
                    break;
            }
            $lesson->save();
            DB::commit();
            // if ($LessonPage->media_type == 'image' || $LessonPage->media_type == 'pdf') {
            //     $LessonPage['media_link'] = Storage::disk('s3')->url($LessonPage->media_link);
            // } else {
            //     $LessonPage['media_link'] = $LessonPage->media_link;
            // }
            // $LessonPage['media_link'] = $LessonPage->media_type == 'image' ? Storage::disk('s3')->url($LessonPage->media_link) : $LessonPage->media_link;
            return $this->ressSuccess([
                'message' => 'Successfully create Lesson Page',
                'data' => [
                    'LessonPage' => $LessonPage,
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
     * @param  \App\Models\LessonPage  $LessonPage
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $LessonPage = LessonPage::find($id);
        if (empty($LessonPage)) {
            return $this->ressError([
                'message' => 'Lesson Page not found!',
                'data' => []
            ]);
        }
        try {
            $LessonPage['media_link'] = $LessonPage->media_type == 'image' || $LessonPage->media_type == 'pdf'  ?
                // Storage::disk('s3')->url(
                $LessonPage->media_link
                // ) 
                : $LessonPage->media_link;
            return $this->ressSuccess([
                'message' => 'Show Lesson Page',
                'data' => [
                    'LessonPage' => $LessonPage,
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
     * @param  \App\Models\LessonPage  $LessonPage
     * @return \Illuminate\Http\Response
     */
    public function edit(LessonPage $LessonPage)
    {
        try {
            return $this->ressSuccess([
                'message' => 'Edit Lesson Page',
                'data' => [
                    'LessonPage' => $LessonPage,
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
     * @param  \App\Models\LessonPage  $LessonPage
     * @return \Illuminate\Http\Response
     */
    public function update(LessonPageRequest $request, $id)
    {
        $request->validated();
        $input = $request->toArray();
        $LessonPage = LessonPage::find($id);
        if (empty($LessonPage)) {
            return $this->ressError([
                'message' => 'Data Lesson Page cannot find!',
                'data' => []
            ]);
        }

        if ($request->media_type == 'image') {
            if ($request->file('media_link') != null && $request->file('media_link')->isValid()) {
                $input['media_link'] = $request->media_link->store('images/lesson-page', 's3');
            } else {
                $input['media_link'] = $LessonPage->media_link;
            }
        }

        if ($request->media_type == 'pdf') {
            if ($request->file('media_link') != null && $request->file('media_link')->isValid()) {
                $input['media_link'] = $request->media_link->store('document/lesson-page', 's3');
            } else {
                $input['media_link'] = $LessonPage->media_link;
            }
        }

        try {
            $lesson = $LessonPage->lesson;
            $data = json_decode($LessonPage->data);
            $dataReq = json_decode($input['data']);

            if (strlen($dataReq->content) < strlen($data->content)) {
                $newContent = strlen($data->content) - strlen($dataReq->content);
                $lesson->learn_time -= $newContent;
            } else {
                $newContent = strlen($dataReq->content) - strlen($data->content);
                $lesson->learn_time += $newContent;
            }

            switch ($LessonPage->media_type) {
                case 'image':
                    if ($input['media_type'] == 'video') {
                        $lesson->learn_time += (5 * 60);
                        $lesson->learn_time -= 2;
                    }
                    if ($input['media_type'] == 'pdf') {
                        $lesson->learn_time += (10 * 60);
                        $lesson->learn_time -= 2;
                    }
                    break;
                case 'video':
                    if ($input['media_type'] == 'image') {
                        $lesson->learn_time += 2;
                        $lesson->learn_time -= (5 * 60);
                    }
                    if ($input['media_type'] == 'pdf') {
                        $lesson->learn_time += (10 * 60);
                        $lesson->learn_time -= (5 * 60);
                    }
                    break;
                case 'pdf':
                    if ($input['media_type'] == 'image') {
                        $lesson->learn_time += 2;
                        $lesson->learn_time -= (10 * 60);
                    }
                    if ($input['media_type'] == 'video') {
                        $lesson->learn_time += (5 * 60);
                        $lesson->learn_time -= (10 * 60);
                    }
                    break;

                default:
                    if ($input['media_type'] == 'video') {
                        $lesson->learn_time += (5 * 60);
                    }
                    if ($input['media_type'] == 'image') {
                        $lesson->learn_time += 2;
                    }
                    break;
            }

            $LessonPageUpdate = LessonPage::updateOrCreate(['id' => $LessonPage->id], $input);
            $LessonPageUpdate['media_link'] = $LessonPageUpdate->media_type == 'image' || $LessonPageUpdate->media_type == 'pdf'  ?
                // Storage::disk('s3')->url(
                $LessonPageUpdate->media_link
                // ) 
                : $LessonPageUpdate->media_link;
            $lesson->save();
            return $this->ressSuccess([
                'message' => 'Successfully update Lesson Page',
                'data' => [
                    'LessonPage' => $LessonPageUpdate,
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
     * @param  \App\Models\LessonPage  $LessonPage
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $LessonPage = LessonPage::find($id);
        if (empty($LessonPage)) {
            return $this->ressError([
                'message' => 'Cannot find data',
                'data' => []
            ]);
        }
        try {
            DB::beginTransaction();
            if ($LessonPage->lessonPageRating()->first()) {
                return $this->ressError([
                    'message' => 'Cannot delete, because Lesson Page have relation with LessonPage',
                    'data' => []
                ]);
            }
            $oldSort = $LessonPage->sorter;
            $LessonPage->update(['sorter' => 0]);
            LessonPage::where('lessons_id', $LessonPage->lessons_id)->where('sorter', '>', $oldSort)->decrement('sorter', 1);
            $LessonPage->delete();
            $lesson = $LessonPage->lesson;

            $data = json_decode($LessonPage->data);

            switch ($LessonPage->media_type) {
                case 'image':
                    $lesson->learn_time = intval($lesson->learn_time) - strlen($data->content) - 2;
                    break;
                case 'video':
                    $lesson->learn_time = intval($lesson->learn_time) - strlen($data->content) - (5 * 60);
                    break;
                case 'pdf':
                    $lesson->learn_time = intval($lesson->learn_time) - strlen($data->content) - (10 * 60);
                    break;
                default:
                    $lesson->learn_time = intval($lesson->learn_time) - strlen($data->content);
                    break;
            }
            $lesson->save();
            DB::commit();
            return $this->ressSuccess([
                'message' => 'Successfully delete Lesson Page',
                'data' => []
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
}
