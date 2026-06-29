<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\UserCourseCategoryRequest;
use Illuminate\Http\Request;
use App\Models\UserCourseCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaginationResource;

class UserCourseCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $userCourseCat = $user->userCourseCategory();
        $resource = new PaginationResource($userCourseCat->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get user course category',
            'data' => [
                'UserCoursesCategory' => $resource,
            ]
        ]);
    }

    public function indexCms(Request $request)
    {
        $resource = new PaginationResource(UserCourseCategory::search($request)->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get user course category',
            'data' => [
                'UserCoursesCategory' => $resource,
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

        try {
            $user = $request->user();
            $uCC = UserCourseCategory::create([
                'course_category_id' => $request->course_category_id,
                'user_id' => $user->id,
            ]);

            return $this->ressSuccess([
                'message' => 'Successfully create User Course Category',
                'data' => [
                    'UserCoursesCategory' => $uCC,
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

    public function storeCms(UserCourseCategoryRequest $request)
    {
        $ucc = UserCourseCategory::where([
            'course_category_id' => $request->course_category_id,
            'user_id' => $request->user_id,
        ])->first();

        if (!empty($ucc)) {
            return $this->ressError([
                'message' => 'Course already selected!',
                'data' => []
            ]);
        }

        try {
            $uCC = UserCourseCategory::create($request->all());
            return $this->ressSuccess([
                'message' => 'Successfully create User Course Category',
                'data' => [
                    'UserCoursesCategory' => $uCC,
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
        $user = Auth::user();
        $courseCategory = UserCourseCategory::where([
            'user_id' => $user->id,
            'course_category_id' => $id,
        ])->first();

        if (empty($courseCategory)) {
            return $this->ressError([
                'message' => 'User Course Category not found!',
                'data' => []
            ]);
        }
        try {
            // Ambil data redeem code jika ada
            return $this->ressSuccess([
                'message' => 'Show User Course Category',
                'data' => [
                    'UserCoursesCategory' => $courseCategory,
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCms(Request $request, $id)
    {
        try {
            $uCC = UserCourseCategory::find($id);
            $uCC = $uCC->update($request->all());
            return $this->ressSuccess([
                'message' => 'Successfully update User Course Category',
                'data' => [
                    'UserCoursesCategory' => $uCC,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $uCC = UserCourseCategory::find($id);
            $uCC->delete();
            return $this->ressSuccess([
                'message' => 'Successfully delete User Course Category',
                'data' => [
                    'UserCoursesCategory' => $uCC,
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
}
