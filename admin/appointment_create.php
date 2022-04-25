<?php

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
//protect("Admin");

define('GIVE_ACCESS', true);

$clients = getClients();

$token = getToken();

$reclamationDetails = null;
if ( !empty($_GET['reclamation_id']) ) {
    $reclamationDetails = getReclamation($_GET['reclamation_id']);
    $token = $reclamationDetails['token'];
}

$_SESSION['appointment.token'] = $token;

$appointmentPhotos = getAppointmentPhotosByToken( $token );

$pageClass = 'appointment_create';
$techGuys = getUsersByLevel( 4 );
$estimatorGuys = getUsersByLevel( 5 );

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$appointmentType = isset($_GET['appointment_type']) ? $_GET['appointment_type'] : '';
$type = $appointmentType == 'repair' ? 'tech' : 'estimator';
$daySchedules = getSchedulesByType( $date, $type );

include_once('header.php');
?>
<div class="container">
    <div class="row">

        <input type="hidden" id="appointment-type" value="<?php echo $appointmentType; ?>">
        <input type="hidden" id="token" value="<?php echo $_SESSION['appointment.token']; ?>">
        <input type="hidden" id="reclamation_id" value="<?php echo !empty($_GET['reclamation_id']) ? $_GET['reclamation_id'] : 0; ?>">

        <?php if ( ! $appointmentType ): ?>
            <h2>No appointment type provided</h2>
        <?php else: ?>

        <?php if ( in_array($appointmentType, ['estimation', 'repair']) ) {
                include_once __DIR__ . '/__estimation.php';
            } ?>
        <?php endif; ?>
    </div>
</div>
<?php
include_once('footer.php');
?>
