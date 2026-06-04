<?php
namespace App\Domains\Company\Member\Presenters;

use App\Models as Model;

class ListPresenter
{
    public static function run($items) {
        $salaries = Model\CompanyMemberSalary::whereIn('company_member_id', pluck($items))
            ->orderBy('start_date', 'desc')
            ->get()
            ->groupBy('company_member_id');

        $associates = Model\CompanyMemberAssociate::whereIn('company_member_id', pluck($items))
            ->orderBy('start_date', 'desc')
            ->get()
            ->groupBy('company_member_id');

        foreach ( $items as $item ) {
            $item->current_salary = '';
            $item->current_salary_tax = '';

            $salary = $salaries[$item->id][0] ?? null;
            if ( $salary && (!$salary->end_date || $salary->end_date > date('Y-m-d')) ) {
                $item->current_salary = $salary->amount;
                $item->current_salary_tax = $salary->tax_amount;
            }

            $associate = $associates[$item->id][0] ?? null;
            if ( $associate && (!$salary->end_date || $salary->end_date > date('Y-m-d')) ) {
                $item->is_associate = true;
            }
        }


        return $items;
    }
}
