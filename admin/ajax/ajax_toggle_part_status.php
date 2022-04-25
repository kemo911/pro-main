<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';

$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$db = DB::getInstance();

$record = $db->query('SELECT * FROM parts WHERE id = ?', [$_POST['id']])->toArray();

$count = 0;

foreach ($record as $r) {
    $r[$_POST['field']] = (int) (! $r[$_POST['field']]);
    $id = $r['id'];
    unset($r['id']);
    $count = $db->update('parts', $r, $id);
}

echo json_encode(['message' => 'updated ' . $count . ' row']);
exit();