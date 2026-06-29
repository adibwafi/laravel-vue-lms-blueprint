<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tutor extends Model
{
  use HasFactory, Uuid, SoftDeletes;

  public $incrementing = false;

  protected $keyType = 'uuid';

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'image',
    'job',
    'link_profile',
  ];

  public function scopeSearch($query, $data)
  {
    if ($data->search) {
      array_push($this->fillable, "id");
      for ($i = 0; $i < count($this->fillable); $i++) {
        if ($i == 0) {
          $query->where($this->fillable[$i], 'like', '%' . $data->search . '%');
        } else {
          $query->orWhere($this->fillable[$i], 'like', '%' . $data->search . '%');
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

  public function course_category()
  {
    return $this->belongsToMany(CourseCategory::class, 'course_category_tutor');
  }

  public function course()
  {
    return $this->belongsToMany(Course::class, 'course_tutor');
  }
}
