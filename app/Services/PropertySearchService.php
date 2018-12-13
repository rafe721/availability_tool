<?php
/**
 * Created by PhpStorm.
 * User: Rahul
 * Date: 13/12/2018
 * Time: 10:13 AM
 */

namespace App\Services;

use App\Property;
use App\Room;
use App\Rate;
use App\Availability;

class PropertySearchService
{

    public function searchProperty($property_id, $arrival_date, $no_of_guests)
    {

        $property = Property::find($property_id)->toArray();

        $rooms = Room::select(['id', 'name', 'default_rate', 'bedrooms', 'max_guests', 'tax_inclusive'])->where([
            ['property_id','=', $property['id']],
            ['max_guests','>=', $no_of_guests]
        ])->get()->toArray();

        $room_ids = $this->getColumnValueList($rooms, 'id');

        $rates = Rate::select(['id', 'room_id', 'start_date', 'end_date', 'rate'])->where([
            ['property_id', $property['id']],
            ['start_date', ">=" , $arrival_date],
        ])->whereIn('room_id', $room_ids)->get()->toArray();

        $availability = Availability::select(['room_id', 'start_date', 'end_date', 'available'])->where([
            ['property_id', $property['id']],
            ['start_date', ">=" , $arrival_date],
        ])->whereIn('room_id', $room_ids)->get()->toArray();

        return compact("property", "rooms", "rates", "availability");
    }

    private function getColumnValueList ($dict_list = array(), $index = "", $isUnique = true) {
        $columnValueList = array();
        foreach($dict_list as $dict_item) {
            $dict_item_array =  $dict_item;
            if (array_key_exists($index, $dict_item_array)) {
                $column_value = $dict_item_array[$index];
                if (!$isUnique || ($isUnique && !in_array($column_value, $columnValueList))) {
                    $columnValueList[] = $column_value;
                }
            }
        }
        return $columnValueList;
    }
}