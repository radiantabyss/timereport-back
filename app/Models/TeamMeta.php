<?php
namespace App\Models;

class TeamMeta extends Model
{
    protected $table = 'team_meta';
    public $timestamps = false;

    public function team() {
        return $this->belongsTo(Team::Model);
    }
}
