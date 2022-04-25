<?php
include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");


try {
    if ( !empty($_GET['id']) ) {
        $_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);
        $db = DB::getInstance();
        $resp = $db->query('DELETE FROM parts WHERE id = ?', [$_GET['id']]);
    }
} catch (Exception $exception) {

}

header('Location: /admin/parts_report.php');