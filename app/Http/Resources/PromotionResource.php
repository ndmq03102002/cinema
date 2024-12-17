<?php

namespace App\Http\Resources;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
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
            'id'                    => $this->id,
            'code'                  => $this->code,
            'name'                  => $this->name,
            'description'           => $this->description,
            'percent'               => $this->percent,
            'max_discount'          => $this->max_discount,
            'time_start'            => $this->time_start,
            'time_end'              => $this->time_end,
            'image'                 => $this->image ? CommonHelper::getUrlFile($this->image, Constant::PATH_UPLOAD) : null,
            'status'                => $this->status,
            'created_at'            => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'            => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
