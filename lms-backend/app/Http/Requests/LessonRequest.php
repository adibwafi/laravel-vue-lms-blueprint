<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class LessonRequest extends BaseRequest
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
            'name' => 'required|max:250',
            'scope' => 'in:tpm,uk|nullable',
            'is_journal_reflection' => 'boolean|required',
        ];
    }
}
