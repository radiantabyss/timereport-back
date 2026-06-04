<?php
namespace App\Domains\Company\MemberAssociate\Validators;

use App\Models as Model;

class Validator
{
    public static function run($data, $id = null) {
        //check if item exists
        if ( $id ) {
            $item = Model\CompanyMemberAssociate::find($id);
            if ( !$item ) {
                return __('Company Member Associate not found.');
            }
        }

        //validate request params
        $validator = \Validator::make($data, [
            'company_member_id' => 'required',
            'percentage' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'date',
        ], [
            'company_member_id' => __('Company Member ID is required.'),
            'percentage' => __('Percentage is required.'),
            'start_date.required' => __('Start Date is required.'),
            'start_date.date' => __('Start Date is invalid.'),
            'end_date.date' => __('End Date is invalid.'),
        ]);

        if ( $validator->fails() ) {
            return $validator->messages();
        }

        //check if start date overlaps with other intervals
        $has_overlap = Model\CompanyMemberAssociate::where('company_member_id', $data['company_member_id'])
            ->where('start_date', '>=', $data['start_date'])
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
            return __('Start date overlaps with other associate interval.');
        }

        //check if end date overlaps with other intervals
        if ( isset($data['end_date']) && $data['end_date'] ) {
            if ( $data['start_date'] > $data['end_date'] ) {
                return __('Start date cannot be greater than end date.');
            }

            $has_overlap_end = Model\CompanyMemberAssociate::where('company_member_id', $data['company_member_id'])
                ->where('start_date', '>=', $data['end_date'])
                ->where('end_date', '<=', $data['end_date'])
                ->whereNotNull('end_date')
                ->first();

            if ( $has_overlap_end ) {
                return __('End date overlaps with other associate interval.');
            }
        }

        return true;
    }
}
