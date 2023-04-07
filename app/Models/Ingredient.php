<?php

namespace App\Models;

use App\Mail\IngredientAlert;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\IngredientLowStockAlert;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'stock'];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

        public static function send_alert_email($ingredient_name)
    {
        // Create a message object with the email details
        $subject = 'Ingredient Alert';
        $body = "The stock level of $ingredient_name is below 50%.";
        Mail::raw($body, function ($message) use ($subject) {
            $message->to('merchant@example.com');
            $message->subject($subject);
        });
    }

    // public function updateStock($quantity)
    // {
    //     $this->stock -= $quantity;
    //     if ($this->stock <= $this->initial_stock * 0.5 && !$this->alert_sent) {
    //         Mail::to('merchant@example.com')->send(new IngredientAlert($this));
    //         $this->alert_sent = true;
    //     }
    //     $this->save();
    // }
}
