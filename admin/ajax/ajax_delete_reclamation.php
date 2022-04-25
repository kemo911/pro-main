<?php
include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
can('delete_reclamation');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

$db = DB::getInstance();

$db->delete('reclamation', $_POST['rid']);
exit;
