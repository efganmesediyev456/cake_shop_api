<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            "tax" => $this->tax,
            "subtotal" => $this->subtotal,
            "total" => $this->total,
            "mobile" => $this->mobile,
            "address" => $this->address,
            "user"=>$this->user,
            "order_items"=>OrderItemResource::collection($this->orderItems),
        ];
    }
}
