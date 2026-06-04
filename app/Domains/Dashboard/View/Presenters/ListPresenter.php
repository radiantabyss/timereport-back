<?php
namespace App\Domains\Dashboard\View\Presenters;

use App\Models as Model;

class ListPresenter
{
    public static function run($items) {
        if ( !count($items) ) {
            return [];
        }


        $panels = self::panels($items);
        foreach ( $items as $item ) {
            $item->panels = $panels[$item->id] ?? [];
        }

        return $items;
    }

    private static function panels($items) {
        $panels = Model\DashboardPanel::whereIn('dashboard_view_id', pluck($items))->get();
        $sorted = [];

        foreach ( $panels as $panel ) {
            $panel->filters = decode_json($panel->filters);
            $panel->settings = decode_json($panel->settings);
            $panel->layout = decode_json($panel->layout);
            $sorted[$panel->dashboard_view_id.'_'.$panel->layout['i']] = $panel;
        }

        ksort($sorted);

        $panels = groupBy($sorted, 'dashboard_view_id');
        return $panels;
    }
}
