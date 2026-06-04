<?php
namespace App\Domains\Dashboard\Panel\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = false) {
        //check if item exists
        if ( $id ) {
            $item = Model::find($id);
            if ( !$item ) {
                return \Domain::name().' not found.';
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'name' => 'required',
            'display' => 'required',
            'dashboard_id' => 'required',
            'dashboard_view_id' => 'required',
        ], [
            'name' => 'Name is required',
            'display' => 'Display is required',
            'dashboard_id' => 'Please select a Dashboard',
            'dashboard_view_id' => 'Please select a Dashboard View',
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        return true;
    }
}
