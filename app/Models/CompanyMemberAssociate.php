<?php
namespace App\Models;

class CompanyMemberAssociate extends Model
{
    use Traits\TeamExclusivity;

    protected $table = 'company_member_associate';
}
