<!-- Footer

================================================== -->


<?php if ( $_SERVER['SCRIPT_NAME'] != '/admin/estimation.php' ): ?>
    </div> <!-- /.span10 -->

            </div> <!-- /.row -->
<?php endif; ?>
            <footer class="text-center">

                <hr>

                <p>

                   &copy; <?php echo date('Y'); ?>. Tous droits réservés. Groupe Bosse.ca

                    <!--?php

                        if ( empty($setTranslate) ) $setTranslate = new Translate();

                        $setTranslate->languageSelector();

                    ?-->

                </p>

            </footer>


<?php if ( $_SERVER['SCRIPT_NAME'] != '/admin/estimation.php' ): ?>
    </div> <!-- /.container -->
<?php endif; ?>
        <link rel="stylesheet" href="/assets/lightbox/css/lightbox.css">
        <link rel="stylesheet" href="/admin/assets/plg/ang-inline-edit/css/xeditable.min.css">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">

        <!-- latest stable jquery framework via CDN as of 24/05/2017 -->

        <script src="assets/js/moment.min.js"></script>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

        <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>



        <!-- latest stable bootstrap javascript framework via CDN as of 24/05/2017 -->

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>



        <script src="../assets/js/jquery.ba-hashchange.min.js"></script>

        <script src="../assets/js/jquery.validate.min.js"></script>

        <script src="../assets/js/jquery.placeholder.min.js"></script>



        <script src="assets/js/select2/select2.min.js"></script>



        <script src="assets/js/jquery-jigowatt-admin.js"></script>

        <script src="../assets/js/jquery.jigowatt.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.7/angular.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.7/angular-route.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.7/angular-animate.js"></script>
        <script src="/admin/assets/plg/ang-inline-edit/js/xeditable.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.8.2/fullcalendar.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/locale/fr.js"></script>

        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

        <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>

        <script src="https://cdn.datatables.net/responsive/2.2.0/js/dataTables.responsive.min.js"></script>

        <script src="https://cdn.datatables.net/responsive/2.2.0/js/responsive.bootstrap.min.js"></script>

        <script src="https://parsleyjs.org/dist/parsley.min.js"></script>

        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script>
            jQuery(function($){
                $.datepicker.regional['fr'] = {
                    closeText: 'Fermer',
                    prevText: '&#x3c;Préc',
                    nextText: 'Suiv&#x3e;',
                    currentText: 'Aujourd\'hui',
                    monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
                        'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
                    monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
                        'Jul','Aou','Sep','Oct','Nov','Dec'],
                    dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
                    dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
                    dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
                    weekHeader: 'Sm',
                    dateFormat: 'dd-mm-yy',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: '',
                    minDate: '-12M +0D',
                    maxDate: '+12M +0D',
                    numberOfMonths: 2,
                    showButtonPanel: true
                };
                $.datepicker.setDefaults($.datepicker.regional['fr']);
            });
        </script>

        <script src="/assets/lightbox/js/lightbox.js"></script>

        <script src="assets/js/notify.min.js"></script>

        <script src="assets/js/dropzone.js"></script>

        <script src="/admin/assets/signature/jquery.signaturepad.js"></script>

        <script src="/admin/assets/signature/json2.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

        <script src="assets/js/custom.js?ver=20190629"></script>
        <script src="assets/js/libs.js"></script>
        <script src="assets/js/new.js?<?php echo date('y-m-d'); ?>"></script>



        <?php if (!empty($pageClass) && $pageClass == 'schedule_calendar_view'): ?>

        <script src="assets/js/schedule_calendar_view.js"></script>

        <?php endif; ?>



        <?php if (!empty($pageClass) && $pageClass == 'schedule_calendar_day_view'): ?>

        <script src="assets/js/schedule_calendar_day_view.js"></script>

        <?php endif; ?>



        <?php if (!empty($pageClass) && $pageClass == 'appointment_view'): ?>

        <script src="assets/js/schedule_calendar_day_view.js"></script>

        <?php endif; ?>



        <?php

            $file_name = explode('/',$_SERVER['PHP_SELF']);

            $file_name = end($file_name);

        ?>

        <?php if($file_name == 'mold.php'):?>

            <script src="assets/js/mold.js?assff22t"></script>
            <script src="assets/js/html2canvas.js"></script>

        <?php endif; ?>



        <?php if($file_name == 'schedule_create.php'):?>

            <script src="assets/js/schedule_create.js?sdfs4d"></script>

        <?php endif; ?>



        <?php if($file_name == 'appointment_create.php' || $file_name == 'reclamation.php'):?>

            <script src="assets/js/appointment_create.js"></script>

        <?php endif; ?>


        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>


<?php if($file_name == 'invoice_report.php'):?>

            <script src="assets/js/invoice_report.js?5shfff22t"></script>

        <?php endif; ?>

        <?php if($file_name == 'estimation_report.php'):?>

            <script src="assets/js/estimation_report.js?5shf22t"></script>

        <?php endif; ?>

        <?php if($file_name == 'parts_report.php'):?>

            <script src="assets/js/parts_report.js?5sdd22t"></script>

        <?php endif; ?>

        <?php if($file_name == 'reclamation_report.php'):?>

            <script src="assets/js/reclamation_report.js"></script>

        <?php endif; ?>

        <?php if ( isset($pageSpecificJS) ): ?>
            <?php echo $pageSpecificJS; ?>
        <?php endif; ?>

<?php if (isset($_SESSION['read_only_mode']) && $_SESSION['read_only_mode'] === true): ?>

    <script>
        (function ($) {
            setTimeout(function () {
                $('button').attr('disabled', true);
                $('input').attr('disabled', true);
                $('select').attr('disabled', true);
                $('textarea').attr('disabled', true);
            }, 2000)
        })(jQuery);
    </script>

<?php endif; ?>


    </body>



</html>



<?php



ob_flush();

