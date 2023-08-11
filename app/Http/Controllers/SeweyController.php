<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeweyController extends Controller
{
    public function sewey()
    {
        return view('sewey.index');
    }
}
?>