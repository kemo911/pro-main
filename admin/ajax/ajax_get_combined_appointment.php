<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';

protect( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3]));

$_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);

$start = $_GET['start'];
$end   = $_GET['end'];

$db = DB::getInstance();

$appointments = $db->query('SELECT * FROM appointment WHERE date BETWEEN ? AND ? GROUP BY date, type ORDER BY date ASC', [ $start, $end ])->toArray();

$combined = array();

foreach ( $appointments as $appointment ) {
    $combined[$appointment['date']][] = $appointment;
}

$events = array();

foreach ( $combined as $date => $appointmentData ) {
    if ( count( $appointmentData ) > 1 ) {
        $events[] = array(
            "title"=> "Estimate Event",
            "start"=> $date,
            "rendering" => "background",
            "className" => 'estimate-combined-block'
        );
    }
}

echo json_encode($events);
