<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class UserCourseRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'courses_id' => 'required',
            'user_id' => 'nullable',
            'completed_at' => 'nullable',
        ];
    }
}
