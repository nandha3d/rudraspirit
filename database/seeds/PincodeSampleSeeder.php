<?php

namespace Database\Seeders;

use App\Models\Pincode;
use Illuminate\Database\Seeder;

/**
 * A tiny real sample so the pincode lookup works out of the box before the
 * full ~155k-row India Post CSV is imported via `php artisan pincode:import`.
 */
class PincodeSampleSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['pincode' => '110001', 'office_name' => 'Connaught Place S.O', 'office_type' => 'SO', 'district' => 'Central Delhi', 'state' => 'Delhi'],
            ['pincode' => '400001', 'office_name' => 'Mumbai GPO', 'office_type' => 'GPO', 'district' => 'Mumbai', 'state' => 'Maharashtra'],
            ['pincode' => '560001', 'office_name' => 'Bangalore GPO', 'office_type' => 'GPO', 'district' => 'Bangalore', 'state' => 'Karnataka'],
            ['pincode' => '600001', 'office_name' => 'Chennai GPO', 'office_type' => 'GPO', 'district' => 'Chennai', 'state' => 'Tamil Nadu'],
            ['pincode' => '700001', 'office_name' => 'Kolkata GPO', 'office_type' => 'GPO', 'district' => 'Kolkata', 'state' => 'West Bengal'],
            ['pincode' => '500001', 'office_name' => 'Hyderabad GPO', 'office_type' => 'GPO', 'district' => 'Hyderabad', 'state' => 'Telangana'],
            ['pincode' => '380001', 'office_name' => 'Ahmedabad GPO', 'office_type' => 'GPO', 'district' => 'Ahmedabad', 'state' => 'Gujarat'],
            ['pincode' => '302001', 'office_name' => 'Jaipur GPO', 'office_type' => 'GPO', 'district' => 'Jaipur', 'state' => 'Rajasthan'],
            ['pincode' => '226001', 'office_name' => 'Lucknow GPO', 'office_type' => 'GPO', 'district' => 'Lucknow', 'state' => 'Uttar Pradesh'],
            ['pincode' => '641001', 'office_name' => 'Coimbatore H.O', 'office_type' => 'HO', 'district' => 'Coimbatore', 'state' => 'Tamil Nadu'],
            ['pincode' => '682001', 'office_name' => 'Ernakulam H.O', 'office_type' => 'HO', 'district' => 'Ernakulam', 'state' => 'Kerala'],
            ['pincode' => '751001', 'office_name' => 'Bhubaneswar GPO', 'office_type' => 'GPO', 'district' => 'Khordha', 'state' => 'Odisha'],
        ];

        foreach ($samples as $s) {
            Pincode::updateOrCreate(
                ['pincode' => $s['pincode'], 'office_name' => $s['office_name']],
                $s
            );
        }
    }
}
