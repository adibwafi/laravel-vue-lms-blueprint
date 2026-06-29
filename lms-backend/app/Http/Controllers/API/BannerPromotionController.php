<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\BannerPromotion;
use Illuminate\Http\Request;
use App\Http\Requests\BannerPromotionRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PaginationResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BannerPromotionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $BannerPromotions = new PaginationResource(BannerPromotion::search($request)->paginate($request->limit));
        return $this->ressSuccess([
            'message' => 'Successfully get Banner Promotion',
            'data' => [
                'BannerPromotions' => $BannerPromotions,
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
     * @param  \Illuminate\Http\BannerPromotionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerPromotionRequest $request)
    {
        $request->validated();
        $input = $request->toArray();
        $banner = BannerPromotion::latest('created_at')->first();
        if ($banner) {
            $sorter = $banner->sorter + 1;
        } else {
            $sorter = 1;
        }
        if ($request->file('image')->isValid()) {
            $input['image'] = $request->image->store('images/banner-promotion', 's3');
        }
        try {
            $input['sorter'] = $sorter;
            $BannerPromotion = BannerPromotion::create($input);
            return $this->ressSuccess([
                'message' => 'Successfully create Banner Promotion',
                'data' => [
                    'BannerPromotion' => $BannerPromotion,
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
     * @param  \App\Models\BannerPromotion  $BannerPromotion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $BannerPromotion = BannerPromotion::find($id);
        if (empty($BannerPromotion)) {
            return $this->ressError([
                'message' => 'Banner promotion not found!',
                'data' => []
            ]);
        }
        try {
            return $this->ressSuccess([
                'message' => 'Show Banner Promotion',
                'data' => [
                    'BannerPromotion' => $BannerPromotion,
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
     * @param  \App\Models\BannerPromotion  $BannerPromotion
     * @return \Illuminate\Http\Response
     */
    public function edit(BannerPromotion $BannerPromotion)
    {
        try {
            return $this->ressSuccess([
                'message' => 'Edit Banner Promotion',
                'data' => [
                    'BannerPromotion' => $BannerPromotion,
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
     * @param  \App\Models\BannerPromotion  $BannerPromotion
     * @return \Illuminate\Http\Response
     */
    public function update(BannerPromotionRequest $request, $id)
    {
        $request->validated();
        $input = $request->toArray();
        $banner = BannerPromotion::find($id);
        if (empty($banner)) {
            return $this->ressError([
                'message' => 'Data banner cannot find!',
                'data' => []
            ]);
        }

        $input['image'] = empty($request->image) ? $banner->image : $request->image->store('images/banner-promotion', 's3');
        try {
            $BannerPromotionUpdate = BannerPromotion::updateOrCreate(['id' => $banner->id], $input);
            return $this->ressSuccess([
                'message' => 'Successfully update Banner Promotion',
                'data' => [
                    'BannerPromotion' => $BannerPromotionUpdate,
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
     * @param  \App\Models\BannerPromotion  $BannerPromotion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $BannerPromotion = BannerPromotion::find($id);
        if (empty($BannerPromotion)) {
            return $this->ressError([
                'message' => 'Cannot find data',
                'data' => []
            ]);
        }
        try {
            $oldSort = $BannerPromotion->sorter;
            BannerPromotion::where('sorter', $oldSort)->update(['sorter' => 0]);
            BannerPromotion::where('sorter', '>', $oldSort)->decrement('sorter', 1);
            $BannerPromotion->delete();
            return $this->ressSuccess([
                'message' => 'Successfully delete Banner Promotion',
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
