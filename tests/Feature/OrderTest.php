<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

        public function testStoreOrder()
        {
            // Create ingredients
            $beef =Ingredient::factory()->create(['name' => 'Beef', 'stock' => 20, 'original_stock' => 20]);
            $cheese = Ingredient::factory()->create(['name' => 'Cheese', 'stock' => 5, 'original_stock' => 5]);
            $onion = Ingredient::factory()->create(['name' => 'Onion', 'stock' => 1, 'original_stock' => 1]);
    
            // Create a product using the ingredients
            $burger = Product::factory()->create(['name' => 'Burger']);
            $burger->ingredients()->attach([$beef->id, $cheese->id, $onion->id]);
    
            // Store the order
            $response = $this->postJson('/api/orders', [
                'product_id' => $burger->id,
                'quantity' => 1,
            ]);
    
            // Assert the response status and contents
            $response->assertStatus(201);
            $response->assertJsonStructure([
                'id', 'product_id', 'quantity', 'total_price', 'created_at', 'updated_at'
            ]);
    
            // Assert that the ingredients stocks were updated
            $this->assertDatabaseHas('ingredients', [
                'id' => $beef->id,
                'stock' => 19
            ]);
            $this->assertDatabaseHas('ingredients', [
                'id' => $cheese->id,
                'stock' => 4
            ]);
            $this->assertDatabaseHas('ingredients', [
                'id' => $onion->id,
                'stock' => 0
            ]);
        }

}
