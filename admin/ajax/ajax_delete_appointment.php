<?php


include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
can('delete_appointment');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
$appointmentId = $_POST['appointment_id'];

$db = DB::getInstance();
/*
$db->query('DELETE FROM schedule WHERE id = ?', [ $scheduleId ]);
$db->query('DELETE FROM appointment_time_slots WHERE schedule_id = ?', [ $scheduleId ]);

$db->query('DELETE FROM appointment_details WHERE appointment_id = (SELECT appointment_id FROM appointment WHERE schedule_id = ?)', [ $scheduleId ]);
$db->query('DELETE FROM appointment_photo WHERE appointment_id = (SELECT appointment_id FROM appointment WHERE schedule_id = ?)', [ $scheduleId ]);

$db->query('DELETE FROM appointment WHERE schedule_id = ?', [ $scheduleId ]);*/

$db->query('UPDATE appointment_time_slots SET appointment_id = 0 WHERE appointment_id = ?', [ $appointmentId ]);
$db->query('DELETE FROM appointment_details WHERE appointment_id = ?', [ $appointmentId ]);
$db->query('DELETE FROM appointment_photo WHERE appointment_id = ?', [ $appointmentId ]);
$db->query('DELETE FROM appointment WHERE id = ?', [ $appointmentId ]);

echo json_encode( array(
    'status' => 1
) );
exit();
