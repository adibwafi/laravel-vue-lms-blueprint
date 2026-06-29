<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class LessonPageRatingRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lesson_pages_id' => 'required',
            'users_id' => 'required',
            'rating' => 'required|integer',
            'feedback' => 'required',
        ];
    }
}
