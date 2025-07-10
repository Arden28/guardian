<?php

namespace Arden28\Guardian\Notifications;

use Illuminate\Notifications\Notification;

class TwoFactorSms extends Notification
{
    /**
     * The 2FA code.
     *
     * @var string
     */
    protected $code;

    /**
     * Create a new notification instance.
     *
     * @param string $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['sms']; // Custom SMS channel (handled by TwoFactorService)
    }
}