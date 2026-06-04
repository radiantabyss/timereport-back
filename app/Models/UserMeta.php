<?php
namespace App\Models;

class UserMeta extends Model
{
    protected $table = 'user_meta';
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class);
    }
}
