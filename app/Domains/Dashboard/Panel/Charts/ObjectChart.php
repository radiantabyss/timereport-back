<?php
namespace App\Domains\Dashboard\Panel\Charts;

use App\Models as Model;
use App\Domains\Social\Object\Filters\Filter;
use App\Domains\Social\Object\Services\ChartRepository;

class ObjectChart
{
    private static $aggregation;
    private static $grouping;

    public static function run($panel) {
        self::$aggregation = $panel->settings['aggregation'] ?? null;
        self::$grouping = $panel->settings['grouping'] ?? null;

        $entries = self::getEntries($panel);
        $grouping_keys = self::getGroupingKeys($panel, $entries);
        $labels = self::buildLabels($panel, $grouping_keys);
        $datasets = self::buildDatasets($panel, $entries, $grouping_keys, $labels);

        if ( self::$grouping && !self::$aggregation ) {
            $datasets = ChartRepository::setColorsPerLabel($datasets, $panel->settings['type']);
        }
        else {
            $datasets = ChartRepository::setColors($datasets);
        }

        $labels = array_values($labels);

        return compact('labels', 'datasets');
    }

    private static function getEntries($panel) {
        $entries = [];
        $filters = $panel->filters;

        $metrics = $panel->settings['has_multiple_metrics'] ? $panel->settings['metrics'] : [$panel->settings['metric']];
        $query_metrics = array_merge($metrics, \Metrics::getUsedMetricsInQuery($filters));

        unset($filters['order_by']);
        unset($filters['order']);

        //make a query for each pair of used metrics and then combine the results
        $raw_metrics = \Metrics::extractRawMetrics($query_metrics);
        $raw_metrics_pages = ceil(count($raw_metrics) / 2);

        for ( $i = 0; $i < $raw_metrics_pages; $i++ ) {
            $query = Model\ObjectModel::query();

            $raw_metrics_paged = array_slice($raw_metrics, $i * 2, 2);
            $raw_metrics_paged_for_query = $raw_metrics_paged;

            //force double metrics index
            if ( count($raw_metrics_paged_for_query) == 1 ) {
                if ( $raw_metrics_paged_for_query[0] == 'cost' ) {
                    $raw_metrics_paged_for_query[] = 'revenue';
                }
                else {
                    array_unshift($raw_metrics_paged_for_query, count($raw_metrics) == 1 ? 'cost' : $raw_metrics[0]);
                }
            }

            $stats_query = '(SELECT object_id';

            if ( in_array(self::$aggregation, ['day', 'month', 'year']) ) {
                $stats_query .= ', date';
            }

            $stats_query_index = 'os_object_id_date';
            $select = [];
            foreach ( $raw_metrics_paged_for_query as $metric ) {
                $stats_query .= ', '.\Metrics::sql()[$metric];
                $stats_query_index .= '_'.\Metrics::short()[$metric];
                $select[] = \DB::raw(\Metrics::sql()[$metric]);
            }

            $stats_query .= ' FROM object_stat_'.$filters['type'];
            $stats_query .= ' FORCE INDEX ('.$stats_query_index.')';
            $stats_query .= ' WHERE '.parse_window_raw_sql($filters['window']);
            $stats_query .= ' GROUP BY object_id';

            if ( self::$aggregation ) {
                $stats_query .= ', date';
            }

            $stats_query .= ') as object_stat';

            $query->rightJoin(\DB::raw($stats_query), 'object_stat.object_id', '=', 'id');

            //join rules count
            if ( (isset($filters['with_rules']) && $filters['with_rules'] != '') || self::$grouping == 'with_rules' ) {
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

            if ( in_array(self::$aggregation, ['day', 'month', 'year']) ) {
                $select[] = 'date';
                $query->groupBy('date')->orderBy('date');
            }

            if ( self::$grouping ) {
                if ( self::$grouping == 'with_rules' ) {
                    $select[] = \DB::raw('IFNULL(with_rules, 0) as with_rules');
                }
                else {
                    $select[] = self::$grouping;
                }

                $query->groupBy(self::$grouping);
            }

            //apply filters
            Filter::apply($query, $filters);

            $entries_page = $query->select($select)->get();

            foreach ( $entries_page as $entry ) {
                foreach ( $raw_metrics_paged as $metric ) {
                    $grouping_key = self::$grouping ? $entry->{self::$grouping} : '';
                    $aggregation_key = '';

                    if ( in_array(self::$aggregation, ['day', 'month', 'year']) ) {
                        if ( self::$aggregation == 'day' ) {
                            $aggregation_key = $entry->date;
                        }
                        else if ( self::$aggregation == 'month' ) {
                            $aggregation_key = date('Y-m', strtotime($entry->date));
                        }
                        else if ( self::$aggregation == 'year' ) {
                            $aggregation_key = date('Y', strtotime($entry->date));
                        }
                    }

                    if ( !$aggregation_key && self::$grouping ) {
                        $aggregation_key = $grouping_key;
                        $grouping_key = '';
                    }

                    if ( !isset($entries[$aggregation_key][$grouping_key][$metric]) ) {
                        $entries[$aggregation_key][$grouping_key][$metric] = 0;
                    }

                    $entries[$aggregation_key][$grouping_key][$metric] += $entry->$metric;
                }
            }
        }

        //reformat to get the exact metrics needed and calculating computed metrics
        $formatted = [];
        foreach ( $entries as $aggregation_key => $entries_grouped ) {
            foreach ( $entries_grouped as $grouping_key => $stats ) {
                $stats = \Metrics::calculateComputed($stats);

                //remove unnecessary metrics
                foreach ( $stats as $metric => $value ) {
                    if ( !in_array($metric, $metrics) ) {
                        unset($stats[$metric]);
                    }
                }

                $formatted[$aggregation_key][$grouping_key] = $stats;
            }
        }

        return $formatted;
    }

    private static function getGroupingKeys($panel, $entries) {
        if ( !self::$grouping ) {
            return [''];
        }

        $grouping_keys = [];
        foreach ( $entries as $aggregation_key => $entries_grouped ) {
            if ( !self::$aggregation ) {
                $grouping_keys[] = $aggregation_key;
            }
            else {
                foreach ( $entries_grouped as $grouping_key => $entry ) {
                    $grouping_keys[] = $grouping_key;
                }
            }
        }

        $grouping_keys = array_unique($grouping_keys);

        if ( !count($grouping_keys) || !in_array(self::$grouping, ['account_id', 'ad_account_id', 'campaign_id', 'adset_id']) ) {
            return $grouping_keys;
        }

        $Model = '\\App\\Models\\'.(self::$grouping == 'account_id' ? 'Account' : 'ObjectModel');
        $objects = $Model::select('id', 'name')
            ->whereIn('id', $grouping_keys)
            ->get();

        $grouping_keys = [];
        foreach ( $objects as $object ) {
            $grouping_keys[$object->id] = $object->name;
        }

        return $grouping_keys;
    }

    private static function buildLabels($panel, $grouping_keys) {
        $labels = [];
        self::$aggregation = $panel->settings['aggregation'] ?? null;
        self::$grouping = $panel->settings['grouping'] ?? null;

        if ( in_array(self::$aggregation, ['day', 'month', 'year']) ) {
            $parsed_window = \Date::parseWindow($panel->filters['window']);
            $date_range = \Date::range($parsed_window['start'], $parsed_window['end']);

            foreach ( $date_range as $date ) {
                if ( self::$aggregation == 'day' ) {
                    $labels[$date] = date(date('Y', strtotime($date)) != date('Y') ? 'd M Y' : 'd M', strtotime($date));
                }
                else if ( self::$aggregation == 'month' ) {
                    $labels[date('Y-m', strtotime($date))] = date(date('Y', strtotime($date)) != date('Y') ? 'M Y' : 'M', strtotime($date));
                }
                else if ( self::$aggregation == 'year' ) {
                    $labels[date('Y', strtotime($date))] = date('Y', strtotime($date));
                }
            }
        }
        else if ( !self::$aggregation && self::$grouping ) {
            foreach ( $grouping_keys as $grouping_key => $grouping_text ) {
                $grouping_key = is_assoc($grouping_keys) ? $grouping_key : $grouping_text;

                if ( $grouping_key == '' ) {
                    $grouping_text = \Metrics::pretty()[$panel->settings['metric']];
                }

                if ( self::$grouping == 'with_rules' ) {
                    $grouping_text = $grouping_key ? 'With Rules' : 'Without Rules';
                }

                $labels[$grouping_key] = $grouping_text;
            }
        }
        else {
            $labels = ['' => ''];
        }

        return $labels;
    }

    private static function buildDatasets($panel, $entries, $grouping_keys, $labels) {
        $datasets = [];
        $metrics = $panel->settings['has_multiple_metrics'] ? $panel->settings['metrics'] : [$panel->settings['metric']];
        self::$grouping = $panel->settings['grouping'] ?? null;
        self::$aggregation = $panel->settings['aggregation'] ?? null;

        foreach ( $metrics as $metric ) {
            foreach ( $grouping_keys as $grouping_key => $grouping_text ) {
                $grouping_key = is_assoc($grouping_keys) ? $grouping_key : $grouping_text;

                if ( $grouping_key == '' ) {
                    $grouping_text = \Metrics::pretty()[$metric];
                }

                if ( !self::$aggregation ) {
                    $grouping_key = '';
                    $grouping_text = '';
                }

                $datasets[$metric.'_'.$grouping_key] = [
                    'label' => $grouping_text,
                    'data' => [],
                ];
            }
        }

        foreach ( $labels as $aggregation_key => $label ) {
            if ( !self::$aggregation ) {
                $grouping_key = '';

                foreach ( $metrics as $metric ) {
                    $value = $entries[$aggregation_key][$grouping_key][$metric] ?? 0;
                    $datasets[$metric.'_'.$grouping_key]['data'][] = $value;
                }
            }
            else {
                foreach ( $grouping_keys as $grouping_key => $grouping_text ) {
                    $grouping_key = is_assoc($grouping_keys) ? $grouping_key : $grouping_text;

                    foreach ( $metrics as $metric ) {
                        $value = $entries[$aggregation_key][$grouping_key][$metric] ?? 0;
                        $datasets[$metric.'_'.$grouping_key]['data'][] = $value;
                    }
                }
            }
        }

        return $datasets;
    }
}
