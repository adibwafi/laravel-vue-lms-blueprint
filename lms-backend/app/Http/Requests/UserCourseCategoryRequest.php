<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class UserCourseCategoryRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'course_category_id' => 'required',
            'user_id' => 'required',
        ];
    }
}
