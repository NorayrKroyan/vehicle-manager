<?php

namespace App\Queries\VehicleManager;

use Illuminate\Support\Facades\DB;

class VehiclesQuery
{
    /**
     * Keep columns EXACTLY as-is.
     * Adds pagination via offset, and returns total rows for UI.
     *
     * @return array{rows: array<int, array>, total: int}
     */
    public function list(string $q, int $limit, $type = 'all', int $page = 1): array
    {
        $q = trim($q);

        $limit = max(1, min($limit, 500));
        $page  = max(1, (int) $page);
        $offset = ($page - 1) * $limit;

        // Base query (NO column changes)
        $base = DB::table('vehicle as v')
            ->leftJoin('vehicle_types as vt', 'vt.id_vehicle_type', '=', 'v.id_vehicle_type')
            ->leftJoin('vehicle_makes as mk', 'mk.id_vehicle_make', '=', 'v.id_vehicle_make')
            ->leftJoin('vehicle_models as mdl', 'mdl.id_vehicle_model', '=', 'v.id_vehicle_model')
            ->where('v.is_deleted', 0);

        // ✅ apply type filter ALWAYS (even when q is empty)
        $typeStr = is_null($type) ? 'all' : (string) $type;
        if (in_array($typeStr, ['1', '2'], true)) {
            $base->where('v.id_vehicle_type', (int) $typeStr);
        }

        // search filter
        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->where('v.vehicle_name', 'like', "%{$q}%")
                    ->orWhere('v.vehicle_number', 'like', "%{$q}%")
                    ->orWhere('v.vehicle_vin', 'like', "%{$q}%")
                    ->orWhere('mk.make_name', 'like', "%{$q}%")
                    ->orWhere('mdl.model_name', 'like', "%{$q}%")
                    ->orWhere('vt.vehicle_type', 'like', "%{$q}%")
                    ->orWhere('v.owner', 'like', "%{$q}%")
                    ->orWhereRaw('CAST(v.vehicle_year AS CHAR) like ?', ["%{$q}%"]);
            });
        }

        // (count distinct vehicles to avoid accidental join-multiplication)
        $total = (int) (clone $base)->distinct('v.id_vehicle')->count('v.id_vehicle');

        // ✅ fetch paged rows (columns EXACTLY as you had them)
        $rows = (clone $base)
            ->select([
                'v.id_vehicle as id',
                'v.id_vehicle_type',
                'vt.vehicle_type as vehicle_type_name',

                'v.vehicle_name',
                'v.vehicle_number',
                'v.vehicle_vin',
                'v.vehicle_year',

                'v.id_vehicle_make',
                'mk.make_name as make_name',

                'v.id_vehicle_model',
                'mdl.model_name as model_name',

                'v.id_color',
                'v.owner',

                'v.insurance_provider',

                'v.billing_active',
                'v.billing_option',
                'v.monthly_cost',
                'v.daily_rental_rate',
                'v.weekly_rental_rate',
                'v.payment_dom',

                'v.license_plate',
                'v.registration_state_id',
            ])
            ->orderByDesc('v.id_vehicle')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(fn ($r) => (array) $r)
            ->toArray();

        return ['rows' => $rows, 'total' => $total];
    }

    public function one(int $id): ?array
    {
        $r = DB::table('vehicle as v')
            ->leftJoin('vehicle_types as vt', 'vt.id_vehicle_type', '=', 'v.id_vehicle_type')
            ->leftJoin('vehicle_makes as mk', 'mk.id_vehicle_make', '=', 'v.id_vehicle_make')
            ->leftJoin('vehicle_models as mdl', 'mdl.id_vehicle_model', '=', 'v.id_vehicle_model')
            ->where('v.is_deleted', 0)
            ->where('v.id_vehicle', $id)
            ->select([
                'v.id_vehicle as id',
                'v.id_vehicle_type',
                'vt.vehicle_type as vehicle_type_name',

                'v.vehicle_name',
                'v.vehicle_number',
                'v.vehicle_vin',
                'v.vehicle_year',

                'v.id_vehicle_make',
                'mk.make_name as make_name',

                'v.id_vehicle_model',
                'mdl.model_name as model_name',

                'v.id_color',
                'v.owner',

                'v.insurance_provider',

                'v.billing_active',
                'v.billing_option',
                'v.monthly_cost',
                'v.daily_rental_rate',
                'v.weekly_rental_rate',
                'v.payment_dom',

                'v.license_plate',
                'v.registration_state_id',
            ])
            ->first();

        return $r ? (array) $r : null;
    }

    public function insert(array $payload): int
    {
        $now = time();
        $payload['date_created'] = $payload['date_created'] ?? $now;
        $payload['date_modified'] = $payload['date_modified'] ?? $now;
        $payload['is_deleted'] = 0;

        return (int) DB::table('vehicle')->insertGetId($payload, 'id_vehicle');
    }

    public function update(int $id, array $payload): void
    {
        $payload['date_modified'] = time();
        DB::table('vehicle')->where('id_vehicle', $id)->update($payload);
    }
}
