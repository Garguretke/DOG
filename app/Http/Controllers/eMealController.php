<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\ProductRecipe;

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
        return redirect('/emeal/products');
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

    public function addProductsToRecipe(Request $request, $recipeId)
    {
        // Pobieramy dane z formularza
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $productRecipe = ProductRecipe::where('recipe_id', $recipeId)->where('product_id', $productId)->first();
        // Tworzymy nowy rekord w tabeli 'product_recipe'
        if(is_null($productRecipe)){
            $productRecipe = new ProductRecipe();
            $productRecipe->recipe_id = $recipeId;
            $productRecipe->product_id = $productId;
            $productRecipe->quantity = $quantity;
        }
        else {
            $productRecipe->quantity += $quantity;
        }
        $productRecipe->save();

        // Przekierowujemy użytkownika z powrotem do edycji przepisu
        return redirect()->route('emeal.recipes-edit', $recipeId)->with('success', 'Produkt został dodany do przepisu.');
    }

    public function eMealProductsRecipeStore(Request $request)
    {
		$dane = $request->all();

		$bsq = ProductRecipe::selectRaw('*');

		return BootstrapTableController::response($bsq,$dane);
	}

}