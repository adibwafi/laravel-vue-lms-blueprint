<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class LessonPageRequest extends BaseRequest
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
            case 'pdf':
                $ml = $this->valPdf(request()->segment(4));
                break;
            default:
                $ml = 'nullable';
                break;
        }
        return [
            'lessons_id' => 'required',
            'name' => 'required|max:250',
            'content_type' => 'required|max:250|in:content,multiple_choice,fill_blank,answer',
            'data' => 'nullable',
            'media_type' => 'nullable',
            'media_link' => $ml,
            'tooltips' => 'nullable',
        ];
    }
}
