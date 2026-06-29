<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PaginationResource;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\CourseCategoryRequest;
use App\Models\Certificate;

class CourseCategoryController extends BaseController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $CourseCategorys = new PaginationResource(CourseCategory::search($request)->with(['tutor'])->paginate($request->limit));

    if ($request->with_tracking) {
      $CourseCategorys->each(function ($cc) {
        $cc->append('tracking');
      });
    }

    return $this->ressSuccess([
      'message' => 'Successfully get Course Category',
      'data' => [
        'CourseCategorys' => $CourseCategorys,
      ]
    ]);
  }

  public function indexUser(Request $request)
  {
    $type = $request->query('type');
    if ($type && $type != 'all') {
      $CourseCategorys = new PaginationResource(CourseCategory::search($request)->where('status', 'released')->where('type', $type)->with(['tutor'])->paginate($request->limit));
    } else {
      $CourseCategorys = new PaginationResource(CourseCategory::search($request)->with(['tutor'])->where('status', 'released')->paginate($request->limit));
    }
    return $this->ressSuccess([
      'message' => 'Successfully get Course Category',
      'data' => [
        'CourseCategorys' => $CourseCategorys,
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
   * @param  \Illuminate\Http\CourseCategoryRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(CourseCategoryRequest $request)
  {
    $request->validated();
    $input = $request->toArray();
    $input['banner'] = $request->banner->store('images/course-category', 's3');
    try {
      $tutor_ids = explode(",", $input['tutors_ids']);
      unset($input['tutors_ids']);
      $CourseCategory = CourseCategory::create($input);
      $CourseCategory->tutor()->sync($tutor_ids);
      return $this->ressSuccess([
        'message' => 'Successfully create Course Category',
        'data' => [
          'CourseCategory' => $CourseCategory,
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
   * @param  \App\Models\CourseCategory  $CourseCategory
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $CourseCategory = CourseCategory::with(['course', 'course.userLessonAnswer', 'tutor', 'userCertificate'])->find($id);
    if ($CourseCategory->is_completed === true) {
      $userHaveCertificate = $CourseCategory->userCertificate()->count();
      if (!$userHaveCertificate) {
        Certificate::create([
          'user_id' => request()->user()->id,
          'course_category_id' => $CourseCategory->id,
        ]);
        $CourseCategory = CourseCategory::with(['course', 'course.userLessonAnswer', 'tutor', 'userCertificate'])->find($id);
      }
    }

    if (empty($CourseCategory)) {
      return $this->ressError([
        'message' => 'Course Category not found!',
        'data' => []
      ]);
    }

    for ($i=0; $i < count($CourseCategory->course); $i++) { 
      if ($CourseCategory->course[$i]->is_unjuk_keterampilan) {
        $is_uk_open = 1;
        for ($j=0; $j < $i; $j++) { 
          $currCourse = json_decode($CourseCategory->course[$j]);
          for ($k=0; $k < count($currCourse->user_lesson_answer); $k++) { 
            if (!$currCourse->user_lesson_answer[$k]->notes) {
              $is_uk_open = 0;
            }
          }
        }
        $CourseCategory->course[$i]->is_unjuk_keterampilan_open = $is_uk_open;
      }
    }

    try {
      return $this->ressSuccess([
        'message' => 'Show Course Category',
        'data' => [
          'CourseCategory' => $CourseCategory,
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
   * @param  \App\Models\CourseCategory  $CourseCategory
   * @return \Illuminate\Http\Response
   */
  public function edit(CourseCategory $CourseCategory)
  {
    try {
      return $this->ressSuccess([
        'message' => 'Edit Course Category',
        'data' => [
          'CourseCategory' => $CourseCategory,
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
   * @param  \App\Models\CourseCategory  $CourseCategory
   * @return \Illuminate\Http\Response
   */
  public function update(CourseCategoryRequest $request, $id)
  {
    $request->validated();
    $input = $request->toArray();
    $CourseCategory = CourseCategory::find($id);
    if (empty($CourseCategory)) {
      return $this->ressError([
        'message' => 'Data course category cannot find!',
        'data' => []
      ]);
    }

    $input['banner'] = empty($request->banner) ? $CourseCategory->banner : $request->banner->store('images/course-category', 's3');
    try {
      if (array_key_exists('tutors_ids', $input)) {
        $tutor_ids = explode(",", $input['tutors_ids']);
        unset($input['tutors_ids']);
        $CourseCategory->tutor()->sync($tutor_ids);
      }
      $CourseCategoryUpdate = CourseCategory::updateOrCreate(['id' => $CourseCategory->id], $input);
      return $this->ressSuccess([
        'message' => 'Successfully update Course Category',
        'data' => [
          'CourseCategory' => $CourseCategoryUpdate,
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
   * @param  \App\Models\CourseCategory  $CourseCategory
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $CourseCategory = CourseCategory::find($id);
    if (empty($CourseCategory)) {
      return $this->ressError([
        'message' => 'Cannot find data',
        'data' => []
      ]);
    }
    try {

      // if ($CourseCategory->course()->first()) {
      //   return $this->ressError([
      //     'message' => 'Cannot delete, because Course Category have relation with CourseCategory',
      //     'data' => []
      //   ]);
      // }
      $CourseCategory->delete();
      return $this->ressSuccess([
        'message' => 'Successfully delete Course Category',
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
