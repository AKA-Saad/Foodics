<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreOrder()
    {
        Mail::fake();
        Artisan::call('db:seed');

        $beef = Ingredient::where('name', 'Beef')->first();
        $cheese = Ingredient::where('name', 'Cheese')->first();
        $onion = Ingredient::where('name', 'Onion')->first();

        $burger = Product::factory()->create(['name' => 'Burger']);
        $burger->ingredients()->attach($beef, ['quantity' => 4000]);
        $burger->ingredients()->attach($cheese, ['quantity' => 30]);
        $burger->ingredients()->attach($onion, ['quantity' => 20]);

        $payload = [
            'products' => [
                [
                    'product_id' => $burger->id,
                    'quantity' => 2,
                ]
            ]
        ];


        $response = $this->json('POST', '/orders', $payload);
        $response->assertStatus(201);
        $response->assertJson(['message' => 'Order stored and ingredients updated successfully.']);

        $this->assertDatabaseHas('orders', ['id' => 1]);
        $this->assertDatabaseHas('ingredients', ['id' => $beef->id, 'stock' =>  4000 * 2]);
        $this->assertDatabaseHas('ingredients', ['id' => $cheese->id, 'stock' => 30 * 2]);
        $this->assertDatabaseHas('ingredients', ['id' => $onion->id, 'stock' => 20 * 2]);

        $response = $this->json('POST', '/orders', $payload);
        $response->assertStatus(201);
        $response->assertJson(['message' => 'Order stored and ingredients updated successfully.']);

        $this->assertDatabaseHas('orders', ['id' => 2]);
        $this->assertDatabaseHas('ingredients', ['id' => $beef->id, 'stock' =>  4000 * 4]);
        $this->assertDatabaseHas('ingredients', ['id' => $cheese->id, 'stock' => 30 * 4]);
        $this->assertDatabaseHas('ingredients', ['id' => $onion->id, 'stock' =>  20 * 4]);

        $response = $this->json('POST', '/orders', $payload);
        $response->assertStatus(201);
        $response->assertJson(['message' => 'Order stored and ingredients updated successfully.']);

        $this->assertDatabaseHas('orders', ['id' => 3]);
        $this->assertDatabaseHas('ingredients', ['id' => $beef->id, 'stock' =>  4000 * 6]);
        $this->assertDatabaseHas('ingredients', ['id' => $cheese->id, 'stock' => 30 * 6]);
        $this->assertDatabaseHas('ingredients', ['id' => $onion->id, 'stock' =>  20 * 6]);
    }
}
