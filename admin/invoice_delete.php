<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 4/9/2018
 * Time: 8:26 PM
 */
include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");

$invoice_id = !empty($_GET['invoice_id']) ? $_GET['invoice_id'] : 0;

if( empty($invoice_id) ) header('Location:/admin/invoice_report.php');

//echo $invoice_id; exit;

$db = DB::getInstance();

$db->query('DELETE FROM invoice WHERE id = ?', [ $invoice_id ]);
$db->query('DELETE FROM invoice_photo WHERE invoice_id = ?', [ $invoice_id ]);
$_SESSION['message'] = 'Inovice has been deleted successfully.';
header('Location:/admin/invoice_report.php');