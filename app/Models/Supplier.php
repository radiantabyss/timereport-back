<?php
namespace App\Models;

class Supplier extends Model
{
    use Traits\TeamExclusivity;

    protected $table = 'supplier';
}
