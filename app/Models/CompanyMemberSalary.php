<?php
namespace App\Models;

class CompanyMemberSalary extends Model
{
    use Traits\TeamExclusivity;

    protected $table = 'company_member_salary';
}
