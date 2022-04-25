<?php

include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");
can("delete_client");

try {
    global $generic;
    $generic->query('DELETE FROM clients WHERE clientid = :clientid', array(':clientid' => $_GET['client_id']));
//    echo json_encode(['success' => true]);
} catch (Exception $e) {
//    echo json_encode(['success' => false]);
}

header('Location: /admin/clients.php');

