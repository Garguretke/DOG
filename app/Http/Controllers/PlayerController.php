<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PlayerController extends Controller
{
    public function getIndex($series)
    {
        switch ($series) {
            case 'player.shippuuden':
                $tableName = 'shippuuden';
                break;
            case 'player.boruto':
                $tableName = 'boruto';
                break;
            default:
                $tableName = 'naruto';
        }

        $episodes = DB::table($tableName)->get();

        return view('player.index', compact('episodes'));
    }
}
