<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class UserLessonRequest extends BaseRequest
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
            'lessons_id' => 'required',
        ];
    }
}
