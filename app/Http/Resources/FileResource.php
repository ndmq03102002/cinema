<?php
namespace App\Http\Resources;

use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FileResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'          => $this->name,
            'url'           => $this->url,
            'url_full'      => !empty($this->url) ? CommonHelper::getUrlFile($this->url) : null,
            'size'          => $this->size,
            'mime_type'     => $this->mime_type,
            'folder_id'     => $this->folder_id,
            'user_id'       => $this->user_id,
            'created_at'    => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'    => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
