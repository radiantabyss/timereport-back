<?php
function get_months_between_dates($start, $end) {
     $current = strtotime($start);
     $end = strtotime($end);
     $months = [];

     while ( $current < $end ) {
         $months[] = date('Y-m', $current);
         $current = strtotime(date('Y-m-01', $current) . "+1 month");
     }

     return $months;
}

function export_to_csv($headers, $values, $filename = 'export', $delimiter = ',') {
    header('Content-Type: text/csv charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$filename.'.csv";');

    ob_start();
    $handle = fopen('php://output', 'w');
    fputs($handle, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

    fputcsv($handle, $headers, $delimiter);

    //put values
    foreach ( $values as $value ) {
        fputcsv($handle, $value, $delimiter);
    }

    fclose($handle);
    ob_flush();
    exit;
}
