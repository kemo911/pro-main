<?php
/**
 * Created by PhpStorm.
 * User: Kajem
 * Date: 1/23/2018
 * Time: 2:49 AM
 */
include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");
$techGuys = getUsersByLevel(4);
$insurerGuys = getUsersByLevel(6);
$currentUser = getUserDetailsById($_SESSION['jigowatt']['user_id']);
$clients = getClients();
include_once('header.php');
?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="text-uppercase">RAPPORT DES PIÈCES</h2>
                <div class="filterFields">
                    <div class="field date">
                        <label for="date">Dates</label><br/>
                        <input type="text" class="form-control" id="start_date" placeholder="Start date"/>
                        <input type="text" class="form-control" id="end_date" value="<?php echo date('m/d/Y'); ?>"
                               placeholder="End date"/>
                        <span class="clearfix"></span>
                    </div>

                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table id="parts_report_dt" class="table table-striped table-bordered dt-responsive"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th>#Réclamation</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Pièce</th>
                            <th>Date</th>
                            <th>Commandé</th>
                            <th>Reçu</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#Réclamation</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Pièce</th>
                            <th>Date</th>
                            <th>Commandé</th>
                            <th>Reçu</th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
    </div>
    <link href="./assets/css/reports.css?aaaff1" rel="stylesheet">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 30px;
            height: 17px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 13px;
            width: 13px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(13px);
            -ms-transform: translateX(13px);
            transform: translateX(13px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 17px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
    <script>
        window.setTimeout(function () {
            ;(function ($) {
                const update_status = function (type,id) {
                    $.post('/admin/ajax/ajax_toggle_part_status.php', {
                        id: id,
                        field: type
                    });
                }
                $(document).on('click', '.toggle-part-status', function (e) {
                    update_status( $(this).data('field'), $(this).data('id') );
                });
            })(jQuery);
        }, 2500)
    </script>
<?php include_once('footer.php'); ?>