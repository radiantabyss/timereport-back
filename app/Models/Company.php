<?php
namespace App\Models;

class Company extends Model
{
    use Traits\TeamExclusivity;

    protected $table = 'company';

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function state() {
        return $this->belongsTo(State::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }
}
