<?php

namespace App\Http\Controllers;

use App\Mail\IngredientAlert;
use App\Models\Customer;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    public function store(Request $request)
    {
        $products = $request->input('products');
        // $customer_name = $request->input('customer_name');
        // $customer_email = $request->input('customer_email');

        // set customer_name , customer_email values manully
        $customer_name = 'kareem';
        $customer_email = 'kareem@example.com';

        // Find or create Customer
        $customer = Customer::firstOrCreate(
            ['email' => $customer_email],
            ['name' => $customer_name]
        );


        // Create Order
        $order = Order::create(['customer_id' => $customer->id]);

        // Loop through products in order
        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $quantity = $product['quantity'];

            // Find product
            $productOrdered = Product::findOrFail($product_id);

            // Loop through ingredients in product which we ordered
            foreach ($productOrdered->ingredients as $ingredient) {
                $inital_stock = $ingredient->stock;
                // Calculate amount of ingredient used in product
                $ingredient_quantity = $ingredient->pivot->quantity * $quantity;

                // Update ingredient stock
                $ingredient->stock -= $ingredient_quantity;
                $ingredient->save();


                // Check if ingredient stock is below 50% and send email if necessary
                if (($ingredient->stock < (0.5 * $inital_stock) ) && !$ingredient->alert_sent) {
                    
                    // Send email alert
                    Mail::to('merchant@domain.com')->send(new IngredientAlert($ingredient));

                    // another way to send mail
                    Ingredient::send_alert_email($ingredient->name);

                    // dd($ingredient->alert_sent);
                    // Mark alert as sent
                    $ingredient->alert_sent = true;
                    $ingredient->save();
                    
                    // dd($ingredient->alert_sent);

                }
            }

            // Add product to order
            $order->products()->attach($product_id, ['quantity' => $quantity]);
        }

        return response()->json(['success' => true]);
    }




   
}


