<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class CourseCategoryRequest extends BaseRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $rq = request()->segment(4) == 'update' ? '' : 'required';

    return [
      'name' => 'max:250|' . $rq,
      'color' => 'max:64|' . $rq,
      'type' => $rq,
      'prakerja_course_id' => 'required_if:type,==,prakerja',
      'description' => $rq,
      'what_learn' => 'nullable',
      'skill' => 'nullable',
      'requirements' => 'nullable',
      "durasi_pelatihan" => "nullable",
      "metode_absensi" => "nullable",
      "prasyarat" => "nullable",
      'price' => 'max:64|' . 'nullable',
      'status' => 'in:draft,released,disabled|nullable',
      'link_payment' => 'nullable',
      'banner' => $this->valImage(request()->segment(4)),
      'tutors_ids' => $rq,
    ];
  }
}
