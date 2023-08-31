<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeweyController extends Controller
{
    public function getIndex()
    {
        return view('sewey.index');
    }
}
?>