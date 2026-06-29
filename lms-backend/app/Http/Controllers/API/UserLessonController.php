<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLessonRequest;
use App\Http\Requests\FeedbackAnswerRequest;
use App\Http\Requests\UserLessonAnswerRequest;
use App\Http\Resources\PaginationResource;
use App\Models\LessonPage;
use App\Models\UserLesson;
use App\Models\UserLessonAnswer;
use Exception;

use function PHPUnit\Framework\isEmpty;

class UserLessonController extends BaseController
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $UserLesson = new PaginationResource(UserLesson::search($request)->paginate($request->limit));
    return $this->ressSuccess([
      'message' => 'Successfully get User Lesson',
      'data' => [
        'UserLesson' => $UserLesson,
      ]
    ]);
  }

  private function sendRequestPrakerjaAPI($endpoint, $data)
  {
    $client_code = env('PRAKERJA_CLIENT_CODE', ''); // provided by Prakerja
    $timestamp = time(); // generate current timestamp
    $method = 'POST'; // API Method

    $input = $client_code . $timestamp . $method . $endpoint . json_encode($data); // create signature input
    $sign_key = env('PRAKERJA_CREDENTIAL', ''); // provided by Prakerja
    if (empty($sign_key)) {
      throw new Exception('PRAKERJA_CREDENTIAL is empty!');
    }
    $signature = hash_hmac('sha1', $input, $sign_key); // generate signature

    $headers = [
      'Content-Type: application/json',
      'client_code: ' . $client_code,
      'signature: ' . $signature,
      'timestamp: ' . $timestamp,
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,            'https://api.prakerja.go.id' . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST,           1);
    curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($data));
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
  }

  public function getAnswers(Request $request)
  {
    $UserLessonAnswer = new PaginationResource(UserLessonAnswer::search($request)->with(['course', 'user'])->paginate($request->limit));
    return $this->ressSuccess([
      'message' => 'Successfully get User Lesson Answer',
      'data' => [
        'UserLessonAnswer' => $UserLessonAnswer,
      ]
    ]);
  }

  public function feedbackAnswer(FeedbackAnswerRequest $request, $id)
  {
    $request->validated();
    $user = $request->user();
    $input = $request->toArray();
    $userLessonAnswer = UserLessonAnswer::find($id);
    if (empty($userLessonAnswer)) {
      return $this->ressError([
        'message' => 'Data user lesson answer cannot find!',
        'data' => []
      ]);
    }
    try {
      DB::beginTransaction();
      $inputAnswer = [
        'admin_id' => $user->id,
        'notes' => $input['notes'],
      ];
      $userLessonAnswerUpdate = UserLessonAnswer::updateOrCreate(['id' => $id], $inputAnswer);

      // Hit API Feedback LP Prakerja
      $endpoint = '/api/v1/integration/tpm/feedback/submission';
      $data = [
        'redeem_code' => $input['redeem_code'],
        'scope' => $input['scope'],
        'sequence' => intval($input['sequence']),
        'url_file' => '',
        'notes' => $input['notes']
      ];
      $feedbackSubmission = $this->sendRequestPrakerjaAPI($endpoint, $data);
      Log::info(json_encode($data) . '<><><><>');
      $feedbackSubmission_json = json_decode($feedbackSubmission);
      if (!$feedbackSubmission_json->success) {
        Log::error($feedbackSubmission);
        DB::rollback();
        return $this->ressError([
          'message' => 'Prakerja.Feedback LP - ' . $feedbackSubmission_json->message,
          'data' => []
        ]);
      }

      DB::commit();

      // Hit API Submission Status Prakerja
      $endpointSubsmissionStatus = '/api/v1/integration/tpm/submission/status';
      $dataSubmissionStatus = [
        'redeem_code' => $input['redeem_code'],
        'scope' => $input['scope'],
        'sequence' => intval($input['sequence'])
      ];
      $submissionStatus = $this->sendRequestPrakerjaAPI($endpointSubsmissionStatus, $dataSubmissionStatus);

      $submissionStatus_json = json_decode($submissionStatus);
      if (!$submissionStatus_json->success) {
        Log::error($submissionStatus);
        return $this->ressError([
          'message' => 'Prakerja.Submission Status - ' . $submissionStatus_json->message,
          'data' => []
        ]);
      }

      $message = 'Successfully update User Lesson Answer';
      switch ($submissionStatus_json->data->feedback->status) {
        case 0:
          $message = 'Feedback belum submit';
          break;
        case 1:
          $message = 'Feedback sudah submit dan status pending';
          break;
        case 2:
          $message = 'Feedback sudah submit dan status sukses';
          break;
        case 3:
          $message = 'Feedback sudah submit dan status gagal';
          break;

        default:
          break;
      }

      return $this->ressSuccess([
        'message' => $message,
        'data' => [
          'Course' => $userLessonAnswerUpdate,
        ]
      ]);
    } catch (\Throwable $th) {
      DB::rollback();
      Log::error($th);
      return $this->ressError([
        'message' => $th->getMessage(),
        'data' => []
      ]);
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(UserLessonRequest $request)
  {
    $request->validated();
    try {
      $user = $request->user();
      $userLesson = $user->userLesson();

      if ($userLesson->where(['lessons_id' => $request->lessons_id])->first() != null) {
        return $this->ressError([
          'message' => 'Duplicate entry lesson id!',
          'data' => []
        ]);
      }
      $newUserLesson = $userLesson->create($request->all());
      return $this->ressSuccess([
        'message' => 'Successfully create User Lesson',
        'data' => [
          'UserLesson' => $newUserLesson,
        ]
      ]);
    } catch (\Throwable $th) {
      Log::error($th);
      return $this->ressError([
        'message' => $th->getMessage(),
        'data' => []
      ]);
    }
  }

  public function answer(UserLessonAnswerRequest $request)
  {
    $request->validated();
    $user = $request->user();
    $input = $request->toArray();
    try {
      DB::beginTransaction();
      $LessonPage = LessonPage::where('id', $input['lesson_pages_id'])->with('lesson')->first();
      if (empty($LessonPage)) {
        return $this->ressError([
          'message' => 'Data Lesson Page cannot find!',
          'data' => []
        ]);
      }

      $UserLessonAnswer = [
        'courses_id' => $input['courses_id'],
        'lessons_id' => $input['lessons_id'],
        'answer' => $input['url_file']
      ];
      $newUserLessonAnswer = UserLessonAnswer::updateOrCreate(['users_id' => $user->id, 'lesson_pages_id' => $input['lesson_pages_id']], $UserLessonAnswer);

      // Cek jika redeem_code dimulai dengan "PRAM"
      if (strpos($input['redeem_code'], 'PRAM') === 0) {
        // Jika ya, langsung commit transaksi
        DB::commit();

        return $this->ressSuccess([
          'message' => 'Successfully create User Lesson Answer',
          'data' => [
            'UserLessonAnswer' => $newUserLessonAnswer,
          ]
        ]);
      }

      // Hit API User Assignment Prakerja
      $endpoint = '/api/v1/integration/tpm/submission';
      $data = [
        'redeem_code' => $input['redeem_code'],
        'scope' => $input['scope'],
        'sequence' => intval($input['sequence']),
        'url_file' => $input['url_file']
      ];
      $submission = $this->sendRequestPrakerjaAPI($endpoint, $data);

      $submission_json = json_decode($submission);
      if (!$submission_json->success) {
        Log::error($submission);
        DB::rollback();
        return $this->ressError([
          'message' => 'Prakerja.User Assignment - ' . $submission_json->message,
          'data' => []
        ]);
      }

      DB::commit();

      // Hit API Submission Status Prakerja
      $endpointSubsmissionStatus = '/api/v1/integration/tpm/submission/status';
      $dataSubmissionStatus = [
        'redeem_code' => $input['redeem_code'],
        'scope' => $input['scope'],
        'sequence' => intval($input['sequence'])
      ];
      $submissionStatus = $this->sendRequestPrakerjaAPI($endpointSubsmissionStatus, $dataSubmissionStatus);

      $submissionStatus_json = json_decode($submissionStatus);
      if (!$submissionStatus_json->success) {
        Log::error($submissionStatus);
        return $this->ressError([
          'message' => 'Prakerja.Submission Status - ' . $submissionStatus_json->message,
          'data' => []
        ]);
      }

      $message = 'Successfully create User Lesson Answer';
      switch ($submissionStatus_json->data->assignment->status) {
        case 0:
          $message = 'Assignment belum submit';
          break;
        case 1:
          $message = 'Assignment sudah submit dan status pending';
          break;
        case 2:
          $message = 'Assignment sudah submit dan status sukses';
          break;
        case 3:
          $message = 'Assignment sudah submit dan status gagal';
          break;

        default:
          break;
      }

      return $this->ressSuccess([
        'message' => $message,
        // 'message' => 'Berhasil menyimpan jawaban',
        'data' => [
          'UserLessonAnswer' => $newUserLessonAnswer,
        ]
      ]);
    } catch (\Throwable $th) {
      DB::rollback();
      Log::error($th);
      return $this->ressError([
        'message' => $th->getMessage(),
        'data' => []
      ]);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
