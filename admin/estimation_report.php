<?php
/**
 * Created by PhpStorm.
 * User: Kajem
 * Date: 1/23/2018
 * Time: 2:49 AM
 */
include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
$users = getUsers();
$estimatorGuys = getUsersByLevel(5);
$insurerGuys = getUsersByLevel(6);
$currentUser = getUserDetailsById($_SESSION['jigowatt']['user_id']);
$clients = getClients();
include_once('header.php');
?>

    <div class="container">
        <div class="row">
            <?php if( !empty($_SESSION['success_msg']) ): ?>
                <div class="alert alert-success fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <?php echo $_SESSION['success_msg']; ?>
                </div>
                <?php
                unset($_SESSION['success_msg']);
            endif; ?>
            <div class="col-md-12">
                <h2 class="text-uppercase">RAPPORT DES ESTIMATIONS</h2>
                <div class="filterFields">
                    <div class="field date">
                        <label for="date">Date</label><br/>
                        <input type="text" class="form-control" id="start_date" placeholder="Start date"/>
                        <input type="text" class="form-control" id="end_date" value="<?php echo date('m/d/Y'); ?>"
                               placeholder="End date"/>
                        <span class="clearfix"></span>
                    </div>

                    <div class="field">
                        <label for="tech">Estimateur</label><br/>
                        <select class="form-control" id="estimator">
                            <option value="">--Choisir un estimateur--</option>
                            <?php foreach ($estimatorGuys as $estimatorGuy): ?>
                                <option value="<?php echo $estimatorGuy['user_id']; ?>" <?php echo $currentUser['user_id'] == $estimatorGuy['user_id'] ? 'selected readonly' : '' ?> ><?php echo $estimatorGuy['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="clients">Client</label><br/>
                        <select class="form-control" id="client">
                            <option value="">--Choisir un client--</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['clientid'] ?>"><?php echo $client['fname'] . ' ' . $client['lname']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table id="estimation_report_dt" class="table table-striped table-bordered dt-responsive"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th>Estimateur</th>
                            <th>#réclamation</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Date</th>
                            <th>ES#</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Estimateur</th>
                            <th>#réclamation</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Date</th>
                            <th>ES#</th>
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
<?php include_once('footer.php'); ?>