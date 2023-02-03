<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The products that belong to the order.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    /**
     * Update the stock of the ingredients used by the products in the order.
     *
     * @return void
     */
    public function updateStock()
    {

        foreach ($this->products as $product) {
            foreach ($product->ingredients as $ingredient) {
                $ingredient->update([
                    'stock' => $ingredient->stock + ($ingredient->pivot->quantity * $product->pivot->quantity),
                ]);
            }
        }
    }
}
