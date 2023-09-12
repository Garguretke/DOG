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
        // $products = Product::selectRaw('*');
        return view('emeal.products');
    }

    public function eMealAddProducts(Request $request)
    {
        $post = new Product();
        $post->name = $request->name;
        $post->quantity = $request->quantity;
        $post->save();
        return redirect('/emeal/products')->with('status', 'Blog Post Form Data Has Been inserted');
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

    public function create(Recipe $recipe)
    {
        $recipe = new Recipe(); // Tworzenie nowej instancji przepisu
        $products = Product::all();
        return view('emeal.recipes-create', compact('recipe', 'products'));
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
        $products = Product::all(); // Przykład pobrania wszystkich produktów z bazy danych
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
            'product' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'recipe_id' => 'required|exists:recipes,id',
        ]);
    
        $product = Product::find($request->input('product'));
        $quantity = $request->input('quantity');
        $recipe = Recipe::find($request->input('recipe_id'));
    
        // Użyj relacji, aby dodać produkt do przepisu w tabeli product_recipe
        $recipe->products()->attach($product, ['quantity' => $quantity]);
    
        return redirect()->route('emeal.recipes-edit', $recipe->id)->with('success', 'Product added to recipe successfully.');
    }

}