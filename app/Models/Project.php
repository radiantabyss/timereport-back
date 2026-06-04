<?php
namespace App\Models;

class Project extends Model
{
    use Traits\TeamExclusivity;

    protected $table = 'project';

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
