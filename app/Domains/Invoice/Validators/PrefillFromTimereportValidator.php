<?php
namespace App\Domains\Invoice\Validators;

use App\Models as Model;

class PrefillFromTimereportValidator
{
    public static function run($data) {
        //validate request params
        $validator = \Validator::make($data, [
            'window' => 'required',
            'client_id' => 'required_without:project_id',
            'project_id' => 'required_without:client_id',
        ], [
            'window' => __('Window is required.'),
            'client_id' => __('Client or Project is required'),
            'project_id' => __('Client or Project is required'),
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        $timereports = Model\Timereport::applyWindowConditions($data['window'])
            ->where(function($query) use($data) {
                if ( $data['project_id'] ) {
                    $query->where('project_id', $data['project_id']);
                }
                else {
                    $query->where('client_id', $data['client_id']);
                }
            })
            ->get();

        if ( !count($timereports) ) {
            return __('Window doesn\'t contain any Timereport entries.');
        }

        $has_invoiced = false;
        foreach ( $timereports as $timereport ) {
            if ( $timereport->invoice_id ) {
                $has_invoiced = true;
            }
        }

        if ( $has_invoiced ) {
            return __('Window contains already invoiced Timereport entries.');
        }

        return true;
    }
}
