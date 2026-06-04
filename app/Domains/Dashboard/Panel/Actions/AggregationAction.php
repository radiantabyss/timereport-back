<?php
namespace App\Domains\Dashboard\Panel\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class AggregationAction extends Action
{
    public function run($id) {
        $item = Model\DashboardPanel::find($id);

        if ( !$item ) {
            return Response::error('Panel not found.');
        }

        $item->filters = decode_json($item->filters);
        $item->settings = decode_json($item->settings);

        $Aggregation = '\\App\\Domains\\Dashboard\\Panel\\Aggregations\\'.\Str::studly($item->type).'Aggregation';
        $data = $Aggregation::run($item);

        return Response::success($data);
    }
}
