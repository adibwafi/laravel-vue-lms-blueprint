<?php

namespace App\Http\Controllers\API;

use App\Models\ExamConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PaginationResource;
use App\Http\Controllers\API\BaseController;

class ExamConfigController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ExamConfig = new PaginationResource(ExamConfig::search($request)->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get Exam Config',
            'data' => [
                'ExamConfig' => $ExamConfig,
            ]
        ]);
    }

    public function indexUser(Request $request)
    {
        
        return $this->ressSuccess([
            'message' => 'Successfully get Exam Config',
            'data' => [
                'ExamConfig' => ExamConfig::all()->first(),
            ]
        ]);
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
        $request->validate([
            'time_exam' => 'required',
        ]);

        try {
            $data = ExamConfig::create($request->all());
            return $this->ressSuccess([
                'message' => 'Successfully create Exam Config',
                'data' => [
                    'ExamConfig' => $data,
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
     * @param  \App\Models\ExamConfig  $examConfig
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $examconfig = ExamConfig::find($id);
        if (empty($examconfig)) {
            return $this->ressError([
                'message' => 'Exam Config not found!',
                'data' => []
            ]);
        }
        try {
            return $this->ressSuccess([
                'message' => 'Show Exam Config',
                'data' => [
                    'ExamConfig' => $examconfig,
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExamConfig  $examConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamConfig $examConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamConfig  $examConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $examconfig = ExamConfig::find($id);

        $request->validate([
            'time_exam' => 'required',
        ]);

        if (empty($examconfig)) {
            return $this->ressError([
                'message' => 'Data exam cannot find!',
                'data' => []
            ]);
        }

        try {
            $examconfig->update($request->all());
            return $this->ressSuccess([
                'message' => 'Successfully update Exam',
                'data' => [
                    'ExamConfig' => $examconfig,
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExamConfig  $examConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $examconfig = ExamConfig::find($id);
        if (empty($examconfig)) {
            return $this->ressError([
                'message' => 'Cannot find data',
                'data' => []
            ]);
        }

        try {
            $examconfig->delete();
            return $this->ressSuccess([
                'message' => 'Successfully delete Exam Config',
                'data' => []
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->ressError([
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
}
