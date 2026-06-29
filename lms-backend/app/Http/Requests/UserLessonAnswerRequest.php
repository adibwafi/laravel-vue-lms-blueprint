<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class UserLessonAnswerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'redeem_code' => 'required',
          'scope' => 'required|in:tpm,uk',
          'sequence' => 'required',
          'url_file' => 'required',
          'lesson_pages_id' => 'required'
        ];
    }
}
