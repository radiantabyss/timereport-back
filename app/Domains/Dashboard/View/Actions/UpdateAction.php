<?php
namespace App\Domains\Dashboard\View\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Dashboard\View\Presenters\Presenter;
use App\Domains\Dashboard\View\Transformers\Transformer;
use App\Domains\Dashboard\View\Validators\Validator;

class UpdateAction extends Action
{
    public function run($id) {
        $data = \Request::all();

        //validate request
        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data, $id);
        $layout = $data['layout'];
        unset($data['layout']);

        $item = Model\DashboardView::find($id);
        $item->update($data);
        $item = Presenter::run($item);

        foreach ( $layout as $panel_layout ) {
            Model\DashboardPanel::where('id', $panel_layout['id'])
                ->update([
                    'layout' => encode_json([
                        'x' => $panel_layout['x'],
                        'y' => $panel_layout['y'],
                        'w' => $panel_layout['w'],
                        'h' => $panel_layout['h'],
                        'i' => $panel_layout['i'],
                    ]),
                ]);
        }

        return Response::success(compact('item'));
    }
}
