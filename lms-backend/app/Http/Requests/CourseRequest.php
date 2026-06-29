<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class CourseRequest extends BaseRequest
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
            'categories_id' => 'max:250|' . $rq,
            'tutors_ids' => 'max:250|' . $rq,
            'name' => 'max:250|' . $rq,
            'banner' => $this->valImage(request()->segment(4)),
            'description' => 'nullable',
            'what_learn' => 'nullable',
            'skill' => 'nullable',
            'requirements' => 'nullable',
            'rating' => 'nullable',
            'status' => 'in:draft,released,disabled|nullable',
            'isPaid' => 'nullable',
            'is_unjuk_keterampilan' => 'boolean|nullable',
        ];
    }
}
