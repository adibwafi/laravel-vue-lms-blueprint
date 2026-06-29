<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\LessonRatingRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonRating;

class LessonRatingController extends BaseController
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
    public function store(LessonRatingRequest $request)
    {
        $request->validated();
        $user = $request->user();
        try {
            $LessonRating = $user->lessonRating()->create($request->all());

            // update rating lesson
            $qr = LessonRating::where('lessons_id', $LessonRating->lessons_id);
            $sum = $qr->sum('rating');
            $count = $qr->count();
            $rating = $sum / $count;
            $lesson = $qr->first()->lesson();
            $lesson->update([
                'rating' => $rating
            ]);    

            // update rating course
            $les = Lesson::where('courses_id', $lesson->first()->courses_id);
            $sum = $les->sum('rating');
            $count = $les->count();
            $rating = $sum / $count;
            Course::where('id', $les->first()->courses_id)->update([
                'rating' => $rating
            ]);

            return $this->ressSuccess([
                'message' => 'Successfully create Lesson Rating',
                'data' => [
                    'LessonRating' => $LessonRating,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
