<?php

use App\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Property::create([
            "id" => 123,
		    "name" => "Lake Side Cottages",
		    "currency" => "NZD",
		    "tax" => 15,
		    "tax_inclusive" => true,
        ]);
    }
}
