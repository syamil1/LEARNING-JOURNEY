<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = ['A', 'B', 'C', 'D', 'E', 'F' , 'G',];

        foreach ($regions as $region) {
            DB::table('regions')->insert([
                'name' => 'Division ' . $region,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
