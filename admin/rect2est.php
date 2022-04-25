<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');


//protect( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3, Permission::USER_LEVEL_4]));

$_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);

if ( !empty($_GET['rid']) ) {

    $db = DB::getInstance();

    $reclamationData = $db->query('select * from reclamation where id = ?', [$_GET['rid']])->toArray();

    foreach ( $reclamationData as $rec ) {
        $invoiceId = createInvoiceDraft();
        $db->update('invoice', ['invoice_type' => 'estimate'], $invoiceId );
        $db->insert('estimations', [
            'reclamation_id' => $_GET['rid'],
            'client_id' => $rec['client_id'],
            'appointment_id' => $_GET['aid'],
            'invoice_id' => $invoiceId,
            'estimation_invoice_id' => $invoiceId,
        ]);
        header('Location: /admin/estimation.php#!/estimation/' . $db->lastId());
        exit();
    }

}