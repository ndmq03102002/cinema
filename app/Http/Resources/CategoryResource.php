<?php
namespace App\Http\Resources;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'url'            => $this->url,
            'key'            => $this->key,
            'order'          => $this->order,
            'icon'           => $this->icon ? CommonHelper::getUrlFile($this->icon, Constant::PATH_UPLOAD) : null,
            'parent_id'      => $this->parent_id,
            'childrens'      => CategoryResource::collection($this->childrens),
            'created_at'     => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'     => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
