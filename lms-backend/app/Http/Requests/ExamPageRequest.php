<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class ExamPageRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (request()->media_type) {
            case 'image':
                $ml = $this->valImage(request()->segment(4));
                break;
            case 'video':
                $ml = 'required';
                break;
            default:
                $ml = 'nullable';
                break;
        }
        return [
            'exam_id' => 'required',
            'name' => 'required|max:250',
            'content_type' => 'required|max:250|in:content,multiple_choice,fill_blank,answer',
            'data' => 'required',
            'media_type' => 'nullable',
            'media_link' => $ml,
            'tooltips' => 'nullable',
        ];
    }
}
