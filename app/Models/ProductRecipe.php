<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRecipe extends Model
{
    protected $table = 'product_recipe'; // Określamy nazwę tabeli

    protected $fillable = [
        'recipe_id', 'product_id', 'quantity', // Określamy kolumny, które można masowo przypisywać
    ];

    // Określamy relację z modelem 'Recipe'
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    // Określamy relację z modelem 'Product'
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
      
    protected function serializeDate(\DateTimeInterface $date){
		return $date->format('Y-m-d H:i:s');
	}

}