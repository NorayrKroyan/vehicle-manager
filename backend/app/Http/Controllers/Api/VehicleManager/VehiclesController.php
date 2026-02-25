<?php

namespace App\Http\Controllers\Api\VehicleManager;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleManager\StoreVehicleRequest;
use App\Http\Requests\VehicleManager\UpdateVehicleRequest;
use App\Queries\VehicleManager\VehiclesQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehiclesController extends Controller
{
    public function index(Request $request, VehiclesQuery $q): JsonResponse
    {
        $limit  = (int) $request->query('limit', 200);
        $page   = (int) $request->query('page', 1);
        $search = (string) $request->query('q', '');

        $type = $request->query('type', 'all'); // 'all'|1|2

        $res = $q->list($search, $limit, $type, $page);

        return response()->json([
            'ok'    => true,
            'count' => count($res['rows']),
            'rows'  => $res['rows'],
            'total' => $res['total'],
        ]);
    }

    public function show(int $id, VehiclesQuery $q): JsonResponse
    {
        $row = $q->one($id);
        if (!$row) return response()->json(['ok' => false, 'error' => 'Vehicle not found'], 404);

        return response()->json(['ok' => true, 'vehicle' => $row]);
    }

    public function store(StoreVehicleRequest $request, VehiclesQuery $q): JsonResponse
    {
        $payload = $this->normalizeBilling($request->validated());

        // TEMP: ensure vehicle_name is never null (DB requires it)
        $payload['vehicle_name'] = trim((string)($payload['vehicle_name'] ?? ''));
        if ($payload['vehicle_name'] === '') {
            $payload['vehicle_name'] = (string)($payload['vehicle_number'] ?? '');
        }

        $id = $q->insert($payload);

        return response()->json(['ok' => true, 'id' => $id]);
    }

    public function update(int $id, UpdateVehicleRequest $request, VehiclesQuery $q): JsonResponse
    {
        $payload = $this->normalizeBilling($request->validated());

        $payload['vehicle_name'] = trim((string)($payload['vehicle_name'] ?? ''));
        if ($payload['vehicle_name'] === '') {
            $payload['vehicle_name'] = (string)($payload['vehicle_number'] ?? '');
        }

        $q->update($id, $payload);

        return response()->json(['ok' => true]);
    }

    private function normalizeBilling(array $v): array
    {
        $billingActive = (int)($v['billing_active'] ?? 0);

        if ($billingActive !== 1) {
            $v['billing_option'] = 'No Billing';
            $v['monthly_cost'] = null;
            $v['daily_rental_rate'] = null;
            $v['weekly_rental_rate'] = null;
            $v['payment_dom'] = null;
            return $v;
        }

        $opt = (string)($v['billing_option'] ?? '');
        if (trim($opt) === '' || $opt === 'No Billing') {
            abort(response()->json([
                'ok' => false,
                'errors' => ['billing_option' => ['Billing Option is required when Process Rental and Billing is checked.']]
            ], 422));
        }

        return $v;
    }
}
