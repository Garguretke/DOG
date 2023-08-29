<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class eMealController extends Controller
{
    public function emeal_index()
    {
        return view('emeal.index');
    }

    public function emeal_products()
    {
        return view('emeal.products');
    }

    public function emeal_recipes()
    {
        return view('emeal.recipes');
    }
}