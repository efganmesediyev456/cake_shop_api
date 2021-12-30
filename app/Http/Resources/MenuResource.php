<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $datas=[];
        foreach($this->images()->get(["name"]) as $d){
            $datas[]=["name"=>asset('storage/gallery/'.$d["name"])];
        }

        return [
            "id"=>$this->id,
            "name"=>$this->name,
            "images"=>$datas,
        ];
    }
}
