<?php

namespace Modules\Manager\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManagerCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $tmpPassword,
    ) {
        $this->onConnection('redis');
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Expelliarmus Management!')
            ->greeting('Hello, '.$notifiable->first_name.'!')
            ->line('Your account was successfully created.')
            ->line('Here your temporary password:')
            ->line("`{$this->tmpPassword}`")
            ->action('Login Now', config('app.frontend_url').'/management/manager/auth')
            ->line('Please, change your password after first login.')
            ->salutation('Regards, Expelliarmus Team.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }

    public function viaQueues(): array
    {
        return [
            'mail' => 'low',
        ];
    }
}
