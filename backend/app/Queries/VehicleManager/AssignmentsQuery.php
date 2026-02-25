<?php

namespace App\Queries\VehicleManager;

use Illuminate\Support\Facades\DB;

class AssignmentsQuery
{
    public function forVehicle(int $vehicleId): array
    {
        // Join:
        // driver_vehicle_history.driver_id -> driver.id_driver -> contact.id_contact
        $rows = DB::table('driver_vehicle_history as h')
            ->leftJoin('driver as d', 'd.id_driver', '=', 'h.driver_id')
            ->leftJoin('contact as c', 'c.id_contact', '=', 'd.id_contact')
            ->where('h.vehicle_id', $vehicleId)
            ->orderByDesc('h.date_action')
            ->orderByDesc('h.id')
            ->select([
                'h.*',
                DB::raw("TRIM(CONCAT(COALESCE(c.first_name,''),' ',COALESCE(c.last_name,''))) as driver_name"),
            ])
            ->get()
            ->map(fn ($r) => (array)$r)
            ->toArray();

        // newest Assigned = CURRENT
        $found = false;
        foreach ($rows as &$r) {
            $isAssigned = strtolower((string)($r['action'] ?? '')) === 'assigned';
            $r['is_current'] = (!$found && $isAssigned) ? 1 : 0;
            if ($r['is_current'] === 1) $found = true;
        }

        return $rows;
    }
}
