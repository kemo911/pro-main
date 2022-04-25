<?php


include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
can('delete_schedule');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
$scheduleId = $_POST['schedule_id'];

$db = DB::getInstance();

$db->query('DELETE FROM schedule WHERE id = ?', [ $scheduleId ]);
$db->query('DELETE FROM appointment_time_slots WHERE schedule_id = ?', [ $scheduleId ]);

$db->query('DELETE FROM appointment_details WHERE appointment_id = (SELECT appointment_id FROM appointment WHERE schedule_id = ?)', [ $scheduleId ]);
$db->query('DELETE FROM appointment_photo WHERE appointment_id = (SELECT appointment_id FROM appointment WHERE schedule_id = ?)', [ $scheduleId ]);

$db->query('DELETE FROM appointment WHERE schedule_id = ?', [ $scheduleId ]);

echo json_encode( array(
    'status' => 1
) );
exit();
