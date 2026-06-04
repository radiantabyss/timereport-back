<?php
namespace App\Domains\Company\Member\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = null) {
        //check if item exists
        if ( $id ) {
            $item = Model\CompanyMember::find($id);
            if ( !$item ) {
                return __('Company Member not found.');
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'user_id' => 'required',
            'name' => 'required',
        ], [
            'user_id' => __('User ID is required. Select the team member.'),
            'name' => __('Name is required'),
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        return true;
    }
}
