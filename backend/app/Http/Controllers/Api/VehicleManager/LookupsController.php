<?php

namespace App\Http\Controllers\Api\VehicleManager;

use App\Http\Controllers\Controller;
use App\Queries\VehicleManager\LookupsQuery;
use Illuminate\Http\JsonResponse;

class LookupsController extends Controller
{
    public function index(LookupsQuery $q): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'lookups' => $q->run(),
        ]);
    }
}
