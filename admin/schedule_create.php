<?php

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');

//protect( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3, Permission::USER_LEVEL_4]));

protect(Permission::USER_LEVEL_1);

$pageClass = 'schedule_create';
$techGuys = getUsersByLevel( 4 );
$estimatorGuys = getUsersByLevel( 5 );

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

if ( $date && $end_date ) {
    $daySchedules = getSchedulesRangeDate($date, $end_date);
}
else {
    $daySchedules = getSchedules( $date );
}

include_once('header.php');
?>
<div class="container">
    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h2>Horraire pour - <span id="selected-date"><?php echo date('m/d/Y', strtotime($date)); ?></span> <span class="pull-right"><a id="view-appointment"
                                    href="/admin/schedule_day.php?date=<?php echo date('Y-m-d', strtotime($date)); ?>">Voir les rendez-vous</a></span> </h2>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Adresse</th>
                            <th>Date</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Bloque de temps</th>
                            <th>Rendez-vous</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="schedule-lists">
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
                                    <a href="/admin/schedule_day.php?date=<?php echo date('Y-m-d', strtotime($date)); ?>">
                                        <span class="badge badge-primary"><?php echo getTotalAppointmentsFromScheduleId($s['id']); ?></span>
                                    </a>
                                </td>
                                <td><a class="delete-schedule" data-schedule-id="<?php echo $s['id']; ?>"  style="color: red;" href="javascript:void(0);">Effacer</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h2 class="text-center">Créer un horaire</h2>
                </div>
                <div class="panel-body">
                    <div id="schedule-create">

                        <form action="" class="form" id="create-schedule">

                            <div class="col-md-4">
                                <div class="form-group">
<!--                                    <label for="date" class="col-md-2">Date:</label>-->

                                    <input type="text" class="form-control dp start_date" readonly="readonly" id="schedule-start-date" value="<?php echo date('Y-m-d', strtotime($date)); ?>" name="schedule[start_date]">
                                    <span class="help-block"></span>
                                    <input type="text" class="form-control dp" readonly="readonly" id="schedule-end-date" value="<?php date('Y-m-d', strtotime("+1 week")); ?>" name="schedule[end_date]">
                                    <span class="help-block label label-primary" id="duration"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
<!--                                    <label for="user_type" class="col-md-2">Assign to: </label><br>-->
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-danger btn-md user_type"> <input type="radio" name="schedule[user_type]" value="estimator" autocomplete="off"> Estimateur </label>
                                        <label class="btn btn-danger btn-md user_type"> <input type="radio" name="schedule[user_type]" value="tech" autocomplete="off"> Tech </label>
                                    </div>
                                    <span class="help-block">Assigner cet horaire</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div id="techGuyDiv" class="form-group">
                                    <label for="tech">Techs:</label>
                                    <select class="form-control" disabled="disabled" id="tech">
                                        <?php foreach ( $techGuys as $techGuy ): ?>
                                            <option value="<?php echo $techGuy['user_id']; ?>"><?php echo $techGuy['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div id="estimatorGuyDiv" class="form-group">
                                    <label for="tech">Estimateurs:</label>
                                    <select class="form-control" disabled="disabled" id="estimator">
                                        <?php foreach ( $estimatorGuys as $techGuy ): ?>
                                            <option value="<?php echo $techGuy['user_id']; ?>"><?php echo $techGuy['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <input type="text" id="address" name="schedule[address]" class="form-control">
                                <span class="help-block">Adresse</span>
                            </div>

                            <div class="col-md-2">
                                <select name="schedule[time_block]" class="form-control" id="time_block">
                                    <option value="30">.5 heures</option>
                                    <option value="60">1 heure</option>
                                    <option value="90">1.5 heures</option>
                                    <option value="120">2 heures</option>
                                    <option value="180">3 heures</option>
                                </select>
                                <span class="help-block">Bloque de temps</span>
                            </div>

                            <div class="col-md-3">

                                <input type="radio" name="shift" id="shift1" class="shift" value="1"> AM

                                <div id="shift-1">
                                    <label for="from1">Début: </label>
                                    <select class="form-control shift-time" disabled="disabled" id="from1" name="schedule[start_time1]">
                                        <?php $from1_times = array('7:00', '7:30', '8:00', '8:30', '9:00'); ?>
                                        <?php foreach ( $from1_times as $time ): ?>
                                            <option data-time-block="<?php echo $time; ?>" value="<?php echo $time; ?>"><?php echo $time; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="to1">à</label>
                                    <select class="form-control shift-time" disabled="disabled" id="to1" name="schedule[end_time1]">
                                        <option data-time-block="" value="">--Choisir--</option>
                                        <?php $to1_times = array('10:00', '10:30', '11:00', '11:30', '12:00'); ?>
                                        <?php foreach ( $to1_times as $time ): ?>
                                            <option data-time-block="<?php echo $time; ?>" value="<?php echo $time; ?>"><?php echo $time; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="help-block">De 7:00 à 12:00</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <input type="radio" name="shift" id="shift2" class="shift" value="2"> PM
                                <div id="shift-2">
                                    <label for="from1">Début: </label>
                                    <select class="form-control shift-time" disabled="disabled" id="from2" name="schedule[start_time2]">
                                        <?php $from2_times = array('13:00', '13:30', '14:00', '14:30', '15:00'); ?>
                                        <?php foreach ( $from2_times as $time): ?>
                                            <option data-time-block="<?php echo $time; ?>" value="<?php echo $time; ?>"><?php echo $time; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="to1">à</label>
                                    <select class="form-control shift-time" disabled="disabled" id="to2" name="schedule[end_time2]">
                                        <option data-time-block="" value="">--Choisir--</option>
                                        <?php $to2_times = array('16:00', '16:30', '17:00', '17:30', '18:00'); ?>
                                        <?php foreach ( $to2_times as $time ): ?>
                                            <option data-time-block="<?php echo $time; ?>" value="<?php echo $time; ?>"><?php echo $time; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="help-block">De 13:00 à 18:00</span>
                                </div>
                            </div>

                            <div class="col-md-4 col-md-offset-4">
                                <button class="btn btn-info btn-lg btn-block" id="create-schedule-button" type="button">Créer</button>
                            </div>

                            <div class="col-md-12">
                                <p id="message"></p>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once('footer.php');
?>
