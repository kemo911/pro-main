<?php
include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
protect('admin');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

$clientId = $_POST['id'];

$db = DB::getInstance();

$result = $db->query('SELECT * FROM clients WHERE clientid = ?', [ $clientId ])->toArray();
$client = $result[0];
$array = array(
    'name' => $client['fname'] . ' ' . $client['lname'],
    'email' => $client['email'],
    'telephone' => $client['tel1'],
    'company' => $client['cie'],
);

echo json_encode( $array );
