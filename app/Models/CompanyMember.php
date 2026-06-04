<?php
namespace App\Models;

class CompanyMember extends Model
{
    use Traits\TeamExclusivity;

    protected $table = 'company_member';
}
