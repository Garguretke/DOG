<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LiqCalcController extends Controller
{
    public function getIndex(Request $request)
    {
        return view('liqcalc.index');
    }
}
?>