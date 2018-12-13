<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends AbstractModel
{
    use SoftDeletes;

    protected $fillable = ['name', 'bedrooms', 'default_rate', 'max_guests', 'tax_inclusive'];

    /**
     * The attributes that should be returned during API lookup.
     *
     * @var array
     */
    protected $api_fields = [];

    public function Property() {
        return $this->belongsTo(Property::class);
    }

    public function Availability() {
        return $this->hasMany(Availability::class);
    }

    public function Rates() {
        return $this->hasMany(Rate::class);
    }

}
