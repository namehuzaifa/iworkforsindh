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
    public $resume;
    

    /**
     * Create a new message instance.
     */

     /**
     * Create a new message instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct($user, $company, $job, $resume, $isCompany)
    {
        $this->isCompany    = $isCompany;
        $this->user         = $user;
        $this->company      = $company;
        $this->job          = $job;
        $this->resume          = $resume;
        $this->subject = "Applied for ". $this->job->title ." at ". $this->company->name;
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
            return $this->subject('Candidate Application '. $this->job->title .' via I Work for Sindh')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('mails.applyByEmailCompany')->with([
                'jobTitle' => $this->job->title,
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'userPhone' => $this->user->phone,
                'userCVULR' => url('/'.$this->resume->file),
                'companyName' => $this->company->name,
            ]);
            
        } else {
            return $this->subject($this->subject)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('mails.applyByEmail')->with([
                'jobTitle' => $this->job->title,
                'userName' => $this->user->name,
                'companyName' => $this->company->name,
            ]);
        }
    }
}
