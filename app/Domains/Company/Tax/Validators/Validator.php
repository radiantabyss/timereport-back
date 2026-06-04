<?php
namespace App\Domains\Company\Tax\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = null) {
        //check if item exists
        if ( $id ) {
            $item = Model\CompanyTax::find($id);
            if ( !$item ) {
                return __('Company Tax not found.');
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'income_tax' => 'required',
            'dividends_tax' => 'required',
            'vat' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'date',
        ], [
            'company_member_id' => __('Company Member ID is required.'),
            'amount' => __('Amount is required.'),
            'tax_amount' => __('Tax Amount is required.'),
            'start_date.required' => __('Start Date is required.'),
            'start_date.date' => __('Start Date is invalid.'),
            'end_date.date' => __('End Date is invalid.'),
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        //check if start date overlaps with other intervals
        $has_overlap = Model\CompanyTax::where(function($query) use($data) {
                if ( !isset($data['end_date']) || !$data['end_date'] ) {
                    return;
                }

                $query->where('start_date', '>=', $data['start_date'])
                    ->where('start_date', '<=', $data['end_date']);
            })
            ->orWhere(function($query) use($data) {
                $query->where('start_date', '>=', $data['start_date'])
                    ->where('end_date', '<=', $data['start_date'])
                    ->whereNotNull('end_date');
            })
            ->orWhere(function($query) use($data) {
                $query->where('start_date', '<=', $data['start_date'])
                    ->where('end_date', '>=', $data['start_date'])
                    ->whereNotNull('end_date');
            })
            ->first();

        if ( $has_overlap ) {
            return __('Start date overlaps with other interval.');
        }

        //check if end date overlaps with other salaries
        if ( isset($data['end_date']) && $data['end_date'] ) {
            if ( $data['start_date'] > $data['end_date'] ) {
                return __('Start date cannot be greater than end date.');
            }

            $has_overlap_end = Model\CompanyTax::where('start_date', '>=', $data['end_date'])
                ->where('end_date', '<=', $data['end_date'])
                ->whereNotNull('end_date')
                ->first();

            if ( $has_overlap_end ) {
                return __('End date overlaps with other interval.');
            }
        }

        return true;
    }
}
