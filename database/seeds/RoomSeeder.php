<?php

use \App\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Room::insert([
            [
                "id" => 1234,
                "property_id" => 123,
                "name" => "Cottage One",
                "default_rate" => 300,
                "bedrooms" => 3,
                "max_guests" => 6,
                "tax_inclusive" => true,
            ],
            [
                "id" => 1235,
                "property_id" => 123,
                "name" => "Cottage Two",
                "default_rate" => 250,
                "bedrooms" => 2,
                "max_guests" => 4,
                "tax_inclusive" => false
            ]
        ]);
    }
}
