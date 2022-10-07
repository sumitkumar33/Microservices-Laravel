<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalNotification extends Notification
{
    use Queueable;
    private $message;
    private $user_id;
    private $name;
    private $email;
    private $admin_name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($dump)
    {
        $this->message = $dump['message'];
        $this->user_id = $dump['user_id'];
        $this->name = $dump['name'];
        $this->email = $dump['email'];
        $this->admin_name = $dump['admin_name'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'admin_name' => $this->admin_name,
        ];
    }
}
