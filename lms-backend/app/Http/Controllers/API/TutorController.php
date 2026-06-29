<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\Tutor;
use Illuminate\Http\Request;
use App\Http\Requests\TutorRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PaginationResource;
use Illuminate\Support\Facades\Storage;

class TutorController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $resource = new PaginationResource(Tutor::search($request)->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get tutor',
            'data' => [
                'tutors' => $resource,
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
     * @param  \Illuminate\Http\TutorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TutorRequest $request)
    {
        $request->validated();
        $input = $request->toArray();
        $input['image'] = $request->image->store('images/tutor', 's3');
        try {
            $tutor = Tutor::create($input);
            // $tutor['image'] = Storage::disk('s3')->url($tutor->image);
            return $this->ressSuccess([
                'message' => 'Successfully create tutor',
                'data' => [
                    'tutor' => $tutor,
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
     * @param  \App\Models\Tutor  $tutor
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tutor = Tutor::find($id);
        if (empty($tutor)) {
            return $this->ressError([
                'message' => 'Tutor not found!',
                'data' => []
            ]);
        }
        try {
            // $tutor['image'] = Storage::disk('s3')->url($tutor->image);
            return $this->ressSuccess([
                'message' => 'Show tutor',
                'data' => [
                    'tutor' => $tutor,
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
     * @param  \App\Models\Tutor  $tutor
     * @return \Illuminate\Http\Response
     */
    public function edit(Tutor $tutor)
    {
        try {
            return $this->ressSuccess([
                'message' => 'Edit tutor',
                'data' => [
                    'tutor' => $tutor,
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tutor  $tutor
     * @return \Illuminate\Http\Response
     */
    public function update(TutorRequest $request, $id)
    {
        $request->validated();
        $input = $request->toArray();

        $tutor = Tutor::find($id);
        if (empty($tutor)) {
            return $this->ressError([
                'message' => 'Data tutor cannot find!',
                'data' => []
            ]);
        }
        $input['image'] = empty($request->image) ? $tutor->image : $request->image->store('images/tutor', 's3');
        try {
            $tutorUpdate = Tutor::updateOrCreate(['id' => $tutor->id], $input);
            // $tutorUpdate['image'] = Storage::disk('s3')->url($tutorUpdate->image);
            return $this->ressSuccess([
                'message' => 'Successfully update tutor',
                'data' => [
                    'tutor' => $tutorUpdate,
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
     * @param  \App\Models\Tutor  $tutor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tutor = Tutor::find($id);
        if (empty($tutor)) {
            return $this->ressError([
                'message' => 'Cannot find data',
                'data' => []
            ]);
        }
        try {

            if ($tutor->course()->first()) {
                return $this->ressError([
                    'message' => 'Cannot delete, because tutor have relation with course',
                    'data' => []
                ]);
            }
            $tutor->delete();
            return $this->ressSuccess([
                'message' => 'Successfully delete tutor',
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
