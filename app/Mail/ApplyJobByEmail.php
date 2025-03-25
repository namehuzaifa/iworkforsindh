<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplyJobByEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     */

     /**
     * Create a new message instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct($data)
    {
       $this->data = $data;
    }

     /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('mails.applyByEmail')
        //             ->with('data', $this->data);

        return $this->subject('Smtp Configuration Test')
        ->from(config('mail.from.address'), config('mail.from.name'))
        ->markdown('mails.applyByEmail');
    }
}
