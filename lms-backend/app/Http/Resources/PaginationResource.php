<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'current_page' => $this->currentPage(),
            'page_size' => $this->count(),
            'total_page' => $this->lastPage(),
            'count' => $this->total(),
            'rows' => $this->items(),
        ];
    }
}
