<?php
include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
can('create_invoice_from_appointment');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
$db = DB::getInstance();

$appointmentId = $_POST['appointment_id'];
$appointment = getAppointment($appointmentId);

if ( !empty($appointment) ) {

    $appointment = $appointment[0];

    $invoiceData = array (
        'date' => $appointment['date'],
        'tech' => $appointment['tech_id'],
        'client_id' => $appointment['client_id'],
        'f_name' => $appointment['client_fname'],
        'l_name' => $appointment['client_lname'],
        'reclamation' => $appointment['reclamation'],
        'tel' => $appointment['tel1'],
        'company' => $appointment['cie'],
        'email' => $appointment['email'],
        'insurer' => $appointment['insurer'],
        'vin' => $appointment['vin'],
        'brand' => $appointment['brand'],
        'model' => $appointment['model'],
        'year' => $appointment['year'],
        'inventory' => $appointment['inventory'],
        'color' => $appointment['color'],
        'particular_area' => $appointment['particular_area'],
        'brake_type' => $appointment['brake_type'],
        'millage' => $appointment['millage'],
    );

    $invoiceData['latest_request'] = json_encode($invoiceData);

    $db->insert('invoice', $invoiceData);

    $_SESSION['invoice'] = $db->lastId();

    echo json_encode( [
        'status' => 1,
        'message' => 'Invoice Created.',
        'invoice_id' => $db->lastId(),
    ] );
    die;
}

echo json_encode( [
    'status' => 0,
    'message' => 'Error creating invoice.'
] );
die;
