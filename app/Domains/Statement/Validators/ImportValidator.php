<?php
namespace App\Domains\Statement\Validators;

use App\Models as Model;

class ImportValidator
{
    public static function run($data) {
        //validate request params
        $validator = \Validator::make($data, [
            'file' => 'required|file|mimes:xls,xlsx,csv,txt|max:20480',
        ], [
            'file.required' => __('File is required'),
            'file.mimes' => __('File type is not allowed. XLS, XLSX and CSV are allowed.'),
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        return true;
    }
}
