<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Availability extends AbstractModel
{
    use SoftDeletes;

    protected $fillable = ['room_id', 'start_date', 'end_date', 'available'];

    /**
     * The attributes that should be returned during API lookup.
     *
     * @var array
     */
    protected $api_fields = [];

    public function Property() {
        return $this->belongsTo(Property::class);
    }

    public function Room() {
        return $this->belongsTo(Room::class);
    }
}
