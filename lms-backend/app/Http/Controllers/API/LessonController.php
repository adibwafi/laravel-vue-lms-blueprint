<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Requests\LessonRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PaginationResource;
use App\Models\Exam;

class LessonController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Lessons = new PaginationResource(Lesson::search($request)->with('exam')->with('userLesson')->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get Lesson',
            'data' => [
                'Lessons' => $Lessons,
            ]
        ]);
    }


    public function indexUser(Request $request)
    {
        $Lessons = new PaginationResource(Lesson::search($request)->with('exam')->with('currentUserLesson')->with('userLessonAnswer')->without('userLesson')->without('lessonRating')->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get Lesson',
            'data' => [
                'Lessons' => $Lessons,
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
     * @param  \Illuminate\Http\LessonRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LessonRequest $request)
    {
        $request->validated();
        $input = $request->toArray();
        $sort = Lesson::where('courses_id', $input['courses_id'])->latest('sorter')->first();
        if ($sort) {
            $sorter = intval($sort->sorter) + 1;
        } else {
            $sorter = 1;
        }
        try {
            $input['sorter'] = $sorter;
            $Lesson = Lesson::create($input);
            return $this->ressSuccess([
                'message' => 'Successfully create Lesson',
                'data' => [
                    'Lesson' => $Lesson
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
     * @param  \App\Models\Lesson  $Lesson
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Lesson = Lesson::with('course')->find($id);
        if (empty($Lesson)) {
            return $this->ressError([
                'message' => 'Lesson not found!',
                'data' => []
            ]);
        }
        try {
            return $this->ressSuccess([
                'message' => 'Show Lesson',
                'data' => [
                    'Lesson' => $Lesson,
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
     * @param  \App\Models\Lesson  $Lesson
     * @return \Illuminate\Http\Response
     */
    public function edit(Lesson $Lesson)
    {
        try {
            return $this->ressSuccess([
                'message' => 'Edit Lesson',
                'data' => [
                    'Lesson' => $Lesson,
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
     * @param  \App\Models\Lesson  $Lesson
     * @return \Illuminate\Http\Response
     */
    public function update(LessonRequest $request, $id)
    {
        $request->validated();
        $input = $request->toArray();
        $lesson = Lesson::find($id);
        if (empty($lesson)) {
            return $this->ressError([
                'message' => 'Data Lesson cannot find!',
                'data' => []
            ]);
        }

        try {
            $LessonUpdate = Lesson::updateOrCreate(['id' => $lesson->id], $input);
            return $this->ressSuccess([
                'message' => 'Successfully update Lesson',
                'data' => [
                    'Lesson' => $LessonUpdate,
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
     * @param  \App\Models\Lesson  $Lesson
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Lesson = Lesson::find($id);
        if (empty($Lesson)) {
            return $this->ressError([
                'message' => 'Cannot find data',
                'data' => []
            ]);
        }
        try {
            $oldSort = $Lesson->sorter;
            $Lesson->update(['sorter' => 0]);
            $Lesson->where('courses_id', $Lesson->courses_id)->where('sorter', '>', $oldSort)->decrement('sorter', 1);
            $Lesson->delete();
            return $this->ressSuccess([
                'message' => 'Successfully delete Lesson',
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
