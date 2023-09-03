<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Recipe;

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

    // public function eMealRecipes()
    // {
    //     return view('emeal.recipes');
    // }

    public function index()
    {
        $recipes = Recipe::all();
        return view('emeal.recipes', compact('recipes'));
    }

    public function create()
    {
        return view('emeal.recipe-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $recipe = Recipe::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('emeal.recipes')->with('success', 'Recipe created successfully.');
    }

    public function show(Recipe $recipe)
    {
        return view('emeal.recipes-show', compact('recipe'));
    }

    public function edit(Recipe $recipe)
    {
        $products = Product::selectRaw('*'); // Przykład pobrania wszystkich produktów z bazy danych
        return view('emeal.recipes-edit', compact('recipe', 'products'));
    }    

    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $recipe->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('emeal.recipes')->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return redirect()->route('emeal.recipes')->with('success', 'Recipe deleted successfully.');
    }

    public function addProduct(Request $request, Recipe $recipe)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->input('product_id'));
        $quantity = $request->input('quantity');

        $recipe->addProduct($product, $quantity);

        return redirect()->route('emeal.recipes-show', $recipe->id)->with('success', 'Product added to recipe successfully.');
    }

}