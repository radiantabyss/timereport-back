<?php
namespace App\Domains\Dashboard\Panel\Aggregations;

use App\Models as Model;
use App\Domains\Social\Object\Filters\Filter;
use App\Domains\Social\Object\Services\ChartRepository;

class ObjectAggregation
{
    private static $flattened = [];

    public static function run($panel) {
        $aggregations = $panel->settings['aggregations'];
        $entries = self::getEntries($panel, $aggregations);
        $entries = self::setAggregationValues($entries, $aggregations);
        $tree = self::buildTree($entries, $aggregations);

        return $tree;
    }

    private static function getEntries($panel, $aggregations) {
        $filters = $panel->filters;
        $metrics = $panel->settings['metrics'];

        $aggregation_types = pluck($aggregations, 'type');
        $aggregation_columns = pluck($aggregations, 'column');

        unset($filters['order_by']);
        unset($filters['order']);

        $query = Model\ObjectModel::query();

        //make a query for each pair of used metrics and then combine the results
        $query_metrics = array_merge($metrics, \Metrics::getUsedMetricsInQuery($filters));
        $raw_metrics = \Metrics::extractRawMetrics($query_metrics);
        $raw_metrics_pages = ceil(count($raw_metrics) / 2);

        for ( $i = 0; $i < $raw_metrics_pages; $i++ ) {
            $raw_metrics_paged = array_slice($raw_metrics, $i * 2, 2);

            //force double metrics index
            if ( count($raw_metrics_paged) == 1 ) {
                if ( $raw_metrics_paged[0] == 'cost' ) {
                    $raw_metrics_paged[] = 'revenue';
                }
                else {
                    array_unshift($raw_metrics_paged, count($raw_metrics) == 1 ? 'cost' : $raw_metrics[0]);
                }
            }

            //build index
            $index = 'os_object_id_date';
            foreach ( $raw_metrics_paged as $metric ) {
                $index .= '_'.\Metrics::short()[$metric];
            }

            //build query
            $stats_query = '(SELECT object_id';
            if ( array_intersect($aggregation_types, ['day', 'month', 'year']) ) {
                $stats_query .= ', date';
            }

            foreach ( $raw_metrics_paged as $metric ) {
                $stats_query .= ', '.\Metrics::sql()[$metric];
            }

            $stats_query .= ' FROM object_stat'.(preg_match('/hours/', $filters['window']) ? '_hourly' : '').'_'.$filters['type'];
            $stats_query .= ' FORCE INDEX ('.$index.')';
            $stats_query .= ' WHERE '.parse_window_raw_sql($filters['window']);
            $stats_query .= ' GROUP BY object_id';

            if ( array_intersect($aggregation_types, ['day', 'month', 'year']) ) {
                $stats_query .= ', date';
            }

            $stats_query .= ') as object_stat_'.$i;

            $query->rightJoin(\DB::raw($stats_query), 'object_stat_'.$i.'.object_id', '=', 'id');
        }

        //join rules count
        if ( (isset($filters['with_rules']) && $filters['with_rules'] != '') || in_array('with_rules', $aggregation_columns) ) {
            $rules_query = '
            (SELECT
                object.id as object_id,
                CASE
                    WHEN (COUNT(DISTINCT object_rule.object_id) > 0 OR COUNT(DISTINCT object_rule_group.object_id) > 0) THEN 1
                    ELSE 0
                END as with_rules
            FROM object
            LEFT JOIN
                object_rule ON object.id = object_rule.object_id
            LEFT JOIN
                object_rule_group ON object.id = object_rule_group.object_id
            GROUP BY object.id
            ) as object_rules';

            $query->leftJoin(\DB::raw($rules_query), 'object_rules.object_id', '=', 'id');
        }

        $select = [];
        foreach ( $metrics as $metric ) {
            $select[] = \DB::raw(\Metrics::sql()[$metric]);
        }

        foreach ( $aggregations as $aggregation ) {
            if ( in_array($aggregation['type'], ['day', 'month', 'year']) ) {
                $select[] = 'date';
                $query->groupBy('date')->orderBy('date', 'desc');
            }
            else if ( $aggregation['column'] == 'with_rules' ) {
                $select[] = \DB::raw('IFNULL(with_rules, 0) as with_rules');
                $query->groupBy('with_rules')->orderBy('with_rules');
            }
            else {
                $select[] = $aggregation['column'];
                $query->groupBy($aggregation['column'])->orderBy($aggregation['column']);
            }
        }

        //apply filters
        Filter::apply($query, $filters);

        $entries = toArray($query->select($select)->get());

        return $entries;
    }

    private static function setAggregationValues($entries, $aggregations) {
        $aggregation_types = [];
        $accounts = [];
        foreach ( $aggregations as &$aggregation ) {
            $aggregation_types[] = $aggregation['type'];

            if ( $aggregation['type'] != 'column' ) {
                continue;
            }

            if ( in_array($aggregation['column'], ['account_id', 'ad_account_id', 'campaign_id', 'adset_id']) ) {
                $Model = $aggregation['column'] == 'account_id' ? '\\App\\Models\\Account' : '\\App\\Models\\ObjectModel';
                $results = $Model::select('id', 'name')
                    ->whereIn('id', pluck($entries, $aggregation['column']))
                    ->get();

                $aggregation['values'] = [];
                foreach ( $results as $result ) {
                    $aggregation['values'][$result->id] = $result->name;
                }
            }
            else if ( $aggregation['column'] == 'with_rules' ) {
                $aggregation['values'] = [
                    0 => 'Without Rules',
                    1 => 'With Rules',
                ];
            }
        }

        foreach ( $entries as &$entry ) {
            foreach ( $aggregations as &$aggregation ) {
                if ( $aggregation['type'] == 'day' ) {
                    $entry['day'] = date(date('Y', strtotime($entry['date'])) != date('Y') ? 'd M Y' : 'd M', strtotime($entry['date']));
                }
                else if ( $aggregation['type'] == 'month' )  {
                    $entry['month'] = date(date('Y', strtotime($entry['date'])) != date('Y') ? 'M Y' : 'M', strtotime($entry['date']));
                }
                else if ( $aggregation['type'] == 'year' )  {
                    $entry['year'] = date('Y', strtotime($entry['date']));
                }
                else if ( isset($aggregation['values']) ) {
                    $entry[$aggregation['column']] = $aggregation['values'][$entry[$aggregation['column']]] ?? $entry[$aggregation['column']];
                }
            }

            if ( array_intersect($aggregation_types, ['day', 'month', 'year']) ) {
                unset($entry['date']);
            }
        }

        return $entries;
    }

    private static function buildTree($entries, $aggregations) {
        $branches = [];

        foreach ( $aggregations as $aggregation ) {
            if ( $aggregation['type'] == 'column' ) {
                $branches[] = $aggregation['column'];
            }
            else {
                $branches[] = $aggregation['type'];
            }
        }

        return build_tree(toArray($entries), $branches);
    }
}
