<?php
include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");

try {
    global $generic;
    $generic->query('DELETE FROM login_users WHERE user_id = :user_id', array(':user_id' => base64_decode($_GET['user_id'])));
//    echo json_encode(['success' => true]);
} catch (Exception $e) {
//    echo json_encode(['success' => false]);
}

header('Location: /admin/');
