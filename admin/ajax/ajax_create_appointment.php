<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
can('create_appointment');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
$db = DB::getInstance();

$appointment = $_POST['appointment'];
$appointment_details = $_POST['appointment_details'];

$appointmentId = $db->insert('appointment', $appointment);

if ( $appointmentId ) {

    $db->query('UPDATE appointment_time_slots SET appointment_id = ? WHERE id = ?', [ $appointmentId, $appointment['appointment_slot_id'] ]);
    $db->query('UPDATE appointment_photo SET appointment_id = ? WHERE token = ?', [ $appointmentId, $_SESSION['appointment.token'] ]);

    $appointment_details['appointment_id'] = $appointmentId;
    $appointment_details_id = $db->insert('appointment_details', $appointment_details);

    if ( !empty($appointment_details['reclamation']) ) {
        createReclamationIfNotExists($appointment_details['reclamation'], array(
            'client_id' => $appointment_details['client_id'],
            'reclamation' => $appointment_details['reclamation'],
            'insurer' => $appointment_details['insurer'],
            'vin' => $appointment_details['vin'],
            'brand' => $appointment_details['brand'],
            'model' => $appointment_details['model'],
            'year' => $appointment_details['year'],
            'inventory' => $appointment_details['inventory'],
            'color' => $appointment_details['color'],
            'brake_type' => $appointment_details['brake_type'],
            'particular_area' => $appointment_details['particular_area'],
            'millage' => $appointment_details['millage'],
            'creation_style' => 'appointment',
        ));
    }

    echo json_encode( array(
        'status' => 1,
        'message' => 'Appointment created',
    ) );
    exit;
}

echo json_encode( array(
    'status' => 0,
    'message' => 'Appointment not created',
) );