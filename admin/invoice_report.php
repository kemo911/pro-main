<?php
/**
 * Created by PhpStorm.
 * User: Kajem
 * Date: 1/23/2018
 * Time: 2:49 AM
 */
include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
$techGuys = getUsersByLevel(4);
$insurerGuys = getUsersByLevel(6);
$users = getUsers();
$currentUser = getUserDetailsById($_SESSION['jigowatt']['user_id']);
$clients = getClients();
include_once('header.php');
?>

    <div class="container">
        <div class="row">
            <?php if( !empty($_SESSION['message']) ): ?>
                <div class="alert alert-success fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php endif; ?>
            <div class="col-md-12">
                <h2 class="text-uppercase">RAPPORT DES FACTURES</h2>
                <div class="filterFields">
                    <div class="field date">
                        <label for="date">Date</label><br/>
                        <input type="text" class="form-control" id="start_date" placeholder="Start date"/>
                        <input type="text" class="form-control" id="end_date" value="<?php echo date('m/d/Y'); ?>"
                               placeholder="End date"/>
                        <span class="clearfix"></span>
                    </div>

                    <div class="field">
                        <label for="tech">Tech</label><br/>
                        <select class="form-control" id="tech">
                            <option value="">--Choisir un tech--</option>
                            <?php foreach ($techGuys as $techGuy): ?>
                                <option value="<?php echo $techGuy['user_id']; ?>" <?php echo $currentUser['user_id'] == $techGuy['user_id'] ? 'selected readonly' : '' ?> ><?php echo $techGuy['name'] . ' (' . $techGuy['username'] . ')'; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ( !isUser('Insurer', $currentUser['user_id']) ): ?>
                    <div class="field">
                        <label for="insurer">Assureur</label><br/>
                        <select class="form-control" id="insurer">
                            <option value="Promutuel">Promutuel</option>
<!--                            --><?php //foreach ($insurerGuys as $insurerGuy): ?>
<!--                                <option value="--><?php //echo $insurerGuy['name']; ?><!--" --><?php //echo $currentUser['user_id'] == $insurerGuy['user_id'] ? 'selected readonly' : '' ?><!-- >--><?php //echo $insurerGuy['name']; ?><!--</option>-->
<!--                            --><?php //endforeach; ?>
<!--                            <option value="Desjardins">Desjardins</option>-->
<!--                            <option value="SSQ">SSQ</option>-->
<!--                            <option value="La Capital">La Capital</option>-->
<!--                            <option value="Intact">Intact</option>-->
<!--                            <option value="l\'unique"> L'unique</option>-->
                        </select>
                    </div>
                    <?php else: ?>
                    <div class="field">
                        <label for="insurer">Assureur</label><br/>
                        <select class="form-control" disabled  id="insurer"></select>
                    </div>
                    <?php endif; ?>

                    <div class="field">
                        <label for="clients">Client</label><br/>
                        <select class="form-control" id="client">
                            <option value="">--Choose a client--</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo $client['clientid'] ?>"><?php echo $client['fname'] . ' ' . $client['lname']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="clients">Solde</label><br/>
                        <select class="form-control" id="solde">
                            <option value="">--Choisir--</option>
                            <option value="solde">Solde</option>
                            <option value="no_solde">Sans Solde</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="clients">Véhicule de location</label><br/>
                        <select class="form-control" id="rental_car">
                            <option value="">--Choisir--</option>
                            <option value="Yes">Oui</option>
                            <option value="No">Non</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="clients">Non Payé / Payé</label><br/>
                        <select class="form-control" id="pending_paid">
                            <option value="">--Choisir--</option>
                            <option value="No">NON PAYÉ</option>
                            <option value="Yes">PAYÉ</option>
                        </select>
                    </div>

                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table id="invoice_report_dt" class="table table-striped table-bordered dt-responsive"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>#réclamation</th>
                            <th>Assureur</th>
                            <th>Compagnie</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Solde</th>
                            <th>Auto loc.</th>
                            <th>PAYÉ</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Utilisateur</th>
                            <th>#réclamation</th>
                            <th>Assureur</th>
                            <th>Compagnie</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Solde</th>
                            <th>Auto loc.</th>
                            <th>PAYÉ</th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="summary">
            <div class="row">
                <div class="col-md-12 text-right">
                    <table class="table-responsive" align="right">
                        <tr>
                            <td class="summary-label">Sous total</td>
                            <td align="right"><span id="sum_sub_total">0.00</span></td>
                        </tr>
                        <tr>
                            <td class="summary-label">TVQ</td>
                            <td align="right"><span id="sum_tvq">0.00</span></td>
                        </tr>
                        <tr>
                            <td class="summary-label">TPS</td>
                            <td align="right"><span id="sum_tps">0.00</span></td>
                        </tr>
                        <tr>
                            <td class="summary-label">Franchise</td>
                            <td align="right"><span id="sum_franchise">0.00</span></td>
                        </tr>
                        <tr>
                            <td class="summary-label">Total</td>
                            <td align="right"><span id="sum_total">0.00</span></td>
                        </tr>
                        <tr>
                            <td class="summary-label">Paiement effectué</td>
                            <td align="right"><span id="sum_deposit">0.00</span></td>
                        </tr>
                        <tr>
                            <td class="summary-label">Solde</td>
                            <td align="right"><span id="sum_solde">0.00</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    <link href="./assets/css/reports.css?aaaff1" rel="stylesheet">
<?php include_once('footer.php'); ?>