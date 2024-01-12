<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotebookController extends Controller
{
    public function getIndex()
    {
        return view('notebook.index');
    }
}
?>