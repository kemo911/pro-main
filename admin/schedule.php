<?php

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');

include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');


//protect( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_2, Permission::USER_LEVEL_3, Permission::USER_LEVEL_4]));



$pageClass = 'schedule_calendar_view';



if ( empty($_GET['date']) )

    $_GET['date'] = date('Y-m-d');



include_once('header.php');

?>



<div class="container">

    <div class="row">

        <div class="col-md-4">

            <div class="panel panel-primary">

                <div class="panel-heading"></div>

                <div class="panel-body">
                    
                    <?php if( protectThis("Admin") ) : ?>
                    <a href="/admin/schedule_create.php?date=<?php echo date('Y-m-d'); ?>" id="create-schedule-block" class="btn btn-md btn-block btn-primary"><i class="glyphicon glyphicon-plus"></i> HORAIRE</a>
                    <?php else : ?><?php endif; ?>

                    <?php if ( allowForSet1() ): ?>

                    <a href="/admin/appointment_create.php?date=<?php echo $_GET['date']; ?>&appointment_type=estimation" id="create-estimation-appointment" class="btn btn-md btn-block btn-blue"><i class="glyphicon glyphicon-plus"></i> ESTIMATION RENDEZ-VOUS</a>
                    <a href="/admin/appointment_create.php?date=<?php echo $_GET['date']; ?>&appointment_type=repair" id="create-repair-appointment" class="btn btn-md btn-block btn-red"><i class="glyphicon glyphicon-plus"></i> RÉPARATION RENDEZ-VOUS</a>

                    <?php endif; ?>

                </div>

            </div>

            <div class="panel panel-primary">

                <div class="panel-heading">LÉGEND</div>

                <div class="panel-body">

                    <ul style="list-style: none; margin: 0; padding: 0;line-height: 30px; font-size: 10px; text-transform: uppercase;">

                        <li> <span style="background-color: #888888; border: 1px solid #333; margin-right: 3px; color: #888888; width: 30px !important; height: 30px;">✦</span> Non disponible</li>

                        <li> <span style="background-color: #ffffff; border: 1px solid #333; margin-right: 3px; color: #ffffff; width: 30px !important; height: 30px;">✦</span> Disponibilités de rendez-vous</li>

                        <li> <span style="background-color: #3498db; border: 1px solid #333; margin-right: 3px; color: #3498db; width: 30px !important; height: 30px;">✦</span> Rendez-vous d'éstimation à l'horaire</li>

                        <li> <span style="background-color: #e74c3c; border: 1px solid #333; margin-right: 3px; color: #e74c3c; width: 30px !important; height: 30px;">✦</span> Rendez-vous de réparation à l'horaire</li>

                    </ul>

                </div>

            </div>

        </div>



        <div class="col-md-8">

            <div class="panel panel-primary">

                <div class="panel-heading"></div>

                <div class="panel-body">

                    <div id="schedule-calendar"></div>

                </div>

            </div>

        </div>

    </div>

</div>



<?php

include_once('footer.php');

?>



