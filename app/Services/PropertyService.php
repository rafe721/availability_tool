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

/* To share a common pattern of retrieval of Property information
 */
class PropertyService
{

    /* Returns property information matching the given criteria..
     * (WARNING parameter validation not sufficiently done)
     * @param $property_id      - int; id or property to query
     * @param $arrival_date     - String; starting date
     * @param $no_of_guests     - int; no of guests..
     *
     * @return                  - array containing property information and room information
     *                          (room data, rates and availability) matching search criteria..
     */
    public function searchProperty($property_id, $arrival_date, $no_of_guests)
    {
        // get property record...
        $property = Property::find($property_id)->toArray();

        // get all rooms of the property
        $rooms = Room::select(['id', 'name', 'default_rate', 'bedrooms', 'max_guests', 'tax_inclusive'])->where([
            ['property_id','=', $property['id']],
            ['max_guests','>=', $no_of_guests]
        ])->get()->toArray();

        // get list of room ids..
        $room_ids = $this->getColumnValueList($rooms, 'id');

        // get rates of rooms in room_id list, property and after the given date..
        $rates = Rate::select(['id', 'room_id', 'start_date', 'end_date', 'rate'])->where([
            ['property_id', $property['id']],
            ['start_date', ">=" , $arrival_date],
        ])->whereIn('room_id', $room_ids)->get()->toArray();

        // Get Availability of rooms in room_id list, property and after the given date..
        $availability = Availability::select(['room_id', 'start_date', 'end_date', 'available'])->where([
            ['property_id', $property['id']],
            ['start_date', ">=" , $arrival_date],
        ])->whereIn('room_id', $room_ids)->get()->toArray();

        // return relevant...
        return compact("property", "rooms", "rates", "availability");
    }

    /* Returns a list of column values present in a list of records (associative array)...
     * (WARNING parameter validation not sufficiently done)
     * @param $dict_list        - associative array containing list of records.
     * @param $column_name      - String; column key; column values in all records will be extracted into a list.
     * @param $isUnique         - boolean (default true); Should list contain unique column values.
     *
     * @return - list (array) containing values of @param $column_name in all records given by $dict_list;
     */
    private function getColumnValueList ($dict_list = array(), $column_name = "", $isUnique = true) {
        $columnValueList = array();
        foreach($dict_list as $dict_item) {
            $dict_item_array =  $dict_item;
            if (array_key_exists($column_name, $dict_item_array)) {
                $column_value = $dict_item_array[$column_name];
                if (!$isUnique || ($isUnique && !in_array($column_value, $columnValueList))) {
                    $columnValueList[] = $column_value;
                }
            }
        }
        return $columnValueList;
    }
}