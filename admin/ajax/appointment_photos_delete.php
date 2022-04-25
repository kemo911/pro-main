<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
protect('admin');

echo json_encode( ['status' => delAppointmentPhoto( $_POST['id'] )] );