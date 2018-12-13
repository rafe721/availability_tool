<?php

use App\Rate;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rate::insert([
            [
                "property_id" => 123,
                "room_id" => 1234,
                "start_date" => "2018-12-01",
                "end_date" => "2019-03-31",
                "rate" => 250
            ],
            [
                "property_id" => 123,
                "room_id" => 1234,
                "start_date" => "2019-04-01",
                "end_date" => "2019-05-31",
                "rate" => 200
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2018-12-01",
                "end_date" => "2019-02-28",
                "rate" => 300
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2019-03-01",
                "end_date" => "2019-05-31",
                "rate" => 275
            ]
        ]);
    }
}
