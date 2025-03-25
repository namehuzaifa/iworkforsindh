<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplyJobByEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject;
    public $isCompany;
    public $user;
    public $company;
    public $job;
    

    /**
     * Create a new message instance.
     */

     /**
     * Create a new message instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct($user, $company, $job, $isCompany)
    {
        $this->subject = "Applied for ". $this->job->title ." at ". $this->company->name;
        $this->isCompany    = $isCompany;
        $this->user         = $user;
        $this->company      = $company;
        $this->job          = $job;
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

        if ($this->isCompany) {
            return $this->subject($this->subject)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('mails.applyByEmailCompany');
        } else {
            return $this->subject($this->subject)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('mails.applyByEmail')->with([
                'job' => $this->job,
                'user' => $this->user,
                'company' => $this->company,
            ]);
        }
    }
}
