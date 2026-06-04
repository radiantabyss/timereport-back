<?php
namespace App\Domains\Project\Filters;

use RA\Filter as RA_Filter;

class Filter extends RA_Filter
{
    protected static $table = 'project';

    protected static function is_active($value) {
        if ( $value == '' ) {
            return;
        }

        if ( $value ) {
            self::$query->whereRelation('client', 'is_active', true)
                ->where('is_active', true);
        }
        else {
            self::$query->where(function($query) {
                $query->whereRelation('client', 'is_active', false);

                $query->orWhere(function($query) {
                    $query->whereRelation('client', 'is_active', true)
                        ->where('is_active', false);
                });
            });
        }
    }

    protected static function export($value) {}
    protected static function tab($value) {}
}
