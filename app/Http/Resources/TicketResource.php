<?php

namespace App\Http\Resources;

use App\Enums\Constant;
use App\Helpers\CommonHelper;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'code_ticket'           => $this->code_ticket,
            'info_address_start'    => new AddressResource($this->info_address_start),
            'info_address_end'      => new AddressResource($this->info_address_end),
            'user_id'               => $this->user_id,
            'user'                  => $this->user,
            'type'                  => $this->type,
            'info_type'             => $this->info_type,
            'type_cars'             => $this->type_cars,
            'info_type_car'         => $this->info_type_car,
            'name_booking'          => $this->name_booking,
            'phone_booking'         => $this->phone_booking,
            'email_booking'         => $this->email_booking,
            'number'                => $this->number,
            'time_start'            => $this->time_start,
            'booking_code'          => $this->booking_code,
            'date_start'            => $this->date_start,
            'seat'                  => $this->seat,
            'price'                 => $this->price,
            'pick_up_point'         => $this->pick_up_point,
            'drop_point'            => $this->drop_point,
            'status'                => $this->status,
            'created_at'            => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
            'updated_at'            => Carbon::parse($this->updated_at)->format('d-m-Y H:i:s'),
        ];
    }
}
