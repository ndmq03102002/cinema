<?php
namespace App\Http\Resources;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'is_pin'        => $this->is_pin,
            'description'   => $this->description,
            'content'       => $this->content,
            'keyword'       => $this->keyword,
            'thumbnail'     => $this->thumbnail ? CommonHelper::getUrlFile($this->thumbnail, Constant::PATH_UPLOAD) : null,
            'status'        => $this->status,
            'created_at'    => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'    => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
