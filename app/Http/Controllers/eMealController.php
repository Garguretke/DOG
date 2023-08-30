<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class eMealController extends Controller
{
    public function emeal_index()
    {
        return view('emeal.index');
    }

    public function emeal_products()
    {
        $products = Product::all();
        return view('emeal.products', compact('products'));
    }

    public function emeal_recipes()
    {
        return view('emeal.recipes');
    }

    //public function emeal_products_store(Request $request)
    //{
    //    $request->validate([
    //        'name' => 'required|string|max:255',
    //        'quantity' => 'required|numeric',
    //    ]);
    
    //    Product::create([
    //        'name' => $request->input('name'),
    //        'quantity' => $request->input('quantity'),
    //        // Dodaj inne pola w zależności od potrzeb
    //    ]);
    
    //    return redirect()->route('emeal_products')->with('success', 'Product added successfully.');
    //}

    public function emeal_products_store(Request $request){
		$dane = $request->all();

		$bsq = Product::selectRaw('*');

		return BootstrapTableController::response($bsq,$dane);
	}
}

