<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Http\Requests\CourseRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PaginationResource;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CourseController extends BaseController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $resource = new PaginationResource(Course::search($request)->with(['tutor', 'courseCategory'])->paginate($request->limit));
    return $this->ressSuccess([
      'message' => 'Successfully get Course',
      'data' => [
        'Courses' => $resource,
      ]
    ]);
  }

  public function indexForUser(Request $request)
  {
    $resource = new PaginationResource(Course::search($request)->with(['tutor', 'courseCategory'])->where('status', 'released')->paginate($request->limit));
    return $this->ressSuccess([
      'message' => 'Successfully get Course',
      'data' => [
        'Courses' => $resource,
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
   * @param  \Illuminate\Http\CourseRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(CourseRequest $request)
  {
    $request->validated();
    $input = $request->toArray();
    $input['banner'] = $request->banner->store('images/course', 's3');
    $CourseCategory = CourseCategory::find($input['categories_id']);
    if ($CourseCategory->type == 'prakerja') {
      $input['isPaid'] = true;
    }

    try {
      $tutor_ids = explode(",", $input['tutors_ids']);
      unset($input['tutors_ids']);
      $Course = Course::create($input);
      $Course['banner'] = Storage::url($Course->banner);
      $Course->tutor()->sync($tutor_ids);
      return $this->ressSuccess([
        'message' => 'Successfully create Course',
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
   * Display the specified resource.
   *
   * @param  \App\Models\Course  $Course
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $Course = Course::with(['lesson', 'tutor'])->find($id);
    if (empty($Course)) {
      return $this->ressError([
        'message' => 'Course not found!',
        'data' => []
      ]);
    }
    try {
      return $this->ressSuccess([
        'message' => 'Show Course',
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

  public function showForUser($id)
  {
    $Course = Course::with(['tutor', 'courseCategory', 'courseCategory.tutor'])->where('status', 'released')->find($id);
    if (empty($Course)) {
      return $this->ressError([
        'message' => 'Course not found!',
        'data' => []
      ]);
    }
    try {
      return $this->ressSuccess([
        'message' => 'Show Course',
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
   * @param  \App\Models\Course  $Course
   * @return \Illuminate\Http\Response
   */
  public function edit(Course $Course)
  {
    try {
      return $this->ressSuccess([
        'message' => 'Edit Course',
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
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Course  $Course
   * @return \Illuminate\Http\Response
   */
  public function update(CourseRequest $request, $id)
  {
    $request->validated();
    $input = $request->toArray();
    $course = Course::find($id);
    if (empty($course)) {
      return $this->ressError([
        'message' => 'Data course cannot find!',
        'data' => []
      ]);
    }
    $input['banner'] = empty($request->banner) ? $course->banner : $request->banner->store('images/course', 's3');

    try {
      if (array_key_exists('tutors_ids', $input)) {
        $tutor_ids = explode(",", $input['tutors_ids']);
        unset($input['tutors_ids']);
        $course->tutor()->sync($tutor_ids);
      }
      $CourseUpdate = Course::updateOrCreate(['id' => $course->id], $input);
      return $this->ressSuccess([
        'message' => 'Successfully update Course',
        'data' => [
          'Course' => $CourseUpdate,
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
   * @param  \App\Models\Course  $Course
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $Course = Course::find($id);
    if (empty($Course)) {
      return $this->ressError([
        'message' => 'Cannot find data',
        'data' => []
      ]);
    }
    try {

      if ($Course->lesson()->first()) {
        return $this->ressError([
          'message' => 'Cannot delete, because Course have relation with course',
          'data' => []
        ]);
      }
      $Course->delete();
      return $this->ressSuccess([
        'message' => 'Successfully delete Course',
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
