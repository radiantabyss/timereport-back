<?php
namespace App\Domains\Invoice\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = false) {
        //check if item exists
        if ( $id ) {
            $item = Model\Invoice::find($id);

            if ( !$item ) {
                return to_words(\Domain::name()).' not found.';
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'client_id' => 'required',
            'template_id' => 'required',
            'number' => 'required',
            'date' => 'required',
            'due_date' => 'required',
            'vat' => 'required',
            'currency' => 'required',
        ], [
            'client_id.required' => __('Client ID is required'),
            'template_id.required' => __('Template ID is required'),
            'number.required' => __('Number is required'),
            'date.required' => __('Date is required'),
            'due_date.required' => __('Due Date is required'),
            'vat.required' => __('VAT is required'),
            'currency.required' => __('Currency is required'),
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        //validate client company
        $company_id = \Auth::user()->team->meta['company_id'] ?? '';

        if ( !$company_id ) {
            return __('Add your company details first.');
        }

        $company = Model\Company::find($company_id);
        if ( !$company ) {
            return __('Add your company details first.');
        }

        //validate client
        $client = Model\Client::find($data['client_id']);
        if ( !$client ) {
            return __('Client not found.');
        }

        //validate client company
        if ( !$client->company_id ) {
            return __('Client doesn\'t have the company details added.');
        }

        $client_company = Model\Company::find($client->company_id);
        if ( !$client_company ) {
            return __('Client doesn\'t have the company details added.');
        }

        if ( isset($data['window']) && $data['window'] ) {
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
            $has_from_upwork = false;
            foreach ( $timereports as $timereport ) {
                if ( $timereport->invoice_id ) {
                    $has_invoiced = true;
                }

                if ( $timereport->is_upwork ) {
                    $has_from_upwork = true;
                }
            }

            if ( $has_invoiced ) {
                return __('Window contains already invoiced Timereport entries.');
            }

            if ( $has_from_upwork ) {
                return __('Window contains Timereport entries from Upwork.');
            }
        }

        if ( $data['currency'] != \Auth::user()->company->currency ) {
            //validate request params
            $validator = \Validator::make($data, [
                'conversion_rate' => 'required',
                'conversion_rate_date' => 'required',
            ], [
                'conversion_rate.required' => __('Conversion Rate is required'),
                'conversion_rate_date.required' => __('Conversion Rate Date is required'),
            ]);

            if ( $validator->fails() ) {
                return $validator->messages();
            }
        }

        return true;
    }
}
