<?php
namespace App\Domains\Invoice\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Invoice\Presenters\Presenter;
use App\Domains\Invoice\Transformers\Transformer;
use App\Domains\Invoice\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);
        $window = $data['window'] ?? '';
        $project_id = $data['project_id'] ?? '';
        unset($data['window']);
        unset($data['project_id']);

        $item = Model\Invoice::create($data);
        $item = Presenter::run($item);

        //set invoice id to timereport entries
        if ( $window ) {
            $timereports = Model\Timereport::applyWindowConditions($window)
                ->where(function($query) use($item, $project_id) {
                    if ( $project_id ) {
                        $query->where('project_id', $project_id);
                    }
                    else {
                        $query->where('client_id', $item->client_id);
                    }
                })
                ->update([
                    'invoice_id' => $item->id,
                ]);
        }

        return Response::success(compact('item'));
    }
}
