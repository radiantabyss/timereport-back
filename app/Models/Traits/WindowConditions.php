<?php
namespace App\Models\Traits;

trait WindowConditions
{
    public function scopeApplyWindowConditions($query, $window, $field = 'date') {
        $timezone = '';
        $parsed_window = \Date::parseWindow($window);

        if ( !$parsed_window ) {
            return;
        }

        if ( preg_match('/hour/', $window) ) {
            $amount = str_replace('last_', '', str_replace('_hours', '', $window));
            $date = date('Y-m-d');
            $created_at = date('Y-m-d H:i:s', strtotime('-'.$amount.' hours'));

            $query->whereRaw('DATE('.\Date::getTimezoneOffsetForSql($timezone, $field).') = "'.date('Y-m-d', strtotime($parsed_window['start'])).'"')
                ->whereRaw('DATE('.\Date::getTimezoneOffsetForSql($timezone, $field).') >= "'.$parsed_window['start'].'"');

            return;
        }

        $query->whereRaw('DATE('.\Date::getTimezoneOffsetForSql($timezone, $field).') >= "'.$parsed_window['start'].'"')
            ->whereRaw('DATE('.\Date::getTimezoneOffsetForSql($timezone, $field).') <= "'.$parsed_window['end'].'"');
    }
}
