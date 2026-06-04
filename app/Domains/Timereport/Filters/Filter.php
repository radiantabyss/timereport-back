<?php
namespace App\Domains\Timereport\Filters;

use RA\Filter as RAFilter;

class Filter extends RAFilter
{
    protected static $table = 'timereport';

    protected static function window($value) {
        if ( $value == 'none' ) {
            return;
        }

        self::$query->applyWindowConditions($value, 'timereport.date');
    }

    protected static function group_by_project($value) {
        if ( $value == 1 ) {
            self::$query->groupBy('project_id');
        }
    }

    protected static function group_by_user($value) {
        if ( $value == 1 ) {
            self::$query->groupBy('company_member_id');
        }
    }

    protected static function export($value) {}

    protected static function description($value) {
        self::$query->where('description', 'LIKE', '%'.$value.'%');
    }

    protected static function is_invoiced($value) {
        if ( $value == 0 ) {
            self::$query->whereNull('invoice_id');
        }
        else if ( $value == 1 ) {
            self::$query->whereNotNull('invoice_id');
        }
    }
}
