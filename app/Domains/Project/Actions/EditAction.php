<?php
namespace App\Domains\Project\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Project\Presenters\EditPresenter;

class EditAction extends Action
{
    public function run($id) {
        $item = Model\Project::find($id);

        if ( !$item ) {
            return Response::error(\Domain::name().' not found.');
        }

        $item = EditPresenter::run($item);

        return Response::success(compact('item'));
    }
}
