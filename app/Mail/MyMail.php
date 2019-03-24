<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MyMail extends Mailable
{
    use Queueable, SerializesModels;
    public $message;
    public $mail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message,$mail)
    {
        $this->message=$message;
        $this->mail=$mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@mostshark-exp.com')->view('email.myemail');
    }
}
