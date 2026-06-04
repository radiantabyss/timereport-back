<?php
namespace App\Domains\Dashboard\Panel\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;

class ChartAction extends Action
{
    public function run($id) {
        $item = Model\DashboardPanel::find($id);

        if ( !$item ) {
            return Response::error('Panel not found.');
        }

        $item->filters = decode_json($item->filters);
        $item->settings = decode_json($item->settings);

        $Chart = '\\App\\Domains\\Dashboard\\Panel\\Charts\\'.\Str::studly($item->type).'Chart';
        $data = $Chart::run($item);

        return Response::success($data);
    }
}
