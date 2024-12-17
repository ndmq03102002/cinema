<?php
namespace App\Http\Resources;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RoadResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'road_name'             => $this->road_name,
            'category_name'         => $this->category->name,
            'start'                 => $this->start,
            'location_start'        => $this->location_start,
            'location_end'          => $this->location_end,
            'cost'                  => $this->cost,
            'unit_price'            => $this->unit_price,
            'distance'              => $this->distance,
            'specific_hours'        => $this->specific_hours,
            'image'                 => $this->image ? CommonHelper::getUrlFile($this->image, Constant::PATH_UPLOAD) : '',
            'created_at'            => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'            => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
