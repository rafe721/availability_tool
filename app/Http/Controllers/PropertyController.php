<?php

namespace App\Http\Controllers;

use App\Property;
use App\Room;
use App\Rate;
use App\Availability;
use Illuminate\Http\Request;

class PropertyController extends Controller
{


    public function index (Request $request) {

        $property_record = Property::all()->first();

        $property_data = $property_record->toArray();
        $rooms_data = $property_record->Rooms; //->where(['max_guests', '>=', 5]);
//        $suitable_rooms = $property_record->getRoomIds();
        $availability_data = $property_record->Availability;
        $rates_data = $property_record->Rates;

        $suitable_rooms = Room::select(['id', 'name'])->where([
                ['property_id','=', 123],
                ['max_guests','>=', 2]
            ])->get();
        $suitable_rates = Rate::select(['id', 'room_id', 'start_date', 'end_date', 'rate'])->where([
            ['property_id', 123],
//            ['start_date', ">=" , date("Y-m-d")],
        ])->whereIn('room_id', [1234, 1235])->get();

        $data = [
            "property" => $property_data,
            "rooms" => $rooms_data,
            "rates" => $rates_data,
            "availability" => $availability_data,
            "suitable_rooms" => $suitable_rooms,
            "suitable_rates" => $suitable_rates,
        ];

        return response()->json($data);
    }
}
