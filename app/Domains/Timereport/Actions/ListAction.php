<?php
namespace App\Domains\Timereport\Actions;

use Illuminate\Routing\Controller as Action;
use RA\Response;
use App\Models as Model;
use App\Domains\Timereport\Presenters\ListPresenter;
use App\Domains\Timereport\Filters\Filter;

class ListAction extends Action
{
    public function run() {
        //get query
        $query = Model\Timereport::leftJoin('company_member', 'company_member.id', '=', 'timereport.company_member_id')
            ->leftJoin('client', 'client.id', '=', 'timereport.client_id')
            ->leftJoin('project', 'project.id', '=', 'timereport.project_id')
            ->leftJoin('invoice', 'invoice.id', '=', 'timereport.invoice_id');

        //apply filters
        $filters = \Request::all();
        $filters['group_by_user'] = $filters['group_by_user'] ?? false;
        $filters['group_by_project'] = $filters['group_by_project'] ?? false;

        $this->select($query, $filters);
        $filters = $this->order($filters);
        Filter::apply($query, $filters);

        //export results
        if ( isset($filters['export']) && $filters['export'] ) {
            $this->export($query);
        }

        //clone query to get totals
        $query_totals = clone $query;

        //paginate
        $per_page = \Request::get('per_page') ?: config('settings.data_table_per_page');
        $paginated = $query->paginate($per_page);
        $items = ListPresenter::run($paginated->items());
        $total = $paginated->total();
        $pages = $paginated->lastPage();

        //get totals
        $totals = $this->totals($query_totals, $filters);

        return Response::success(compact('items', 'total', 'pages', 'totals'));
    }

    private function select($query, $filters) {
        $select = [];

        if ( $filters['group_by_project'] || $filters['group_by_user'] ) {
            $select[] = \DB::raw('sum(hours) as hours');
            $select[] = \DB::raw('SUM(hours * timereport.rate) as amount');
        }

        if ( $filters['group_by_project'] ) {
            $select[] = 'timereport.project_id';
            $select[] = 'project.name as project_name';
        }

        if ( $filters['group_by_user'] ) {
            $select[] = 'timereport.company_member_id';
            $select[] = 'company_member.name';
            $select[] = 'company_member.short_name';
        }

        if ( !$filters['group_by_user'] && !$filters['group_by_project'] ) {
            $select = [
                'timereport.*', 'company_member.name', 'company_member.short_name',
                'client.name as client_name', 'project.name as project_name',
                'invoice.series as invoice_series', 'invoice.number as invoice_number',
            ];
        }

        $query->select($select);
    }

    private function order($filters) {
        $filters['group_by_user'] = $filters['group_by_user'] ?? false;
        $filters['group_by_project'] = $filters['group_by_project'] ?? false;

        if ( $filters['group_by_project'] ) {
            $filters['order_by'] = 'project.name';
            $filters['order'] = 'asc';
        }

        if ( $filters['group_by_user'] ) {
            $filters['order_by'] = 'company_member.name';
            $filters['order'] = 'asc';
        }

        $filters['order_by'] = $filters['order_by'] ?? 'timereport.date';
        $filters['order'] = $filters['order'] ?? 'desc';

        return $filters;
    }

    private function totals($query, $filters) {
        $totals = [];

        $select = [\DB::raw('SUM(hours) as hours'), \DB::raw('SUM(hours * timereport.rate) as amount'), 'timereport.currency'];

        if ( $filters['group_by_user'] ) {
            $select[] = 'company_member_id';
            $query->groupBy('company_member_id');
        }

        if ( $filters['group_by_project'] ) {
            $select[] = 'project_id';
            $query->groupBy('project_id');
        }

        $query->select($select);
        $query->groupBy('currency');
        $query->reorder();

        $totals = $query->get();

        return $totals;
    }

    private function export($query) {
        $items = ListPresenter::run($query->get());

        $headers = [__('User'), __('Client'), __('Project'), __('Date'), __('Hours'), __('Amount'), __('Description')];
        if ( \Auth::user()->type == 'client' ) {
            $headers = [__('User'), __('Project'), __('Date'), __('Hours'), __('Amount'), __('Description')];
        }

        $values = $items->map(function($item) {
            $value = [
                $item->short_name ?? $item->name,
                $item->client_name,
                $item->project_name,
                $item->date,
                $item->hours,
                $item->amount ? $item->amount : $item->hours * $item->rate,
                $item->description,
            ];

            if ( \Auth::user()->type == 'client' ) {
                $value = [
                    $item->short_name ?? $item->name,
                    $item->project_name,
                    $item->date,
                    $item->hours,
                    $item->amount ? $item->amount : $item->hours * $item->rate,
                    $item->description,
                ];
            }
            return $value;
        });

        export_to_csv($headers, $values, __(\Domain::name()).' '.($filters['window'] ?? ''));
    }
}
