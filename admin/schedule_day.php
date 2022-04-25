<?php



include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');

include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');

protect("*");



$pageClass = 'schedule_calendar_view';



include_once('header.php');

?>

<div class="container">

    <div class="row">

        <div class="col-md-6">

            <div class="panel panel-primary">

                <div class="panel-heading"></div>

                <div class="panel-body">

                    <?php if ( allowForSet1() ): ?>
                    <a href="/admin/schedule_create.php?date=<?php echo $_GET['date']; ?>" id="create-schedule-block" class="btn btn-md btn-block btn-primary"><i class="glyphicon glyphicon-plus"></i> HORAIRE</a>
                    <a href="/admin/appointment_create.php?date=<?php echo $_GET['date']; ?>&appointment_type=estimation" id="create-estimation-appointment" class="btn btn-md btn-block btn-blue"><i class="glyphicon glyphicon-plus"></i> ESTIMATION RENDEZ-VOUS</a>
                    <a href="/admin/appointment_create.php?date=<?php echo $_GET['date']; ?>&appointment_type=repair" id="create-repair-appointment" class="btn btn-md btn-block btn-red"><i class="glyphicon glyphicon-plus"></i> RÉPARATION RENDEZ-VOUS</a>
                    <?php endif; ?>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="panel panel-primary">

                <div class="panel-heading">Changer la date pour voir l'horaire</div>

                <div class="panel-body">

                    <div class="form-group">

                        <label for="day-view-datepicker"></label>

                        <input id="day-view-datepicker" value="<?php echo $_GET['date']; ?>" type="text" class="form-control" readonly="readonly"/>

                    </div>

                    <a href="/admin/schedule_day_list.php?date=<?php echo $_GET['date']; ?>" class="btn btn-sm btn-success btn-block">Vois la liste <i class="glyphicon glyphicon-chevron-right"></i></a>

                </div>

            </div>

        </div>

        <div class="col-md-12"></div>

        <div class="col-md-6">

            <div class="panel panel-primary">

                <div class="panel-heading"></div>

                <div class="panel-body">

                    <div id="schedule-calendar-day-1"></div>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="panel panel-primary">

                <div class="panel-heading"></div>

                <div class="panel-body">

                    <div id="schedule-calendar-day-2"></div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include_once('footer.php');

?>

