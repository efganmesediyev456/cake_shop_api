<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CakeResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $images=$this->whenLoaded('images');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'image' => asset('storage/'.$this->image),
            'gallery'=>GalleryResponse::collection($images),
            'description' => $this->description,
            'created_at' => $this->created_at->diffForHumans(),
            "category"=>$this->category()->first(["id","name"]),
            "favourite_type"=>$this->when(Auth::guard("api")->check(),$this->favourite_type),
        ];
    }
}
