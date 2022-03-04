<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::query()->truncate();

        City::query()->insert([
            [
                'name'          => 'New York',
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'          => 'London',
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'          => 'Paris',
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'          => 'Berlin',
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'          => 'Tokyo',
                'created_at'    => Carbon::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
