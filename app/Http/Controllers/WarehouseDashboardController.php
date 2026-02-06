<?php

namespace App\Http\Controllers;

use App\Models\Material;

class WarehouseDashboardController extends Controller
{
    public function index()
    {
        $materialsBelowRop = Material::belowRop()->get();

        return response()->json([
            'materials_below_rop' => $materialsBelowRop,
            'total' => $materialsBelowRop->count(),
        ]);
    }
}
