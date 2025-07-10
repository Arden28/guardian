<?php

namespace Arden28\Guardian\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorEmail extends Notification
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your 2FA Code')
            ->line('Your two-factor authentication code is: **' . $this->code . '**')
            ->line('This code will expire in ' . config('guardian.two_factor.code_expiry', 300) / 60 . ' minutes.');
    }
}