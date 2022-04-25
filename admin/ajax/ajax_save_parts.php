<?php
ini_set('display_errors', 1);
include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
can('save_estimate');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
$invoice = $_POST['invoice_id'];
$db = DB::getInstance();

$db->delete('parts', [ ['invoice_id', '=', $invoice] ]);

foreach ( $_POST['parts'] as $part ) {
    $part['invoice_id'] = $invoice;
    $db->insert('parts', $part);
}
