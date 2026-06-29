<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'kkm' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'max_attempt' => [
                'nullable',
                'numeric',
                'min:1',
            ],
        ];
    }
}
