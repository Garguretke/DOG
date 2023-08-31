<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class eMealController extends Controller
{
    public function getIndex()
    {
        return view('emeal.index');
    }

    public function eMealProducts()
    {
        logger()->debug('eMealController->emeal.products');
        $products = Product::selectRaw('*');
        return view('emeal.products', compact('products'));
    }

    public function eMealProductsStore(Request $request)
    {
        logger()->debug('eMealController->emeal.products_store');
		$dane = $request->all();

		$bsq = Product::selectRaw('*');

		return BootstrapTableController::response($bsq,$dane);
	}

    public function eMealRecipes()
    {
        return view('emeal.recipes');
    }
}

