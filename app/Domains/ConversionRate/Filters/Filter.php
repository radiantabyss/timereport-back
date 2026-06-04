<?php
namespace App\Domains\ConversionRate\Filters;

use RA\Filter as RA_Filter;

class Filter extends RA_Filter
{
    protected static $table = 'conversion_rate';

    protected static function window($value) {
        if ( $value == 'none' ) {
            return;
        }

        self::$query->applyWindowConditions($value);
    }
}
