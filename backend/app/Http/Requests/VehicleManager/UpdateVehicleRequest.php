<?php

namespace App\Http\Requests\VehicleManager;

class UpdateVehicleRequest extends StoreVehicleRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_vehicle_type' => ['required','integer','min:1'],
            'vehicle_name' => ['nullable','string','max:50'],
            'vehicle_number' => ['required','string','max:20'],
            'vehicle_vin' => ['nullable','string','max:50'],
            'vehicle_year' => ['nullable','integer','min:1900','max:2100'],

            'id_vehicle_make' => ['nullable','integer','min:1'],
            'id_vehicle_model' => ['nullable','integer','min:1'],
            'id_color' => ['nullable','integer','min:1'],

            'owner' => ['nullable','string','max:100'],
            'insurance_provider' => ['nullable','integer','in:1,2,3,4'],
            'billing_active' => ['required','integer','in:0,1'],
            'billing_option' => ['nullable','string','max:20'],
            'monthly_cost' => ['nullable','numeric'],
            'daily_rental_rate' => ['nullable','numeric'],
            'weekly_rental_rate' => ['nullable','numeric'],
            'payment_dom' => ['nullable','integer','min:1','max:31'],


            'license_plate' => ['nullable','string','max:20'],
            'registration_state_id' => ['nullable','integer','min:1'],
        ];
    }
}
