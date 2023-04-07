<?php

namespace App\Notifications;

use App\Models\Ingredient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IngredientLowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ingredient;

    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ingredient Low Stock Alert')
            ->line('The stock level of ' . $this->ingredient->name . ' has fallen below 50%.')
            ->line('Current stock level: ' . $this->ingredient->stock . ' ' . $this->ingredient->unit)
            ->line('Please restock the ingredient as soon as possible.');
    }
}