<?php

namespace App\Queries\VehicleManager;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LookupsQuery
{
    public function run(): array
    {
        $vehicleTypes = DB::table('vehicle_types')
            ->select(['id_vehicle_type as id', 'vehicle_type as name'])
            ->orderBy('vehicle_type')
            ->get();

        $makes = DB::table('vehicle_makes')
            ->select(['id_vehicle_make as id', 'make_name as name'])
            ->orderBy('make_name')
            ->get();

        // models table name differs across envs: vehicle_models vs vehicle_model
        $models = collect([]);
        if (Schema::hasTable('vehicle_models')) {
            $models = DB::table('vehicle_models')
                ->select(['id_vehicle_model as id', 'id_vehicle_make as make_id', 'model_name as name'])
                ->orderBy('model_name')
                ->get();
        } elseif (Schema::hasTable('vehicle_model')) {
            $models = DB::table('vehicle_model')
                ->select(['id_vehicle_model as id', 'id_vehicle_make as make_id', 'model_name as name'])
                ->orderBy('model_name')
                ->get();
        }

        $states = collect([]);
        if (Schema::hasTable('us_states')) {
            $states = DB::table('us_states')
                ->select(['id as id', 'state_name as name', 'state_code as code'])
                ->orderBy('state_code')
                ->get();
        }

        $colors = collect([]);
        // your DB uses "colors" (plural)
        if (Schema::hasTable('colors')) {
            $colors = DB::table('colors')
                ->select(['id_color as id', 'color_name as name'])
                ->orderBy('color_name')
                ->get();
        }

        $billingOptions = collect([
            ['id' => 'No Billing',     'name' => 'No Billing'],
            ['id' => 'Daily',          'name' => 'Daily'],
            ['id' => 'Weekly',         'name' => 'Weekly'],
            ['id' => 'Monthly',        'name' => 'Monthly'],
            ['id' => 'Revenue Based',  'name' => 'Revenue Based'],
        ]);

        return [
            'vehicle_types' => $vehicleTypes,
            'makes' => $makes,
            'models' => $models,
            'states' => $states,
            'colors' => $colors,
            'billing_options' => $billingOptions,
        ];
    }
}
