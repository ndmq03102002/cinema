<?php

namespace App\Listeners;

use App\Enums\Constant;
use App\Events\UpdateCarSeatEvent;
use LRedis;

class UpdateCarSeatListener
{

    /**
     * Handle the event.
     * @return void
     */
    public function handle(UpdateCarSeatEvent $event)
    {

        $data = [
            'event' => 'Booking',
            'data' => [
                'room' => $event->data['room'],
                'response' => json_encode($event->data['seats']),
            ]
        ];

        LRedis::set($event->data['ticket_id'], "Cancel Ticket", 'EX', Constant::TIME_CANCEL_TICKET);
        LRedis::publish('booking-channel' , json_encode($data));
    }
}
