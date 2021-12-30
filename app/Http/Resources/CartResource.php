<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {




        return [
            "id" => $this->id,
            "quantity" => $this->quantity,
            "subtotal"=>$this->quantity*$this->cake->price,
            "cake"=>new CakeResponse($this->cake),
        ];
    }
}
