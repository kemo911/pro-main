<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
protect('admin');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

$periods = date_range($_POST['start_date'], $_POST['end_date']);
unset($_POST['start_date']);
unset($_POST['end_date']);
$failed = [];

foreach ( $periods as $period ) {
    $_POST['date'] = $period;
    list($status, $message) = createSchedule( $_POST );

    if ( !$status ) {
        $failed[] = 'Failed on ' . $period . ' reason: ' . $message . PHP_EOL;
    }
}

echo json_encode(array(
    'status' => 1,
    'message' => implode(PHP_EOL, $failed),
));
exit;
