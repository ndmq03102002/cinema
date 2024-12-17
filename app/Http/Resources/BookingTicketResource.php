<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingTicketResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'ticket_id'             => $this->id,
            'booking_code'          => $this->booking_code,
            'name_booking'          => $this->name_booking,
            'phone_booking'         => $this->phone_booking,
            'email_booking'         => $this->email_booking,
            'seats'                 => $this->seat,
            'pick_up_point'         => $this->pick_up_point,
            'drop_point'            => $this->drop_point,
            'price'                 => $this->price,
            'status_payment'        => $this->status
        ];
    }
}
