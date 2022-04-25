<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
protect('admin');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

$whiteListTable = [
    'appointment_details' => [
        'checkbox_not_presented', 'checkbox_total_loss', 'checkbox_want_repair_appointment', 'checkbox_monetary_compensation',
        'FK' => 'appointment_id'
    ]
];

$table = $_POST['table'];
$field = $_POST['field'];
$value = $_POST['value'];

if ( array_key_exists($table, $whiteListTable) ) {
    if ( in_array( $field, $whiteListTable[$table] ) ) {

        $db = DB::getInstance();

        if ( !empty($_POST['FK']) && !empty($_POST['fkValue']) ) {
            $fkField = $whiteListTable[$table]['FK'];
            $fkValue = $_POST['fkValue'];
            $db->query("UPDATE $table SET $field = ? WHERE $fkField = ?", [ $value, $fkValue ]);

            echo json_encode( [
                'status' => 1
            ] );
            die;
        }

    }
}

echo json_encode( [
    'status' => 0
] );