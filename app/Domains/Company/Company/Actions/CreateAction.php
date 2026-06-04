<?php
namespace App\Domains\Company\Company\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Company\Company\Presenters\Presenter;
use App\Domains\Company\Company\Transformers\Transformer;
use App\Domains\Company\Company\Validators\Validator;

class CreateAction extends Action
{
    public function run() {
        $data = \Request::all();

        $validation = Validator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        $data = Transformer::run($data);

        if ( $this->noChangesMade($data) ) {
            return Response::success();
        }

        $item = Model\Company::create($data);

        //update client company id
        if ( $data['client_id'] ) {
            Model\Client::where('id', $data['client_id'])->update([
                'company_id' => $item->id,
            ]);
        }
        //update team company id
        else {
            Model\TeamMeta::updateOrCreate([
                'team_id' => \Auth::user()->team->id,
                'key' => 'company_id',
            ], [
                'team_id' => \Auth::user()->team->id,
                'key' => 'company_id',
                'value' => $item->id,
            ]);
        }

        return Response::success();
    }

    private function noChangesMade($data) {
        $current_company = null;

        //if is a client's company
        if ( $data['client_id'] ) {
            $client = Model\Client::find($data['client_id']);
            $current_company = Model\Company::find($client->company_id);
        }
        else if ( isset(\Auth::user()->team->meta['company_id']) ) {
            $current_company = Model\Company::find(\Auth::user()->team->meta['company_id']);
        }

        //company doesn't exist, then proceed to creating
        if ( !$current_company ) {
            return false;
        }

        $current_company = toArray($current_company);
        unset($current_company['id']);
        unset($current_company['created_at']);
        unset($current_company['team_id']);
        unset($current_company['updated_at']);
        unset($data['client_id']);

        //there were no changes made
        if ( $current_company == $data ) {
            return true;
        }

        return false;
    }
}
