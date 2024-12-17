<?php
namespace App\Http\Resources;

use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FolderResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'parent_id'     => $this->parent_id,
            'files'         => FileResource::collection($this->files),
            'created_at'    => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'    => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
