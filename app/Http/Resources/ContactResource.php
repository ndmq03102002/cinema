<?php
namespace App\Http\Resources;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'email'                 => $this->email,
            'display_name'          => $this->display_name,
            'phone_number'          => $this->phone_number,
            'description'           => $this->description,
            'created_at'            => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'            => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
