<?php
namespace App\Domains\Statement\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Statement\Presenters\ListPresenter;
use App\Domains\Statement\Filters\Filter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\Statement::with('invoice', 'client');

        //apply filters
        $filters = \Request::all();
        $filters['order_by'] = $filters['order_by'] ?? 'date';
        $filters['order'] = $filters['order'] ?? 'desc';
        Filter::apply($query, $filters);

        //export results
        if ( isset($filters['export']) && $filters['export'] ) {
            $this->export($query);
        }

        //clone query to get totals
        $query_totals = clone $query;
        $query_totals_by_member = clone $query;

        //paginate
        $per_page = \Request::get('per_page') ?: config('settings.data_table_per_page');
        $paginated = $query->paginate($per_page);
        $items = ListPresenter::run($paginated->items());
        $total = $paginated->total();
        $pages = $paginated->lastPage();

        //get totals
        $totals = $this->totals($query_totals);
        $totals_by_member = $this->totalsByMember($query_totals_by_member);

        return Response::success(compact('items', 'total', 'pages', 'totals', 'totals_by_member'));
    }

    private function totals($query) {
        $results = $query->select([\DB::raw('SUM(amount) as amount'), 'type'])
            ->where('is_ignored', false)
            ->groupBy('type')
            ->reorder()
            ->get();

        $totals = [];
        foreach ( $results as $result ) {
            $totals[$result->type] = $result->amount;
        }

        return $totals;
    }

    private function totalsByMember($query) {
        $results = $query->select([\DB::raw('SUM(amount) as amount'), 'type', 'company_member_id'])
            ->where('is_ignored', false)
            ->groupBy('type', 'company_member_id')
            ->reorder()
            ->get();

        $company_members = keyBy(Model\CompanyMember::all());

        $totals = [];
        foreach ( $results as $result ) {
            if ( isset($company_members[$result->company_member_id]) ) {
                $company_member = $company_members[$result->company_member_id];
                $company_member_name = $company_member->short_name ?? $company_member->name;
            }
            else {
                $company_member_name = __('Split');
            }
            $totals[$company_member_name][$result->type] = $result->amount;
        }

        return $totals;
    }

    private function export($query) {
        $items = ListPresenter::run($query->get());

        $headers = [__('Date'), __('Source'), __('Type'), __('Amount'), __('Description'), __('Balance')];
        $values = $items->map(function($item) {
            return [
                $item->date,
                $item->source,
                __($item->type),
                $item->amount,
                $item->description,
                $item->balance,
            ];
        });

        export_to_csv($headers, $values, __(\Domain::name()).' '.($filters['window'] ?? ''));
    }
}
