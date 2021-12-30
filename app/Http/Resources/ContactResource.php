<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            "name" => $this->name,
            "subject" => $this->subject,
            "message" => $this->message,
            "email" => $this->email,
            "read_at" => $this->read_at,
            "created_at" => $this->created_at->format("Y-m-d H:i:s"),
            "diffforhumans" => $this->created_at->diffForHumans(),

        ];
    }
}
