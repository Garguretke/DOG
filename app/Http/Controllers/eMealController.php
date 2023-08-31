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
        logger()->debug('eMealController->emeal_products');
        $products = Product::selectRaw('*');
        return view('emeal.products', compact('products'));
    }

    public function emeal_recipes()
    {
        return view('emeal.recipes');
    }

    public function emeal_products_store(Request $request)
    {
        logger()->debug('eMealController->emeal_products_store');
		$dane = $request->all();

		$bsq = Product::selectRaw('*');

		return BootstrapTableController::response($bsq,$dane);
	}
}

