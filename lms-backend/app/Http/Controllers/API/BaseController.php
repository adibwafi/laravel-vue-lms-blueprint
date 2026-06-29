<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Tutor;
use App\Models\Lesson;
use App\Models\ExamPage;
use App\Models\LessonPage;
use Illuminate\Http\Request;
use App\Models\BannerPromotion;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class BaseController extends Controller
{
    public function ressSuccess($data)
    {
        return response()->json([
            'success' => true,
            'message' => $data['message'],
            'code' => 0,
            'data' => $data['data'],
        ], 200);
    }

    public function ressError($data)
    {
        return response()->json([
            'success' => false,
            'message' => $data['message'],
            'code' => 1,
            'data' => $data['data'],
        ], 200);
    }

    public function sendOTP($input)
    {
        Mail::to($input['email'])->send(new \App\Mail\OtpMail([
            'subject' => 'Verify OTP',
            'message' => 'Please insert to OTP form',
            'otp' => $input['otp'],
            'url' => $input['url'],
            'type' => $input['type'],
        ]));
    }

    public function moveDown(Request $request)
    {
        switch (request()->segment(3)) {
            case 'lesson':
                $data = Lesson::find($request->id);
                $this->sorter($data, 'down', $data->where('courses_id', $data->courses_id));
                break;
            case 'banner-promotion':
                $data = BannerPromotion::find($request->id);
                $this->sorter($data, 'down', $data);
                break;
            case 'lesson-page':
                $data = LessonPage::find($request->id);
                $this->sorter($data, 'down', $data->where('lessons_id', $data->lessons_id));
                break;
            case 'exam-page':
                $data = ExamPage::find($request->id);
                $boundary = $data->where('exam_id', $data->exam_id);
                if ($data->exam->use_question_bank) {
                    $boundary = $boundary->where('question_bank_id', $data->question_bank_id);
                }
                $this->sorter($data, 'down', $boundary);
                break;
            default:
                return $this->ressError([
                    'message' => 'Something wrong!',
                    'data' => []
                ]);
                break;
        }
        return $this->ressSuccess([
            'message' => 'Successfully update sorter!',
            'data' => []
        ]);
    }

    public function moveUp(Request $request)
    {
        switch (request()->segment(3)) {
            case 'lesson':
                $data = Lesson::find($request->id);
                $this->sorter($data, 'up', $data->where('courses_id', $data->courses_id));
                break;
            case 'banner-promotion':
                $data = BannerPromotion::find($request->id);
                $this->sorter($data, 'up', $data);
                break;
            case 'lesson-page':
                $data = LessonPage::find($request->id);
                $this->sorter($data, 'up', $data->where('lessons_id', $data->lessons_id));
                break;
            case 'exam-page':
                $data = ExamPage::with('exam')->find($request->id);
                $boundary = $data->where('exam_id', $data->exam_id);
                if ($data->exam->use_question_bank) {
                    $boundary = $boundary->where('question_bank_id', $data->question_bank_id);
                }
                $this->sorter($data, 'up', $boundary);
                break;
            default:
                return $this->ressError([
                    'message' => 'Something wrong!',
                    'data' => []
                ]);
                break;
        }
        return $this->ressSuccess([
            'message' => 'Successfully update sorter!',
            'data' => []
        ]);
    }

    public function sorter($data, $type, $boundary)
    {
        switch ($type) {
            case 'up':
                $oldSorter = $data->sorter;
                if ($oldSorter == 1) return true;

                $boundary->where('sorter', $oldSorter - 1)->update(['sorter' => $oldSorter]);
                $data->update(['sorter' => $oldSorter - 1]);
                return true;
                break;
            case 'down':
                $oldSorter = $data->sorter;
                $lastSorter = $boundary->latest('sorter')->first()->sorter;
                if ($oldSorter == $lastSorter) return true;
                $boundary->where('sorter', $oldSorter + 1)->update(['sorter' => $oldSorter]);
                $data->update(['sorter' => $oldSorter + 1]);
                return true;
                break;

            default:
                Log::error('Type not found!');
                return $this->ressError([
                    'message' => 'Something wrong!',
                    'data' => []
                ]);
                break;
        }
    }
}
