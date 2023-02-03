<?php

namespace App\Notifications;

use App\Models\Ingredient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Notifiable;

    /**
     * The ingredient with low stock.
     *
     * @var \App\Ingredient
     */
    public $ingredient;

    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }


    public function via($notifiable)
    {
        return ['mail'];
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
            ->subject('Low Stock Alert')
            ->line('The ingredient ' . $this->ingredient->name . ' is running low on stock.')
            ->line('Current stock: ' . $this->ingredient->stock . 'g')
            ->line('Total stock: ' . $this->ingredient->total_stock . ' ' . $this->ingredient->unit);
    }
}
