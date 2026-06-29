<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class TutorRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:250',
            'image' => $this->valImage(request()->segment(4)),
            'job' => 'required|max:250',
            'link_profile' => 'required',
        ];
    }
}
