<?php
$appointmentId = !empty($_GET['appointment_id']) ? $_GET['appointment_id'] : 0;

if(!$appointmentId) header('Location: /admin/schedule.php');

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once ('classes/send_email.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");

$appointment = getAppointment( $appointmentId );

if( !empty($appointment[0]) ) $appointment = $appointment[0];

//echo "<pre>"; print_r($appointment);exit;

ob_start();
include 'appointment_email_body.php';
$body = ob_get_contents();
ob_end_clean();

$generic = new Send_email();
$to = $appointment['email'];
$subject = 'Notification de rendez-vous';
$generic->sendEmail(['info@eco-solutiongrele.com', $to], $subject, $body);
$_SESSION['email_sent_msg'] = 'Le courriel a été envoyé';
header('Location: /admin/appointment_view.php?appointment_id='.$appointmentId);
//echo "<pre>";print_r($body);exit;