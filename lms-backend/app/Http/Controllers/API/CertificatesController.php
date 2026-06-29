<?php

namespace App\Http\Controllers\API;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\BaseController;
use App\Models\Course;
use Illuminate\Support\Str;

class CertificatesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'course_category_id' => 'required',
        ]);

        // $course = Course::find($request->course_category_id);

        // $exp = explode(" ", $course->name);

        // if(sizeof($exp) > 1){
        //     $a = substr($exp[0], 0, 1);
        //     $b = substr($exp[1], 0, 1);
        //     $c = strtoupper($a). strtoupper($b);
        // }
        // if (sizeof($exp) == 1) {
        //     $c = substr($exp[0], 0, 1);
        // }

        // $uuid = $c.'-'.Str::uuid();

        try {
            $data = $user->userCertificate()->create([
                'course_category_id' => $request->course_category_id,
                // 'id' => $uuid,
            ]);
            return $this->ressSuccess([
                'message' => 'Successfully create Certificate',
                'data' => [
                    'Certificate' => $data,
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $certificate = Certificate::with(['course_category', 'user'])->find($id);
        if (empty($certificate)) {
            return $this->ressError([
                'message' => 'Certificate Point not found!',
                'data' => []
            ]);
        }
        // try {
        return $this->ressSuccess([
            'message' => 'Show Certificate',
            'data' => [
                'Certificate' => $certificate,
            ]
        ]);
        // } catch (\Throwable $th) {
        // Log::error($th);
        // return $this->ressError([
        //     'message' => $th->getMessage(),
        //     'data' => []
        // ]);
        // }
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
