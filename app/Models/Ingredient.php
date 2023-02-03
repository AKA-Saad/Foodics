<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{

    use HasFactory;
    /**
     * The products that use the ingredient.
     */

    protected $fillable = [
        'stock',
        'name',
        'unit'

    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }

    /**
     * Scope a query to only include ingredients with less than 50% stock.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowStock($query)
    {
       
        $ingredients = $this->where('merchant_email_sent' , false)->get()->filter(function ($item) {
            $half_total_stock = $item->unit == 'kg' ? ($item->total_stock * 1000) * 0.5 : $item->total_stock * 0.5;
            if ($half_total_stock <= $item->stock) {
                return $item;
            }
        });
        return $ingredients;
    }
}
