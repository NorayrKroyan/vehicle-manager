<?php

namespace App\Http\Controllers\Api\VehicleManager;

use App\Http\Controllers\Controller;
use App\Queries\VehicleManager\AssignmentsQuery;
use Illuminate\Http\JsonResponse;

class AssignmentsController extends Controller
{
    public function index(int $id, AssignmentsQuery $q): JsonResponse
    {
        $rows = $q->forVehicle($id);
        return response()->json(['ok' => true, 'count' => count($rows), 'rows' => $rows]);
    }
}
