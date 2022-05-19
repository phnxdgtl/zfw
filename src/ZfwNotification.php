<?php

namespace Sevenpointsix\Zfw;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App;

class ZfwNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $message; // The text of the message
    public $subject; // The subject of the message

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $subject = '')
    {
        $this->message = $message;

        if ($subject) {
            $this->subject = $subject;
        } else {
            $this->subject = 'Form from website';
        }
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $from = $this->getDefaultFromAddress();

        return $this->markdown('zfw.notification-email',[
                    'message' => $this->message,
                    'subject' => $this->subject
                ])
                ->from($from)
                ->subject($this->subject);
    }

    protected function getDefaultFromAddress() {
        switch (App::environment()) {
            case 'staging':
                $from = 'noreply@phoenixdigital.agency'; // Assumes we're using the staging Mailgun account
                break;
            case 'production':
            case 'local':
            default:
                $from       = env('MAIL_FROM_ADDRESS');
                break;
        }
        return $from;
    }
}
