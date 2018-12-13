<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends AbstractModel
{
    use SoftDeletes;

    protected $fillable = ['name', 'currency', 'tax', 'tax_inclusive'];

    /**
     * The attributes that should be returned during API lookup.
     *
     * @var array
     */
    protected $api_fields = [];

    public function Rooms() {
        return $this->hasMany(Room::class)->select(['name', 'bedrooms', 'max_guests', 'tax_inclusive']);
    }

    public function Rates() {
        return $this->hasMany(Rate::class)->select(['id', 'room_id', 'start_date', 'end_date', 'rate']);
    }

    public function Availability() {
        return $this->hasMany(Availability::class)->select(['room_id', 'start_date', 'end_date', 'available']);
    }

    public function getRoomsWithCapacity() {
        return Property::with(
            array(
                'Rooms'=>function($query){
                    $query->select('id', 'property_id')->where("max_guests", ">=", 5);
                },
            )
        )->get();
    }

    public function getRoomRatesAfter(int $room_id, $start_date){

    }

    public function getRoomAvailabilityAfter(int $room_id, $start_date){

    }
}
