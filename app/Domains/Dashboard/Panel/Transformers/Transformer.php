<?php
namespace App\Domains\Dashboard\Panel\Transformers;

use App\Models as Model;

class Transformer
{
    public static function run($data) {
        $data['type'] = $data['type'] ?? 'default';
        $data['filters'] = encode_json($data['filters']);

        $data = self::layout($data);
        $data = self::settings($data);

        return $data;
    }

    protected static function settings($data) {
        $allowed_settings = [
            'table' => ['visible_columns', 'show_pagination'],
            'chart' => ['type', 'has_multiple_metrics', 'metrics', 'metric', 'grouping', 'aggregation'],
            'aggregation' => ['metrics', 'aggregations'],
        ];

        $settings = decode_json($data['settings']);
        foreach ( $settings as $key => $value ) {
            if ( !in_array($key, $allowed_settings[$data['display']]) ) {
                unset($settings[$key]);
            }
        }

        if ( $data['display'] == 'chart' ) {
            if ( $settings['has_multiple_metrics'] ) {
                unset($settings['metric']);
                unset($settings['grouping']);
            }
            else {
                unset($settings['metrics']);

                if ( !$settings['grouping'] ) {
                    $settings['grouping'] = '';
                }
            }
        }

        $data['settings'] = encode_json($settings);

        return $data;
    }

    protected static function layout($data) {
        $items = Model\DashboardPanel::where('dashboard_view_id', $data['dashboard_view_id'])->get();

        $i = 0;
        foreach ( $items as $item ) {
            $layout = decode_json($item->layout);
            if ( $layout['i'] > $i ) {
                $i = $layout['i'];
            }
        }

        $data['layout'] = encode_json([
            'x' => 0,
            'y' => 0,
            'w' => 5,
            'h' => 3,
            'i' => $i,
        ]);

        return $data;
    }
}
