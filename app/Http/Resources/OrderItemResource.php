<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $cake=$this->cake;
        $cake->image=asset('storage/'.$cake->image);
        return [
            "id"=>$this->id,
            "cake"=>$cake,
            "price"=>$this->price,
            "quantity"=>$this->quantity,
            "subtotal"=>number_format($this->quantity*$this->price,2),

        ];
    }
}
