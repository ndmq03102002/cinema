<?php

namespace App\Http\Resources;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'address'           => $this->address,
            'phone_1'           => $this->phone_1,
            'phone_2'           => $this->phone_2,
            'state_id'          => $this->state_id,
            'state'             => $this->state,
            'lat'               => $this->lat,
            'lon'               => $this->lon,
            'key'               => $this->key,
            'image'             => $this->image ? CommonHelper::getUrlFile($this->image, Constant::PATH_UPLOAD) : null,
            'content'           => $this->content,
            'meta_description'  => $this->meta_description,
            'meta_keyword'      => $this->meta_keyword,
            'created_at'        => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'        => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
