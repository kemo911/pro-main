<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
can('create_appointment');

$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
$scheduleId = $_POST['schedule_id'];

$db = DB::getInstance();

$slots = $db->query('SELECT * FROM appointment_time_slots WHERE schedule_id = ? AND appointment_id = 0 ORDER BY start_time ASC', [ $scheduleId ] )->toArray();

if ( ! empty($slots) ) : ?>
    <option value="0">Heure</option>
<?php
    foreach ( $slots as $slot ):
?>
        <option value="<?php echo $slot['id']; ?>"><?php echo date('H:i A', strtotime($slot['start_time'])); ?> - <?php echo date('H:i A', strtotime($slot['end_time'])); ?></option>
    <?php endforeach; ?>
<?php else: ?>
    NOT_FOUND
<?php endif; ?>

