<?php
namespace App\Models;

class ConversionRate extends Model
{
    use Traits\WindowConditions;

    protected $table = 'conversion_rate';
    public $timestamps = false;
}
