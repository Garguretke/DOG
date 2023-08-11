<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LiqCalcController extends Controller
{
    public function liqcalc()
    {
        return view('liqcalc.index');
    }
}
?>