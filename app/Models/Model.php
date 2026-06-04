<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as LaravelModel;

class Model extends LaravelModel
{
    protected $guarded = [
        'id', 'created_at', 'updated_at',
    ];

    protected function serializeDate(\DateTimeInterface $date): string {
        return $date->format('Y-m-d H:i:s');
    }
}
