<?php
namespace App\Domains\Statement\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Statement\Validators\ImportValidator;

class ImportAction extends Action
{
    public function run() {
        $data = \Request::all();

        //validate request
        $validation = ImportValidator::run($data);
        if ( $validation !== true ) {
            return Response::error($validation);
        }

        //get company members
        $company_members = Model\CompanyMember::all();
        foreach ( $company_members as $company_member ) {
            $company_member->aliases = array_map(function($alias) {
                return trim($alias);
            }, explode(',', $company_member->aliases));
        }

        //get company member salaries
        $company_member_salaries = Model\CompanyMemberSalary::all()->map(function($salary) {
            if ( !$salary->end_date ) {
                $salary->end_date = date('Y-m-d', strtotime('+1 year'));
            }

            return $salary;
        });
        $company_member_salaries = groupBy($company_member_salaries, 'company_member_id');

        $csv_data = \Excel::toArray([], $data['file']);
        foreach ( $csv_data[0] as $i => $row ) {
            if ( $i == 0 ) {
                continue;
            }

            $amount = (float) str_replace(',', '.' , str_replace('.', '', $row[2]));
            $balance = (float) str_replace(',', '.' , str_replace('.', '', $row[10]));
            $description = $row[4]."\n".$row[6]."\n".$row[9];
            $source = $row[5] ?? $description;
            $account = $row[0];
            $date = date('Y-m-d', strtotime($row[1]));
            $currency = $row[3];
            $company_member_id = null;

            $type = 'income';
            if ( $amount < 0 ) {
                $type = 'expense';

                //check if it's withdrawal
                foreach ( $company_members as $company_member ) {
                    if ( in_array($source, $company_member->aliases) ) {
                        $type = 'withdrawal';
                        $company_member_id = $company_member->id;
                        break 1;
                    }
                }

                //check if it's salary
                if ( $type == 'withdrawal' ) {
                    foreach ( $company_member_salaries[$company_member_id] as $salary ) {
                        if ( \Date::isInRange($date, $salary->start_date, $salary->end_date) && $amount == $salary->amount ) {
                            $is_salary = true;
                        }
                    }
                }

                //check if it's tax
                if ( \Auth::user()->team->meta['country'] == 'RO' && \Str::contains($source, 'Bugetul de stat') ) {
                    $type = 'tax';
                }
            }

            $amount = abs($amount);

            Model\Statement::create(compact(
                'type', 'account', 'date', 'amount', 'currency', 'source',
                'description', 'balance', 'company_member_id',
            ));
        }

        return Response::success();
    }
}
