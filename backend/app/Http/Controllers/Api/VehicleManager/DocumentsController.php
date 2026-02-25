<?php

namespace App\Http\Controllers\Api\VehicleManager;

use App\Http\Controllers\Controller;
use App\Queries\VehicleManager\DocumentsQuery;
use Illuminate\Http\JsonResponse;

class DocumentsController extends Controller
{
    public function index(int $id, DocumentsQuery $q): JsonResponse
    {
        $rows = $q->forVehicle($id);
        return response()->json(['ok' => true, 'count' => count($rows), 'rows' => $rows]);
    }
}
