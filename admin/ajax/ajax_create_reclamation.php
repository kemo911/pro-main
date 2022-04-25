<?php
include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
can('create_reclamation');

$post = filter_var_array($_POST, FILTER_SANITIZE_STRING);
$db = DB::getInstance();

$date = $post['date'];
unset($post['date']);

$post['created_by'] = $_SESSION['jigowatt']['user_id'];
$post['session_id'] = session_id();
$saving_type = $post['saving_type'];
unset($post['saving_type']);
$reclamationId = $post['reclamation_id'];
unset($post['reclamation_id']);
if ( $reclamationId > 0 ) {
    $post['updated_at'] = date('Y-m-d H:i:s');
    $db->update('reclamation', $post, $reclamationId);
} else {
    $reclamationId = $db->insert('reclamation', $post);
}

if ( $reclamationId ) {

	if ( $saving_type == 'book' ) {
		$redirect_url = '/admin/appointment_create.php?date='.$date.'&appointment_type=estimation&reclamation_id='.$reclamationId;
	} else {
		$_SESSION['message.reclamation'] = 'Reclamation saved successfully.';
		$redirect_url = '/admin/reclamation.php';
	}

    echo json_encode([
        'status' => 1,
        'message' => 'Réclamation sauvegardée',
        'redirect_url' => $redirect_url,
    ]);

} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Réclamation NON sauvegardée'
    ]);
}
exit(0);