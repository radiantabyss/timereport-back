<?php
namespace App\Models;

class CompanyTax extends Model
{
    use Traits\TeamExclusivity;

    protected $table = 'company_tax';
}
