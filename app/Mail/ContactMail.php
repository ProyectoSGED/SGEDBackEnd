<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    private $message;
    private $sender;
    private $name;
    private $phone;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $sender, $name, $phone)
    {
        $this->message = $message;
        $this->sender = $sender;
        $this->name = $name;
        $this->phone = $phone;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from($this->sender, $this->name)
            ->subject("Nuevo mensaje de contacto")
            ->view('mails.contactMail')
            ->text('mails.contactMailPlain')
            ->with([
                "contactMail" => $this->sender,
                 "bodyMessage" => $this->message,
                 "phone" => $this->phone,
                 "name" => $this->name
            ]);
    }
}
