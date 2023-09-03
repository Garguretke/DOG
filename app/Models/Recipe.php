<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'name', 'description',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity'); // Jeśli chcesz przechowywać ilość w relacji
    }

    public function addProduct(Product $product, $quantity)
    {
        // Dodawanie produktu do przepisu z określoną ilością
        $existingQuantity = $this->products()->where('product_id', $product->id)->value('quantity');

        if ($existingQuantity) {
            $quantity += $existingQuantity;
            $this->products()->updateExistingPivot($product->id, ['quantity' => $quantity]);
        } else {
            $this->products()->attach($product, ['quantity' => $quantity]);
        }
    }

    public function removeProduct(Product $product)
    {
        // Usuwanie produktu z przepisu
        $this->products()->detach($product);
    }

    public function updateProductQuantity(Product $product, $quantity)
    {
        // Aktualizacja ilości produktu w przepisie
        $this->products()->updateExistingPivot($product->id, ['quantity' => $quantity]);
    }

    public function getTotalQuantity()
    {
        // Oblicz całkowitą ilość wszystkich produktów w przepisie
        return $this->products->sum(function ($product) {
            return $product->pivot->quantity;
        });
    }
}
