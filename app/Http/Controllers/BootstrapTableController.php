<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

class BootstrapTableController extends Controller
{
    public static function response(Builder $query, array &$data,) : JsonResponse {

            $qc = clone($query->getQuery());
            $qc->groups = null;
            $count = $qc->count();
            $query = $query->offset(0)->limit(0);
            $results = [
                'rows' => $query->get()->toArray(),
                'total' => $count,
                'totalNotFiltered' => $count,
            ];
            return response()->json($results);
    

    }
}