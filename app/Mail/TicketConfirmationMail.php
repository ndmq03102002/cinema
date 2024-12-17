<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $username;
    public $ticketCode;
    public $seatList;
    public $cinema_name;
    public $movie_name;
    public $show_time;
    public $total;
    public $room;
    public function __construct($username, $ticketCode, $seatList, $cinema_name, $movie_name,$room, $show_time, $total)
    {
        $this->username = $username;
        $this->ticketCode = $ticketCode;
        $this->seatList = $seatList;
        $this->cinema_name = $cinema_name;
        $this->movie_name = $movie_name;
        $this->show_time = $show_time;
        $this->total = $total;
        $this->room = $room;
    }
   

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Ticket Confirmation Mail',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mail.ticket_confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
