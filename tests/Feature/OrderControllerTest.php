<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreOrder()
    {
        Mail::fake();  
        $beef = Ingredient::factory()->create(['name' => 'Beef','stock' => 0, 'total_stock' => 20, 'unit'=> 'kg' ]);
        $cheese = Ingredient::factory()->create(['name' => 'Cheese', 'stock' => 0, 'total_stock' => 5, 'unit' => 'kg']);
        $onion = Ingredient::factory()->create(['name' => 'Onion', 'stock' => 0, 'total_stock' => 1, 'unit' => 'kg']);

        $burger = Product::factory()->create(['name' => 'Burger']);
        $burger->ingredients()->attach($beef, ['quantity' => 150]);
        $burger->ingredients()->attach($cheese, ['quantity' => 30]);
        $burger->ingredients()->attach($onion, ['quantity' => 20]);

        $payload = [
            'customer_id' => 1,
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

        // $this->assertDatabaseHas('orders', ['customer_id' => 1]);
        $this->assertDatabaseHas('ingredients', ['id' => $beef->id, 'stock' =>  150 * 2]);
        $this->assertDatabaseHas('ingredients', ['id' => $cheese->id, 'stock' => 30 * 2]);
        $this->assertDatabaseHas('ingredients', ['id' => $onion->id, 'stock' => 20 * 2]);

        // Mail::assertSent(LowStockAlert::class, function ($mail) use ($beef, $cheese, $onion) {
        //     return $mail->ingredient->id === $beef->id || $mail->ingredient->id === $cheese->id || $mail->ingredient->id === $onion->id;
        // });
        // Mail::assertSent(LowStockAlert::class, function ($mail) use ($beef) {
        //     return $mail->ingredient->id === $beef->id;
        // });
        // Mail::assertSent(LowStockAlert::class, function ($mail) use ($cheese) {
        //     return $mail->ingredient->id === $cheese->id;
        // });
        // Mail::assertSent(LowStockAlert::class, function ($mail) use ($onion) {
        //     return $mail->ingredient->id === $onion->id;
        // });
        // Mail::assertSent(LowStockAlert::class, 3);
        // Mail::assertNotSent(LowStockAlert::class, function ($mail) use ($beef) {
        //     return $mail->ingredient->id === $beef->id;
        // });

        
        $response = $this->json('POST', '/orders', $payload);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Order stored and ingredients updated successfully.']);

        
        // $this->assertDatabaseHas('orders', ['customer_id' => 1]);
        $this->assertDatabaseHas('ingredients', ['id' => $beef->id, 'stock' =>  150 * 4]);
        $this->assertDatabaseHas('ingredients', ['id' => $cheese->id, 'stock' => 30 * 4]);
        $this->assertDatabaseHas('ingredients', ['id' => $onion->id, 'stock' =>  20 * 4]);

        // Mail::assertNotSent(LowStockAlert::class, function ($mail) use ($beef) {
        //     return $mail->ingredient->id === $beef->id;
        // });
        // Mail::assertNotSent(LowStockAlert::class, function ($mail) use ($cheese) {
        //     return $mail->ingredient->id === $cheese->id;
        // });
        // Mail::assertNotSent(LowStockAlert::class, function ($mail) use ($onion) {
        //     return $mail->ingredient->id === $onion->id;
        // });
        // Mail::assertNotSent(LowStockAlert::class);
    }
}
