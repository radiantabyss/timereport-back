<?php
namespace App\Models;

class Contract extends Model
{
    use Traits\TeamExclusivity;

    protected $table = 'contract';
}
