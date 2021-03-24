<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class PasswordResetNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
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
        $urlToResetForm = "http://lazzatools.dk/#/reset?resettoken=" . $this->token;
        return (new MailMessage)
            ->greeting(Lang::get('Hejsa'))
            ->subject(Lang::get('Gendannelse af adgangskode'))
            ->line(Lang::get('Du modtager denne email fordi du har anmodet om gendannelse af din adgangskode.'))
            ->action(Lang::get('Gendan din adgangskode'), $urlToResetForm)
            ->line(Lang::get('Hvis du ikke har andmodet om gendannelse af adgangskoden, så skal du intet foretage dig.'))
            ->salutation(Lang::get('Med venlig hilsen, praktikanten Edin fra produktudvikling ;)'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
