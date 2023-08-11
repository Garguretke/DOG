<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PlayerController extends Controller
{
    public function index($series)
    {
        switch ($series) {
            case 'shippuuden':
                $tableName = 'shippuuden';
                break;
            case 'boruto':
                $tableName = 'boruto';
                break;
            default:
                $tableName = 'naruto';
        }

        $episodes = DB::table($tableName)->get();

        return view('player.index', compact('episodes'));
    }
}
