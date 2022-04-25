<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';

$db = DB::getInstance();

$post = filter_var_array($_POST, FILTER_SANITIZE_STRING);

if ( !empty($post['id']) ) {
    $query = 'SELECT COUNT(id) as total FROM reclamation WHERE reclamation = ? AND id NOT IN (?)';
    $binding = [ $post['reclamation'], $post['id'] ];
} else {
    $query = 'SELECT COUNT(id) as total FROM reclamation WHERE reclamation = ?';
    $binding = [ $post['reclamation'] ];
}

$result = $db->query($query, $binding)->first();

if ( $result->total == 0 ) {
    echo json_encode([
        'status' => 1,
        'message' => 'new reclamation',
    ]);
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Cette réclamation existe déjà',
    ]);
}
