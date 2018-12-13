<?php

use App\Availability;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Availability::insert([
            [
                "property_id" => 123,
                "room_id" => 1234,
                "start_date" => "2018-12-01",
                "end_date" => "2018-12-15",
                "available" => true
            ],
            [
                "property_id" => 123,
                "room_id" => 1234,
                "start_date" => "2018-12-15",
                "end_date" => "2018-12-18",
                "available" => false
            ],
            [
                "property_id" => 123,
                "room_id" => 1234,
                "start_date" => "2018-12-18",
                "end_date" => "2018-12-22",
                "available" => true
            ],
            [
                "property_id" => 123,
                "room_id" => 1234,
                "start_date" => "2018-12-22",
                "end_date" => "2018-12-23",
                "available" => false
            ],
            [
                "property_id" => 123,
                "room_id" => 1234,
                "start_date" => "2018-12-23",
                "end_date" => "2018-12-24",
                "available" => true
            ],
            [
                "property_id" => 123,
                "room_id" => 1234,
                "start_date" => "2018-12-24",
                "end_date" => "2019-01-15",
                "available" => false
            ],
            [
                "property_id" => 123,
                "room_id" => 1234,
                "start_date" => "2019-01-15",
                "end_date" => "2019-03-31",
                "available" => true
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2018-12-01",
                "end_date" => "2018-12-14",
                "available" => false
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2018-12-14",
                "end_date" => "2018-12-18",
                "available" => true
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2018-12-18",
                "end_date" => "2018-12-20",
                "available" => false
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2018-12-20",
                "end_date" => "2018-12-21",
                "available" => true
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2018-12-21",
                "end_date" => "2018-12-23",
                "available" => false
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2018-12-23",
                "end_date" => "2018-12-24",
                "available" => true
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2018-12-24",
                "end_date" => "2019-01-10",
                "available" => false
            ],
            [
                "property_id" => 123,
                "room_id" => 1235,
                "start_date" => "2019-01-10",
                "end_date" => "2019-03-31",
                "available" => true
            ]
        ]);
    }
}
