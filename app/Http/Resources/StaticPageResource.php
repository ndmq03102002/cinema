<?php

namespace App\Http\Resources;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StaticPageResource extends JsonResource
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
            'category_id'       => $this->category_id,
            'typePage'          => $this->typePage,
            'name'              => $this->name,
            'key'               => $this->key,
            'position'          => $this->position,
            'url_thumbnail'     => $this->url_thumbnail,
            'url_link'          => $this->url_link,
            'status'            => $this->status,
            'content'           => $this->content,
            'meta_description'  => $this->meta_description,
            'meta_keyword'      => $this->meta_keyword,
            'thumbnail'         => $this->thumbnail ? CommonHelper::getUrlFile($this->thumbnail, Constant::PATH_UPLOAD) : null,
            'slider'            => $this->slider ? CommonHelper::getUrlFile($this->slider, Constant::PATH_UPLOAD) : null,
            'created_at'        => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'        => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
