<?php

namespace App\Mail;

use App\Models\Ingredient ;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IngredientAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $ingredient;

    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }

    public function build()
    {
        return $this->subject('Ingredient Alert: ' . $this->ingredient->name)
        ->view('emails.ingredient_alert');;
    }
}