<?php

namespace Arden28\Guardian\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
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
            ->subject('Password Reset Request')
            ->line('You requested a password reset. Use the following token to reset your password:')
            ->line('**Token: ' . $this->token . '**')
            ->line('This token will expire in ' . config('guardian.password_reset.token_expiry', 3600) / 60 . ' minutes.')
            ->line('If you did not request a password reset, please ignore this email.');
    }
}