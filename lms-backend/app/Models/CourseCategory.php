<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class CourseCategory extends Model
{
  use HasFactory, Uuid, SoftDeletes;

  public $incrementing = false;

  protected $keyType = 'uuid';

  protected $hidden = [
    'tutors_id',
  ];

  protected $appends = ['course_finish', 'course_total', 'is_completed'];

  protected $withoutAppends = false;

  public function scopeWithoutAppends($query)
  {
    $this->withoutAppends = true;

    return $query;
  }

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  // protected $fillable = [
  //     'name',
  //     'color',
  // ];

  protected $guarded = [];

  public function getTableColumns()
  {
    return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
  }


  public function scopeSearch($query, $data)
  {
    if ($data->search) {
      array_push($this->fillable, "id");

      $searchableColumns = ['name', 'type']; // Kolom-kolom yang ingin dicari

      for ($i = 0; $i < count($searchableColumns); $i++) {
        if ($i == 0) {
          $query->where($searchableColumns[$i], 'like', '%' . $data->search . '%');
        } else {
          $query->orWhere($searchableColumns[$i], 'like', '%' . $data->search . '%');
        }
      }
    }

    if ($data->sortBy || $data->orderBy) {
      if (empty($data->orderBy)) {
        $data->orderBy = 'asc';
      }
      $query->orderBy($data->sortBy, $data->orderBy);
    }
    return $query;
  }

  public function course()
  {
    return $this->hasMany(Course::class, 'categories_id')->where(function ($query) {
      $query->where('status', 'released')->orWhere('status', 'disabled');
    })->with('tutor')->orderBy('order');
  }

  public function getByType($type)
  {
    return $this->where('type', $type)->get();
  }

  public function tutor()
  {
    return $this->belongsToMany(Tutor::class, 'course_category_tutor');
  }

  public function userCourseCategory()
  {
    return $this->hasMany(UserCourseCategory::class, 'course_category_id');
  }

  private function getAverageCompleted($number_enrolled_user)
  {
    $courses = $this->course()->get();
    $sum = 0;
    foreach ($courses as $c) {
      $sum += $c->userCourse()->count();
    }
    $divider = $this->course()->count() * $number_enrolled_user;
    if ($divider != 0) {
      return $sum / $divider * 100;
    }
    return 0;
  }

  public function getNumberEnrolledUser()
  {
    return $this->userCourseCategory()->count();
  }

  public function getTrackingAttribute()
  {
    if ($this->withoutAppends) {
      return null;
    }
    if (!Auth::user()) {
      return null;
    }

    if (Auth::user()->hasAnyRole('Super-Admin')) {
      $number_enrolled_user = $this->getNumberEnrolledUser();
      $average_percentage_completed = $this->getAverageCompleted($number_enrolled_user);
      return [
        'number_enrolled_user' => $number_enrolled_user,
        'average_percentage_completed' => $average_percentage_completed
      ];
    } else {
      return null;
    }
  }

  public function getRatioCompletedAttribute($userID = null)
  {
    if (request()->user()) {
      if (!$userID) {
        $userID = request()->user()->id;
      }
      $courseFinish = $this->getCourseFinishAttribute($userID);
      $courseTotal = $this->getCourseTotalAttribute();
      // dd($userID, $courseFinish, $courseTotal);
      if ($courseTotal != 0) {
        return $courseFinish / $courseTotal;
      } else {
        return 0;
      }
    }
  }

  public function getCourseFinishAttribute($userID = null)
  {
    if ($this->withoutAppends) {
      return null;
    }
    $c = 0;
    $courses = $this->course()->get();

    if ($courses->isNotEmpty()) {
      if (request()->user()) {
        if (!$userID) {
          $userID = request()->user()->id;
        }

        foreach ($courses as $course) {
          if ($course->getIsCompletedAttribute($userID)) {
            $c += 1;
          }
        }
        return $c;
      }
    }

    return $c;
  }

  public function getCourseTotalAttribute()
  {
    if ($this->withoutAppends) {
      return null;
    }
    // dd(json_encode($this->course()->get()->toArray()));
    // dd($this->course()->get()->toArray());

    return $this->course()->count();
  }

  public function getIsCompletedAttribute($userId = null)
  {
    if (request()->user()) {
      if ($this->withoutAppends) {
        return null;
      }
      if (!$userId) {
        $userId = request()->user()->id;
      }
      $courseFinish = $this->getCourseFinishAttribute($userId);
      $courseTotal = $this->getCourseTotalAttribute();
      if ($courseTotal != 0) {
        return $courseFinish / $courseTotal >= 1;
      } else {
        return false;
      }
    }
  }

  public function userCertificate()
  {
    $userID = null;
    if (request()->user()) {
      $userID = request()->user()->id;
    }
    return $this->hasMany(Certificate::class, 'course_category_id', 'id')->where('user_id', $userID);
  }

  // public function getBannerAttribute()
  // {
  //     if ($this->attributes['banner'])
  //         return Storage::disk('s3')->url($this->attributes['banner']);
  // }
}
