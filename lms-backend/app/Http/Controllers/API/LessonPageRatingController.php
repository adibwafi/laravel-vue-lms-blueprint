<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\LessonPageRating;
use Illuminate\Http\Request;
use App\Http\Requests\LessonPageRatingRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PaginationResource;

class LessonPageRatingController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $LessonPageRatings = new PaginationResource(LessonPageRating::search($request)->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get Lesson Page Rating',
            'data' => [
                'LessonPageRatings' => $LessonPageRatings,
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
     * @param  \Illuminate\Http\LessonPageRatingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LessonPageRatingRequest $request)
    {
        $request->validated();
        $input = $request->toArray();
        try {
            $LessonPageRating = LessonPageRating::create($input);
            return $this->ressSuccess([
                'message' => 'Successfully create Lesson Page Rating',
                'data' => [
                    'LessonPageRating' => $LessonPageRating,
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
     * @param  \App\Models\LessonPageRating  $LessonPageRating
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $LessonPageRating = LessonPageRating::find($id);
        if (empty($LessonPageRating)) {
            return $this->ressError([
                'message' => 'Lesson Page not found!',
                'data' => []
            ]);
        }
        try {
            return $this->ressSuccess([
                'message' => 'Show Lesson Page Rating',
                'data' => [
                    'LessonPageRating' => $LessonPageRating,
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
     * @param  \App\Models\LessonPageRating  $LessonPageRating
     * @return \Illuminate\Http\Response
     */
    public function edit(LessonPageRating $LessonPageRating)
    {
        try {
            return $this->ressSuccess([
                'message' => 'Edit Lesson Page Rating',
                'data' => [
                    'LessonPageRating' => $LessonPageRating,
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
     * @param  \App\Models\LessonPageRating  $LessonPageRating
     * @return \Illuminate\Http\Response
     */
    public function update(LessonPageRatingRequest $request, $id)
    {
        $request->validated();
        $input = $request->toArray();
        $LessonPageRatingUpdate = LessonPageRating::find($id);
        if (empty($LessonPageRatingUpdate)) {
            return $this->ressError([
                'message' => 'Cannot find data!',
                'data' => []
            ]);
        }
        try {
            $LessonPageRatingUpdate = LessonPageRating::updateOrCreate(['id' => $LessonPageRatingUpdate->id], $input);
            return $this->ressSuccess([
                'message' => 'Successfully update Lesson Page Rating',
                'data' => [
                    'LessonPageRating' => $LessonPageRatingUpdate,
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
     * @param  \App\Models\LessonPageRating  $LessonPageRating
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $LessonPageRating = LessonPageRating::find($id);
        if (empty($LessonPageRating)) {
            return $this->ressError([
                'message' => 'Data Lesson Page Rating cannot find!',
                'data' => []
            ]);
        }
        try {

            if ($LessonPageRating->lessonPage()->first() || $LessonPageRating->user()->first()) {
                return $this->ressError([
                    'message' => 'Cannot delete, because Lesson Page Rating have relation with LessonPageRating',
                    'data' => []
                ]);
            }
            $LessonPageRating->delete();
            return $this->ressSuccess([
                'message' => 'Successfully delete Lesson Page Rating',
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
