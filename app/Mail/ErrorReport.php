<?php

namespace app\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ErrorReport extends Mailable
{
    use Queueable, SerializesModels;
    private $data = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title,$data)
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
        return $this->view('emails.errorReport',['data'=>$this->data]);
    }
}
