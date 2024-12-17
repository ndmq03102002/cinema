<?php

namespace App\Http\Resources;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
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
            'email'             => $this->email,
            'display_name'      => $this->display_name,
            'phone_number'      => $this->phone_number,
            'roleId'           => $this->roleId,
            'role'              => $this->role,
            'status'            => $this->status,
            'detail_address'    => $this->detail_address,
            'avatar'            => $this->avatar ? CommonHelper::getUrlFile($this->avatar, Constant::PATH_UPLOAD) : null,
            'created_at'        => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'        => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
