<?php
include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");

$eid = @$_GET['eid'];

if ( $eid && $eid > 0 ) {
    $db = DB::getInstance();

    $db->query('DELETE FROM estimations where id = ?', [$eid]);

    $_SESSION['success_msg'] = 'Estimation has been deleted successfully.';
}

header('Location: /admin/estimation_report.php');