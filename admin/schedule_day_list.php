<?php

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("*");

$pageClass = 'schedule_calendar_day_view';

$date = !empty($_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : date('Y-m-d');

$appointments = getAppointmentsByDate( $date );

include_once('header.php');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="text-center">
                        <a class="pull-left" href="/admin/schedule_day.php?date=<?php echo $date; ?>"><i class="glyphicon glyphicon-chevron-left"></i> Voir le jour</a>
                        Liste des estimation pour <?php echo date('F j, Y', strtotime($date)); ?>
                    </h4>
                </div>
                <div class="panel-body">
                    <?php if ( empty($appointments) ): ?>
                        <h4 class="text-center">Aucun rendez-vous </h4>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <tr>
                                <th>Réclamation</th>
                                <th>Nom de client</th>
                                <th>Jour</th>
                                <th>Heure du rendez-vous</th>
                                <th>Type de rendez-vous</th>
                                <th>Tech</th>
                                <th></th>
                            </tr>
                        <?php foreach ( $appointments as $appointment ): ?>
                            <tr>
                                <td><?php echo $appointment['reclamation']; ?></td>
                                <td><?php echo $appointment['client_name']; ?></td>
                                <td><?php echo $appointment['date']; ?></td>
                                <td><span class="label label-info"><?php echo $appointment['start_time']; ?></span> to <span class="label label-success"><?php echo $appointment['end_time']; ?></span></td>
                                <td><?php echo $appointment['type']; ?></td>
                                <td><?php echo $appointment['tech_name']; ?></td>
                                <td>
                                    <a href="/admin/appointment_view.php?appointment_id=<?php echo $appointment['id']; ?>">view</a> |
                                    <a class="delete-appointment" data-appointment-id="<?php echo $appointment['id']; ?>"  style="color: red;" href="javascript:void(0);">delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                         </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>
