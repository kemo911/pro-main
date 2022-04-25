<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';

protect( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3]));

$_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);

$start = $_GET['start'];
$end   = $_GET['end'];

$db = DB::getInstance();

$appointments = $db->query('
SELECT * FROM schedule s
WHERE s.date BETWEEN ? AND ?
  AND s.date NOT IN ( SELECT date FROM appointment a WHERE a.date BETWEEN ? AND ?)
GROUP BY s.date
ORDER BY s.date ASC', [ $start, $end, $start, $end ])->toArray();

$combined = array();

foreach ( $appointments as $appointment ) {
    $combined[$appointment['date']][] = $appointment;
}

$events = array();

foreach ( $combined as $date => $appointmentData ) {
    $events[] = array(
        "title"=> "Schedule",
        "start"=> $date,
        "rendering" => "background",
        "className" => 'schedule-block'
    );
}

echo json_encode($events);
