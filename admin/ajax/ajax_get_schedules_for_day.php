<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
protect('admin');

$start_date = date('Y-m-d', strtotime($_POST['start_date']));
$end_date = date('Y-m-d', strtotime($_POST['end_date']));

$daySchedules = getSchedulesRangeDate( $start_date, $end_date );
if ( count($daySchedules) > 0 ):
?>
<?php foreach ($daySchedules as $s): ?>
    <tr>
        <td><?php echo $s['user_type']; ?></td>
        <td><?php echo getUserNameById($s['user_id']); ?></td>
        <td><?php echo $s['address']; ?></td>
        <td><?php echo $s['date']; ?></td>
        <td><?php echo $s['start_time']; ?></td>
        <td><?php echo $s['end_time']; ?></td>
        <td><?php echo $s['time_block']; ?></td>
        <td>
            <a href="/admin/schedule_day.php?date=<?php echo date('Y-m-d', strtotime($start_date)); ?>">
                <span class="badge badge-primary"><?php echo getTotalAppointmentsFromScheduleId($s['id']); ?></span>
            </a>
        </td>
        <td><a class="delete-schedule" data-schedule-id="<?php echo $s['id']; ?>"  style="color: red;" href="javascript:void(0);">delete</a></td>
    </tr>
<?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="7">No Schedule Created Yet.</td>
    </tr>
<?php endif; ?>

