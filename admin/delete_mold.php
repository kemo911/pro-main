<?php
include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");

try {
    global $generic;
    $generic->query('DELETE FROM mold WHERE id = :id', array(':id' => $_GET['mold_id']));
    $_SESSION['success_msg'] = 'Mold has been deleted successfully.';
//    echo json_encode(['success' => true]);
} catch (Exception $e) {
//    echo json_encode(['success' => false]);
}

if( !empty($_GET['from']) && $_GET['from'] == 'estimation_report' ){
    header('Location: /admin/estimation_report.php');
}else{
    header('Location: /admin/molds.php');
}
