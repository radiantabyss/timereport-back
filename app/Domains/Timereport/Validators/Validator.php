<?php
namespace App\Domains\Timereport\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = false) {
        //check if item exists
        if ( $id ) {
            $item = Model\Timereport::find($id);

            if ( !$item ) {
                return to_words(\Domain::name()).' not found.';
            }

            if ( \Gate::denies('delete-timereport', $item) ) {
                return 'Entry can\'t be edited anymore.';
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'client_id' => 'required',
            'project_id' => 'required',
            'date' => 'required',
            'hours' => 'required',
        ], [
            'client_id.required' => 'Client is required',
            'project_id.required' => 'Project is required',
            'date.required' => 'Date is required',
            'hours.required' => 'Hours Amount is required',
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        if ( \Gate::denies('add-timereport') ) {
            return 'Not allowed.';
        }

        //validate client
        $client = Model\Client::find($data['client_id']);
        if ( !$client ) {
            return 'Client not found.';
        }

        //validate project
        $project = Model\Project::find($data['project_id']);
        if ( !$project ) {
            return 'Project not found.';
        }

        return true;
    }
}
