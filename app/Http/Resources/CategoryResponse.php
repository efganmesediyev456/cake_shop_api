<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CategoryResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $cakes=$this->whenLoaded('cakes');

        return [
            "id"=>$this->id,
            "name"=>$this->name,
            "cakes"=>CakeResponse::collection($this->whenLoaded('cakes')),
        ];
    }
}
