<?php
namespace App\Models;

class Statement extends Model
{
    use Traits\TeamExclusivity;
    use Traits\WindowConditions;

    protected $table = 'statement';

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }
}
