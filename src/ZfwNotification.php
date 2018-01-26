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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
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
                    'message'=>$this->message
                ])
                ->from($from)
                ->subject('Form from website');
    }

    protected function getDefaultFromAddress() {
        switch (App::environment()) {
            case 'staging':
                $from = 'noreply@phoenixdigital.agency'; // Assumes we're using the staging Mailgun account
                break;
            case 'production':
            case 'local':
            default:
                $app_url    = env('APP_URL');
                $parse_url  = parse_url($app_url);
                $host       = preg_replace('/^www/','',$parse_url['host']);
                $from       = 'noreply@'.$host;
                break;
        }
        return $from;
    }
}
