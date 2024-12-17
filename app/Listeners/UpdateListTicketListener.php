<?php

namespace App\Listeners;

use App\Events\UpdateListTicketEvent;
use LRedis;

class UpdateListTicketListener
{

    /**
     * Handle the event.
     * @return void
     */
    public function handle(UpdateListTicketEvent $event)
    {
        $data = [
            'event' => 'UpdateTickets',
            'data' => [
                'listTicket' => $event->data,
            ]
        ];

        LRedis::publish('list-ticket' , json_encode($data));
    }
}
