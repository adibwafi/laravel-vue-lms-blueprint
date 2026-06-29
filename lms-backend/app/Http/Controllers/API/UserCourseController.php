<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Requests\UserCourseRequest;
use App\Models\UserCourse;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaginationResource;
use Illuminate\Support\Facades\Log;

class UserCourseController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $UserCourse = new PaginationResource(UserCourse::search($request)->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get User Course',
            'data' => [
                'UserCourse' => $UserCourse,
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
    public function store(UserCourseRequest $request)
    {
        $request->validated();

        try {
            $user = Auth::user();
            $existingCourse = UserCourse::where([
                'courses_id' => $request->courses_id,
                'users_id' => $user->id,
            ])->first();

            if ($existingCourse) {
                return $this->ressSuccess([
                    'message' => 'Data already exists',
                    'data' => []
                ]);
            }

            $userCourse = UserCourse::create([
                'courses_id' => $request->courses_id,
                'users_id' => $user->id,
            ]);

            return $this->ressSuccess([
                'message' => 'Successfully created User Course',
                'data' => [
                    'UserCourses' => $userCourse,
                ]
            ]);
            // $user = $request->user();
            // if (UserCourse::where([
            //     'courses_id' => $request->courses_id,
            //     'users_id' => $user->id,
            // ])->first() != null) {
            //     return $this->ressSuccess([
            //         'message' => 'Data already exist',
            //         'data' => []
            //     ]);
            // }
            // $uCourse = $user->userCourse()->create([
            //     'courses_id' => $request->courses_id
            // ]);
            // return $this->ressSuccess([
            //     'message' => 'Successfully create User Course',
            //     'data' => [
            //         'UserCourses' => $uCourse,
            //     ]
            // ]);
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
        $user = Auth::user();
        // $Course = $user->userCourse()->where('courses_id', $id)->first();
        $Course = UserCourse::where('courses_id', $id)
            ->where('users_id', $user->id)
            ->first();

        if (empty($Course)) {
            return $this->ressError([
                'message' => 'User Course not found!',
                'data' => []
            ]);
        }

        try {
            return $this->ressSuccess([
                'message' => 'Show User Course',
                'data' => [
                    'Course' => $Course,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
