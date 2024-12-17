<?php
namespace App\Http\Resources;

use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class FcmNotifyResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'title'          => $this->title,
            'content'          => $this->content,
            'created_at'    => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'    => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
