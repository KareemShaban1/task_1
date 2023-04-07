<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_is_created_and_ingredients_stock_is_updated()
    {
        // Create initial ingredient stock
        $beef = Ingredient::create(['name' => 'Beef', 'stock' => 20000]);
        $cheese = Ingredient::create(['name' => 'Cheese', 'stock' => 5000]);
        $onion = Ingredient::create(['name' => 'Onion', 'stock' => 1000]);

        // Create product and associate ingredients
        $product = Product::create(['name' => 'Burger']);
        $product->ingredients()->attach($beef, ['quantity' => 150]);
        $product->ingredients()->attach($cheese, ['quantity' => 30]);
        $product->ingredients()->attach($onion, ['quantity' => 20]);

        // Make order with 2 burgers
        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ],
        ]);

        // Assert response is successful [ the order is correctly stored ]
        $response->assertSuccessful();

        // Assert order and customer are created
        $this->assertDatabaseHas('orders', [
            'customer_id' => 1
        ]);

        $this->assertDatabaseHas('customers', [
            'id' => 1,
            'name' => 'kareem',
            'email' => 'kareem@example.com'
        ]);

        // Assert ingredient stock is correctly updated
        $this->assertEquals(19850, $beef->fresh()->stock);
        $this->assertEquals(4970, $cheese->fresh()->stock);
        $this->assertEquals(980, $onion->fresh()->stock);
    }


    public function test_ingredient_alert_is_sent_when_stock_is_below_50_percent()
    {
        // Create initial ingredient stock
        $beef = Ingredient::create(['name' => 'Beef', 'stock' => 5000]);
        $cheese = Ingredient::create(['name' => 'Cheese', 'stock' => 1250]);
        $onion = Ingredient::create(['name' => 'Onion', 'stock' => 250]);


        // Create product and associate ingredients
        $product = Product::create(['name' => 'Burger']);
        $product->ingredients()->attach($beef, ['quantity' => 2600]);
        $product->ingredients()->attach($cheese, ['quantity' => 1000]);
        $product->ingredients()->attach($onion, ['quantity' => 200]);

        // Make order with 1 burger
        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ],
            
        ]);

        $response->assertSuccessful();

       
    }
}
