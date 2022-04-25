<?php

header('Location: /admin/reports.php', true, 301);

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");

$currentUser = getUserDetailsById($_SESSION['jigowatt']['user_id']);
include_once('header.php');
?>


<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-uppercase">RAPPORT DES RÉCLAMATIONS</h2>

            <div class="col-md-2">
                <div class="field date">

                    <input type="hidden" id="callback" value="<?php echo isset($_GET['callback']) ? 1 : 0; ?>">

                    <label for="date">Dates</label><br/>
                    <input type="text" class="form-control dp" id="start_date" placeholder="Start date"/>
                    to
                    <input type="text" class="form-control dp" id="end_date" value="<?php echo date('m/d/Y'); ?>"
                           placeholder="End date"/>
                    <span class="clearfix"></span>
                </div>
            </div>

            <div class="col-md-9 col-md-offset-1">
                <div class="table-responsive">
                    <table id="reclamation_report_dt" class="table table-striped table-bordered dt-responsive"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th>#Réclamation</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Courriel</th>
                            <th>Téléphone</th>
                            <th></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>


        </div>
    </div>
</div>
    <link href="./assets/css/reports.css?aaaff1" rel="stylesheet">
<?php include_once('footer.php'); ?>