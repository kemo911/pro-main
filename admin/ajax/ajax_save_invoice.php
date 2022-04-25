<?php
//header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
can('save_estimate');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
$_POST['latest_request'] = json_encode($_POST);
$postId = $_POST['invoice_id'];

$parts = $_POST['parts'];
unset($_POST['parts']);

if ( !empty($_POST['javascript_object']) ) {
    $_POST['javascript_object'] = json_encode($_POST['javascript_object']);
}
if ( !empty($_POST['javascript_object2']) ) {
    $_POST['javascript_object2'] = json_encode($_POST['javascript_object2']);
}
$_POST['rental_car'] = intval($_POST['rental_car']);
unset($_POST['invoice_id']);

$db = DB::getInstance();
if (isset($_POST['dots'])) {
    $_POST['dots'] = json_encode(json_decode(html_entity_decode($_POST['dots'])));
}
if (isset($_POST['shared'])) {
    $sharedObject = json_decode(html_entity_decode($_POST['shared']), true);
    if (!empty($sharedObject['windShield']) && $sharedObject['windShield']['enabled']) {
        $invoice['parts'][] = [
            'name' => $sharedObject['windShield']['label'],
            'description' => $sharedObject['windShield']['desc'],
            'hour' => $sharedObject['windShield']['hour'],
            'price' => $sharedObject['windShield']['price'],
            'damage_section' => $sharedObject['windShield']['label'],
        ];
    }
    $_POST['shared'] = json_encode($sharedObject);
}
$db->update('invoice', $_POST, $postId);
$invoiceId = $db->lastId();
if ( isset($_POST['confirm_invoice']) && $_POST['confirm_invoice'] ) {
    flashInvoiceSession();
}

//save parts
if ( !empty($parts) ) {
    foreach ($parts as $part) {
        $existingParts = $db->query('SELECT id FROM parts WHERE invoice_id = ? AND name = ? AND damage_section = ?', [ $invoiceId, $part['name'], $part['damage_section'] ])->toArray();
        $partData = [
            'invoice_id' => $invoiceId,
            'name' => $part['name'],
            'description' => $part['description'],
            'hour' => $part['hour'],
            'price' => $part['price'],
            'ordered' => 0,
            'received' => 0,
            'damage_section' => $part['damage_section'] ?? null
        ];
        if (!empty($existingParts)) {
            foreach ($existingParts as $existingPart) {
                $partData['ordered'] = $existingPart['ordered'];
                $partData['received'] = $existingPart['received'];
                $partData['damage_section'] = $existingPart['damage_section'] ?? $partData['damage_section'];
                $db->update('parts', $partData, $existingPart['id']);
            }
        } else {
            $db->insert('parts', $partData);
        }
    }
}

if ( !empty($_POST['reclamation']) ) {
    createReclamationIfNotExists( $_POST['reclamation'], array(
        'client_id' => $_POST['client_id'],
        'reclamation' => $_POST['reclamation'],
        'insurer' => $_POST['insurer'],
        'vin' => $_POST['vin'],
        'brand' => $_POST['brand'],
        'model' => $_POST['model'],
        'year' => $_POST['year'],
        'inventory' => $_POST['inventory'],
        'color' => $_POST['color'],
        'brake_type' => $_POST['brake_type'],
        'particular_area' => $_POST['particular_area'],
        'millage' => $_POST['millage'],
        'creation_style' => 'invoice',
    ) );
}

echo json_encode(array(
    'message' => 'Data saved!',
    'invoice' => $invoiceId,
));

exit();
