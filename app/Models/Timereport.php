<?php
namespace App\Models;

class Timereport extends Model
{
    use Traits\TeamExclusivityWithClient;
    use Traits\WindowConditions;

    protected $table = 'timereport';

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }
}
