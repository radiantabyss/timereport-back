<?php
namespace App\Domains\Index\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use RA\Revisioner;
use App\Models\Index as Model;
use App\Domains\Index\Filter;
use App\Domains\Index\Presenter;
use App\Domains\Index\Transformer;
use App\Domains\Index\Validator;

class IndexAction extends Action
{
    public function run() {
        return Response::success();
    }
}
