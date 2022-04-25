<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("*");
$techGuys = getUsersByLevel( 4 );
$currentUser = getUserDetailsById($_SESSION['jigowatt']['user_id']);
$clients = getClients();
/*
if ( !empty($_GET['flush']) ) {
    if ( 'go_ahead' == base64_decode( $_GET['flush'] ) ) {
        flashInvoiceSession();
    }
}

$newInvoice = false;

if ( !empty($_GET['new']) && $_GET['new'] == 'true' ) {
    $newInvoice = true;
}

if ( !empty($_GET['invoice_id'])) {
    $_SESSION['invoice'] = $_GET['invoice_id'];
    $newInvoice = true;
}

if ( empty($_SESSION['invoice'] ) ) {
    $_SESSION['invoice'] = createInvoiceDraft();
    header('Location: /admin/main.php?new=true');
    exit();
}*/

$id = !empty($_GET['invoice_id']) ? $_GET['invoice_id'] : 0;
$newInvoice = true;
if ( !$id ) {
    $newInvoice = true;
    $id = createInvoiceDraft();
    header('Location: /admin/main.php?invoice_id=' . $id);
    exit();
}
$_SESSION['invoice'] = $invId = $id;
$invoice = getInvoice( $id );

$currentClient = null;
if (!empty($invoice['client_id'])) {
    $currentClient = getClient($invoice['client_id']);
}

$invoicePhotos = getInvoicePhotos( $invId );
$parts = getIvoiceParts($invId);

$damageListForm = array(
    1 => 'CAPOT',
    2 => 'PAVILLON',
    3 => 'DESSUS HAYON',
    4 => 'VALISE HAYON',
    5 => 'PAN. LATERAL G',
    6 => 'PORTE ARR. G',
    7 => 'PORTE AV. G',
    8 => 'LONGERON G',
    9 => 'AILE G',
    10 => 'AILE D',
    11 => 'PORTE AV. D',
    12 => 'PORTE ARR. D',
    13 => 'PAN LATERAL D',
    14 => 'LONGERON D',
    15 => 'AUTRE',
    'stripping' => 'Dégarnissage',
    'other_fees' => 'Autres frais',
    'glazier' => 'Vitrier',
    'work_force' => 'Main d\'oeuvre',
    'parts' => 'Pièces',
    'covid' => 'COVID',
);

include_once 'header.php';
echo '<script> var clients = '. ( json_encode($clients) ) .'; </script>';
//echo '<script> var javascriptObject = '. ( empty($invoice['javascript_object']) ? '{}' : $invoice['javascript_object'] ) .'; </script>';
//echo '<script> var javascriptObject2 = '. ( empty($invoice['javascript_object2']) ? '{}' : $invoice['javascript_object2'] ) .'; </script>';
//echo '<script> var savedRequest = '. ( empty($invoice['latest_request']) ? '{}' : $invoice['latest_request'] ) .'; </script>';
echo '<script> var javascriptObject = {}; </script>';
echo '<script> var javascriptObject2 = {}; </script>';
echo '<script> var savedRequest = {}; </script>';
?>
<div id="page-name" data-page-name="invoice" data-status="draft" data-invoice-id="<?php echo $invId; ?>"></div>
<div class="container">
    <div class="row">

        <?php if ( empty($invoice) ): ?>
            <div id="invoice_alert" class="col-md-12">
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <div class="text-center"><strong>Aucune facture disponible pour ce ID</strong></div>
                    </div>
                </div>
            </div>
            <?php include_once('footer.php'); exit; ?>
        <?php endif; ?>

        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <?php if (protectThis( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3]) )) : ?>
                    <a class="btn btn-info text-right" href="/admin/client.php/"><i class="glyphicon glyphicon-plus"></i> Créer un client</a>
                <?php endif; ?>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="clients">Choisir un client</label>
                            <select name="client" class="form-control" id="clients">
                                <option value=""> --Choisir-- </option>
                                <?php foreach ( $clients as $client ): ?>
                                    <option value="<?php echo $client['clientid'] ?>" <?php echo ($client['clientid'] == $invoice['client_id']) ? 'selected' : '';  ?> ><?php echo $client['fname'] . ' ' . $client['lname']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date">Date:</label>
                                <input type="text" class="form-control" id="date" value="<?php echo date('Y/m/d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tech">Tech:</label>
                                <select class="form-control" id="tech">
                                    <?php foreach ( $techGuys as $techGuy ): ?>
                                        <option value="<?php echo $techGuy['user_id']; ?>" <?php echo $currentUser['user_id'] == $techGuy['user_id'] ? 'selected' : '' ?> ><?php echo $techGuy['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
<!--                                <input type="text" class="form-control" value="--><?php //echo $currentUser['name']; ?><!--" id="tech">-->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reclamation"># Réclamation:</label>
                                <input class="form-control" value="<?php echo $invoice['reclamation']; ?>" id="reclamation">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="border: 1px solid #e4e4e4; padding-top: 10px;">
                        <input type="hidden" name="clientid" id="clientid" value="">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_fname">Prénom:</label>
                                <input readonly="readonly" value="<?php echo $invoice['f_name']; ?>" type="text" class="form-control" id="client_fname">
                            </div>
                            <div class="form-group">
                                <label for="client_lname">Nom:</label>
                                <input readonly="readonly" value="<?php echo $invoice['l_name']; ?>" type="text" class="form-control" id="client_lname">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_tel1">Téléphone:</label>
                                <input readonly="readonly" type="text" value="<?php echo $invoice['tel']; ?>" class="form-control" id="client_tel1">
                            </div>
                            <div class="form-group">
                                <label for="client_email">Courriel:</label>
                                <input readonly="readonly" type="email" value="<?php echo $invoice['email']; ?>" class="form-control" id="client_email">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="client_address">Address:</label>
                                <input readonly="readonly" value="<?php echo $currentClient ? $currentClient['address'] : ''; ?>" type="text" class="form-control" id="client_address">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="client_cie">Compagnie:</label>
                                <input readonly="readonly" value="<?php echo $invoice['company']; ?>" type="text" class="form-control" id="client_cie">
                            </div>
                        </div>
<!--                        <div class="col-md-12">-->
<!--                            <div class="form-group">-->
<!--                                <label for="insurer">Assureur:</label>-->
<!--                                <select name="insurer" class="form-control" id="insurer">-->
<!--                                    <option value="Promutuel">Promutuel</option>-->
<!--                                    <option value="SSQ">SSQ</option>-->
<!--                                    <option value="La Capital">La Capital</option>-->
<!--                                    <option value="Intact">Intact</option>-->
<!--                                    <option value="l\'unique"> L'unique</option>-->
<!--                                </select>-->
<!--                            </div>-->
<!--                        </div>-->
                        <input type="hidden" id="insurer" name="insurer" value="Promutuel">
                    </div>
                    <div class="col-md-12" id="vinArea" style="border: 1px solid #e4e4e4; padding-top: 10px;">
                        <div id="vinAreaOverlay" style="display: none;"><p><img src="/admin/assets/img/ajax-loader.gif"></p></div>

                        <div class="input-group">
                            <input type="text" value="<?php echo $invoice['vin']; ?>" placeholder="Type VIN" class="form-control col-md-9" id="vin">
                            <span class="input-group-btn">
                               <button class="btn btn-primary"  id="scanVIN" type="button">Recherche</button>
                            </span>
                        </div>

                        <div class="form-group">
                            <div id="vinResults">
                                <div class="list-group">
<!--                                    <a class="list-group-item selectClient" href="javascript:void(0);">Item 1</a>-->
                                </div>
                            </div>
                            <div id="vinOutput">

                                <?php foreach ( array(
                                    'brand' => 'Marque',
                                    'model' => 'Modèle',
                                    'year' => 'Année',
                                    'inventory' => '#Immatriculation',
                                    'sn' => '#NS',
                                    'color' => 'Couleur',
//                                    'pa' => 'P.A',
//                                    'bt' => 'B.T',
                                    'millage' => 'Millage',
                                                ) as $k => $v ): ?>
                                    <div class="col-md-6 <?php echo $k == 'sn' ? 'hidden' : ''; ?>">
                                        <div class="form-group">
                                            <label for="<?php echo $k; ?>"><?php echo $v; ?>:</label>
                                            <input type="text" value="<?php echo isset($invoice[$k]) ? $invoice[$k] : '' ; ?>"  required="required" class="form-control" id="<?php echo $k; ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <input type="hidden" name="pa" value="" id="pa">
                                <input type="hidden" name="bt" value="" id="bt">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="border: 1px solid #e4e4e4; padding-top: 10px; min-height: 100px; padding-bottom: 15px; margin-bottom: 5px;">
                        <h4>Photos: </h4>
                        <form action="/admin/ajax/ajax_file_upload.php" id="dropzone" class="dropzone">
                            <div class="fallback">
                                <input name="file" type="file" multiple />
                            </div>
                        </form>

<!--                        --><?php //if ( $newInvoice == false ): ?>
                            <div id="photo_view">
                                <?php foreach ( $invoicePhotos as $photo ): ?>
                                    <a href="/<?php echo $photo['photo_url']; ?>" data-lightbox="image-<?php echo $photo['id']; ?>">
                                        <img class="img img-thumbnail" data-lightbox="image-<?php echo $photo['id']; ?>" src="/<?php echo $photo['photo_url']; ?>" width="100px">
                                    </a>
                                <?php endforeach; ?>
                            </div>
<!--                        --><?php //endif; ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <ul class="nav nav-tabs" id="vehicles" role="tablist" style="display: none;">
                        <li role="presentation" class="active"><a role="tab" data-toggle="tab" href="#car">Auto</a></li>
                        <li role="presentation"><a role="tab" data-toggle="tab" href="#truck">Camion</a></li>
                        <li role="presentation"><a role="tab" data-toggle="tab" href="#suv">VUS</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="car">
                            <div id="graphics_car">
                                <div data-dot-number="1" data-dot-type="car" class="dot dot-1">1</div>
                                <div data-dot-number="2" data-dot-type="car" class="dot dot-2">2</div>
                                <div data-dot-number="3" data-dot-type="car" class="dot dot-3">3</div>
                                <div data-dot-number="4" data-dot-type="car" class="dot dot-4">4</div>
                                <div data-dot-number="5" data-dot-type="car" class="dot dot-5">5</div>
                                <div data-dot-number="6" data-dot-type="car" class="dot dot-6">6</div>
                                <div data-dot-number="7" data-dot-type="car" class="dot dot-7">7</div>
                                <div data-dot-number="8" data-dot-type="car" class="dot dot-8">8</div>
                                <div data-dot-number="9" data-dot-type="car" class="dot dot-9">9</div>
                                <div data-dot-number="10" data-dot-type="car" class="dot dot-10">10</div>
                                <div data-dot-number="11" data-dot-type="car" class="dot dot-11">11</div>
                                <div data-dot-number="12" data-dot-type="car" class="dot dot-12">12</div>
                                <div data-dot-number="13" data-dot-type="car" class="dot dot-13">13</div>
                                <div data-dot-number="14" data-dot-type="car" class="dot dot-14">14</div>
                                <div data-dot-number="15" data-dot-type="car" class="dot dot-15">15</div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="truck">
                            <div id="graphics_truck">
                                <div data-dot-number="1" data-dot-type="truck" class="dot dot-1">1</div>
                                <div data-dot-number="2" data-dot-type="truck" class="dot dot-2">2</div>
                                <div data-dot-number="3" data-dot-type="truck" class="dot dot-3">3</div>
                                <div data-dot-number="4" data-dot-type="truck" class="dot dot-4">4</div>
                                <div data-dot-number="5" data-dot-type="truck" class="dot dot-5">5</div>
                                <div data-dot-number="6" data-dot-type="truck" class="dot dot-6">6</div>
                                <div data-dot-number="7" data-dot-type="truck" class="dot dot-7">7</div>
                                <div data-dot-number="8" data-dot-type="truck" class="dot dot-8">8</div>
                                <div data-dot-number="9" data-dot-type="truck" class="dot dot-9">9</div>
                                <div data-dot-number="10" data-dot-type="truck" class="dot dot-10">10</div>
                                <div data-dot-number="11" data-dot-type="truck" class="dot dot-11">11</div>
                                <div data-dot-number="12" data-dot-type="truck" class="dot dot-12">12</div>
                                <div data-dot-number="13" data-dot-type="truck" class="dot dot-13">13</div>
                                <div data-dot-number="14" data-dot-type="truck" class="dot dot-14">14</div>
                                <div data-dot-number="15" data-dot-type="truck" class="dot dot-15">15</div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="suv">
                            <div id="graphics_suv">
                                <div data-dot-number="1" data-dot-type="suv" class="dot dot-1">1</div>
                                <div data-dot-number="2" data-dot-type="suv" class="dot dot-2">2</div>
                                <div data-dot-number="3" data-dot-type="suv" class="dot dot-3">3</div>
                                <div data-dot-number="4" data-dot-type="suv" class="dot dot-4">4</div>
                                <div data-dot-number="5" data-dot-type="suv" class="dot dot-5">5</div>
                                <div data-dot-number="6" data-dot-type="suv" class="dot dot-6">6</div>
                                <div data-dot-number="7" data-dot-type="suv" class="dot dot-7">7</div>
                                <div data-dot-number="8" data-dot-type="suv" class="dot dot-8">8</div>
                                <div data-dot-number="9" data-dot-type="suv" class="dot dot-9">9</div>
                                <div data-dot-number="10" data-dot-type="suv" class="dot dot-10">10</div>
                                <div data-dot-number="11" data-dot-type="suv" class="dot dot-11">11</div>
                                <div data-dot-number="12" data-dot-type="suv" class="dot dot-12">12</div>
                                <div data-dot-number="13" data-dot-type="suv" class="dot dot-13">13</div>
                                <div data-dot-number="14" data-dot-type="suv" class="dot dot-14">14</div>
                                <div data-dot-number="15" data-dot-type="suv" class="dot dot-15">15</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-primary">
                    <div class="panel-heading" role="tab" id="strippingHead">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Dégarnissage
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <div class="form-inline">
                                <div class="checkbox" style="border-bottom: 1px solid #e4e4e4; padding-bottom: 10px;">
                                    <label><input id="stripping_partial" data-price="150" class="stripping" value="Partial stripping" type="checkbox"> Dégarnissage partiel</label>
                                    <label><input id="stripping_compact" class="stripping" data-price="250" value="Compact" type="checkbox"> Compact</label>
                                    <label><input id="stripping_standard" class="stripping" data-price="325" value="Standard" type="checkbox"> Standard</label>
                                    <label><input id="stripping_suv" class="stripping" data-price="400" value="SUV" type="checkbox"> VUS</label>
                                    <label><input id="stripping_sky_roof" class="stripping" data-price="75" value="Toit ouvrant" type="checkbox"> Toit ouvrant</label>
                                    <label><input id="stripping_dvd_video_acc" class="stripping" data-price="75" value="DVD Video Acc." type="checkbox"> DVD Vidéo Acc.</label>
                                    <label><input id="stripping_root_support" class="stripping" data-price="75" value="Support à toit" type="checkbox"> Support à toit</label>
                                    <label><input class="stripping" data-price="150" value="Toit panoramique" type="checkbox"> Toit panoramique</label>
                                </div>
                            </div>
                            <div class="input-details" style="padding-top: 10px;">
                                <div class="form-group">
                                    <label for="stripping_price">Prix</label>
                                    <input type="number" class="form-control" id="stripping_price">
                                </div>
                                <div class="form-group">
                                    <label for="stripping_note">Débossage sans peinture</label>
                                    <textarea name="stripping_note" class="form-control" id="stripping_note"
                                              style="width: 100%;"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="stripping_tech">Tech</label>
                                    <select class="form-control" name="stripping_tech" id="stripping_tech">
                                        <?php foreach ( $techGuys as $techGuy ): ?>
                                            <option value="<?php echo $techGuy['user_id']; ?>" <?php echo $currentUser['user_id'] == $techGuy['user_id'] ? 'selected' : '' ?>><?php echo $techGuy['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button id="stripping_add" class="btn btn-primary btn-md">Ajouter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading" role="tab" id="otherFeesHead">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Autres frais
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                        <div class="panel-body">
<!--                            <div class="form-group">-->
<!--                                <label for="oversize_dent">Bosse surdimensionnée (plus de 30mm) Prix:</label>-->
<!--                                <input type="number" min="0" id="oversize_dent" value="0" class="form-control">-->
<!--                            </div>-->
                            <div class="form-group">
                                <label for="other_fees_description">Description: </label>
                                <textarea id="other_fees_description" class="form-control"></textarea>
                            </div>
                            <div class="form-group hidden">
                                <label for="other_fees_price">Prix:</label>
                                <input type="number" min="0" id="other_fees_price" value="0" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="other_fees_total_price">Prix Total: </label>
                                <input type="number" id="other_fees_total_price" class="form-control" value="0">
                            </div>
                            <div class="form-group">
                                <button id="other_fees_add_button" class="btn btn-primary btn-md">Ajouter</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading" role="tab" id="glazierHead">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Vitrier
                            </a>
                        </h4>
                    </div>
                    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                        <div class="panel-body">

                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="text-center text-uppercase"><strong>Facture</strong></div>
                </div>
                <div class="panel-body">
                    <form id="invoice" action="" class="form-horizontal" method="post">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="invoice_location">Contrat de location:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?php echo $invoice['rental_agreement']; ?>" name="invoice[rental_agreement]" id="invoice_location" placeholder="Contrat de location">
                            </div>

                            <label class="control-label col-sm-2" for="invoice_location">Jours:</label>
                            <div class="col-sm-2">
                                <input type="number" id="number_of_days" value="<?php echo $invoice['number_of_days']; ?>" name="invoice[number_of_days]" class="form-control" placeholder="Nombre de jours">
                            </div>

                            <div class="col-sm-2">
                                <div class="checkbox">
                                    <label><input name="invoice[rental_car]" id="rental_car" value="1"  <?php echo $invoice['rental_car'] ? 'checked' : ''; ?> type="checkbox"> Véhicule de location:</label>
                                </div>
                            </div>
                        </div>

                        <table class="table table-responsive">
                            <tr>
                                <th>#</th>
                                <th>Tech</th>
                                <th>Description / Note</th>
                                <th>Prix</th>
                            </tr>
                            <?php foreach ( $damageListForm as $key => $damageListItem ): ?>
                                <?php if ( is_numeric($key) ): ?>
                                    <tr>
                                        <td><?php echo $key; ?> - <?php echo $damageListItem; ?></td>
                                        <td>
                                            <select class="form-control <?php echo (in_array($key, [15])) ? 'hidden' : ''; ?>" id="inv_<?php echo $key; ?>_tech" name="invoice[damage<?php echo $key; ?>][tech]">
                                                <?php foreach ( $techGuys as $techGuy ): ?>
                                                    <option value="<?php echo $techGuy['user_id']; ?>" <?php echo $currentUser['user_id'] == $techGuy['user_id'] ? 'selected' : '' ?>><?php echo $techGuy['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" value="<?php echo !empty($invoice["inv_" . $key . "_note"]) ? $invoice["inv_" . $key . "_note"] : ''; ?>" id="inv_<?php echo $key; ?>_note" class="form-control" name="invoice[damage<?php echo $key; ?>][description]">
                                        </td>
                                        <td>
                                            <input type="number" value="<?php echo !empty($invoice["inv_" . $key . "_note"]) ? $invoice["inv_" . $key . "_price"] : ''; ?>" min="0" id="inv_<?php echo $key; ?>_price" class="form-control invoice_price" name="invoice[damage<?php echo $key; ?>][price]">
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php foreach ( $damageListForm as $key => $damageListItem ): ?>
                                <?php if ( !is_numeric($key) ): ?>
                                    <tr>
                                        <td><?php echo $damageListItem; ?></td>
                                        <td>
                                            <?php if ( $key == 'glazier' ): ?>
                                                <input type="text" class="form-control" value="<?php echo $invoice["inv_" . $key . "_tech"]; ?>"  id="inv_<?php echo $key; ?>_tech" name="invoice[damage<?php echo $key; ?>][tech]">
                                            <?php else: ?>
                                                <select class="form-control <?php echo ( in_array($key, ['other_fees','work_force','parts', 'covid']) ) ? 'hidden' : ''; ?>" id="inv_<?php echo $key; ?>_tech" name="invoice[damage<?php echo $key; ?>][tech]">
                                                    <?php foreach ( $techGuys as $techGuy ): ?>
                                                        <option value="<?php echo $techGuy['user_id']; ?>" <?php echo $currentUser['user_id'] == $techGuy['user_id'] ? 'selected' : '' ?>><?php echo $techGuy['name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="<?php echo !empty($invoice["inv_" . $key . "_note"]) ? $invoice["inv_" . $key . "_note"] : ''; ?>" id="inv_<?php echo $key; ?>_note" name="invoice[damage<?php echo $key; ?>][description]">
                                        </td>
                                        <td>
                                            <input type="number" min="0" class="form-control invoice_price" value="<?php echo !empty($invoice["inv_" . $key . "_price"]) ? $invoice["inv_" . $key . "_price"] : 0; ?>"  id="inv_<?php echo $key; ?>_price" name="invoice[damage<?php echo $key; ?>][price]">
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </table>
                        <div class="col-md-12">
                            <div class="col-md-9 clearfix">
                                <div>
                                    <p>Je soussigné(e), assuré(e) ou représentant de l’assuré(e), déclare avoir pris connaissance des réparations de débosselage sans peinture exécuter sur mon véhicule par Eco Solution Grêle, et je m’en déclare entièrement satisfait(e).</p>
                                </div>

                                <div id="signature">
                                    <?php if ( $invoice['signature_img'] ): ?>
                                        <img src="<?php echo $invoice['signature_img']; ?>" alt="Signature Image">
                                    <?php endif; ?>
                                </div>

                                <div id="signature-pad" class="clearfix">
                                    <canvas class="pad"></canvas>
                                    <br>
                                    <a href="#" class="btn btn-xs btn-danger" id="signature-clear">Effacer la signature</a>
                                </div>

                                <br>

                                <div id="payment_type">
                                    <label for="p_check"> <input id="p_check" <?php echo $invoice['payment_method'] == 'check' ? ' checked ' : ''; ?> name="payment_method" type="radio" value="check"> Chèque</label> &nbsp;
                                    <label for="p_interac"> <input id="p_interac" <?php echo $invoice['payment_method'] == 'interac' ? ' checked ' : ''; ?> name="payment_method" type="radio" value="interac"> Interac</label> &nbsp;
                                    <label for="p_visa"> <input id="p_interac" name="payment_method" type="radio" value="visa"> Visa</label> &nbsp;
                                    <label for="p_cash"> <input id="p_cash" <?php echo $invoice['payment_method'] == 'cash' ? ' checked ' : ''; ?> name="payment_method" type="radio" value="cash"> Contant</label>
                                </div>

                            </div>
                            <div class="col-md-3">
                                <table>
                                    <tr>
                                        <td>Sous Total: </td>
                                        <td><input type="number" id="sub_total" value="<?php echo $invoice['sub_total']; ?>" class="form-control invoice_price_calculate" name="invoice[subtotal]"></td>
                                    </tr>
                                    <tr>
                                        <td>TPS (5%): </td>
                                        <td><input type="number" id="tps" value="<?php echo $invoice['tps']; ?>" class="form-control invoice_price_calculate" name="invoice[tps]"></td>
                                    </tr>
                                    <tr>
                                        <td>TVQ (9.975%): </td>
                                        <td><input type="number" id="tvq" value="<?php echo $invoice['tvq']; ?>" class="form-control invoice_price_calculate" name="invoice[tvq]"></td>
                                    </tr>
                                    <tr>
                                        <td>Franchise: </td>
                                        <td><input type="number" id="franchise" min="0" step="1" value="<?php echo $invoice['franchise']; ?>" class="form-control invoice_price_calculate" name="invoice[franchise]"></td>
                                    </tr>
                                    <tr>
                                        <td>Total: </td>
                                        <td><input type="number" id="total" value="<?php echo $invoice['total']; ?>" class="form-control invoice_price_calculate" name="invoice[total]"></td>
                                    </tr>
                                    <tr>
                                        <td>Dépôt:  </td>
                                        <td><input type="number" id="deposit" value="<?php echo $invoice['deposit']; ?>" class="form-control invoice_price_calculate" name="invoice[deposit]"></td>
                                    </tr>
                                    <tr>
                                        <td>Balance:  </td>
                                        <td><input type="number" id="balance" value="<?php echo $invoice['balance']; ?>" class="form-control invoice_price_calculate" name="invoice[balance]"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12 clearfix">

                                <div class="form-group" style="padding-top: 10px;">
                                    <input type="hidden" name="payment_status" id="payment_status" value="0"/>
                                    <?php if( !empty($invoice['payment_status'])){ ?>
                                    <button type="button" style="margin-right: 16px;" id="payment-button" data-value="PAID" class="btn btn-success pull-right">PAYÉ</button>
                                    <?php } else { ?>
                                        <button type="button" style="margin-right: 16px;" id="payment-button" data-value="Payment Pending" class="btn btn-danger pull-right">NON PAYÉ</button>
                                    <?php } ?>
                                </div>

                                <div class="form-group">
                                    <label class="control-label">Notes: </label>
                                    <textarea name="invoice[damages_notes]"  id="damages" class="form-control"><?php echo $invoice['damages']; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer clearfix">
                    <?php if (protectThis(Permission::USER_LEVEL_1)) : ?>
                    <button class="btn btn-primary btn-lg pull-right" id="saveInvoice" type="button">Sauvegarder</button>
                    <a class="btn btn-warning" target="_blank" href="/admin/invoice/index.php?invoice_id=<?php echo $id; ?>&print=y">Imprimer</a>
                    | <a class="btn btn-success" target="_blank" href="/admin/invoice/index-email.php?invoice_id=<?php echo $id; ?>&email=y&token=creedDefaultToken">Email</a>


                    <?php require __DIR__ . '/page/components/EmailOptions.vue'; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="chooseClientModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Choisir Client</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <input type="text" style="width: 100%;" autocomplete="off" id="client_search_token" class="form-control" placeholder="Type Client Info">
                </div>

                <div id="client-lists">
                    <div class="list-group">

                    </div>
                </div>

            </div>
<!--            <div class="modal-footer">
                <button type="button" id="client_submit" class="btn btn-primary" disabled="disabled"><i class="glyphicon glyphicon-check"></i> Submit</button>
            </div>-->
        </div>

    </div>
</div>

<input type="hidden" id="dots" value="<?php echo !empty($invoice['dots']) ? htmlentities($invoice['dots']) : null ?>">
<input type="hidden" id="shared" value="<?php echo !empty($invoice['shared']) ? htmlentities($invoice['shared']) : null ?>">
<?php require __DIR__ . '/page/components/__dots.vue'; ?>
<?php require __DIR__ . '/page/components/dots.vue'; ?>

<?php
        $script = <<<JS
        shared.ready = true;
        shared.invoiceId = '{$invoice['id']}';
JS;

    if (!empty($parts)) {
        $partsJson = json_encode($parts);
        $customJS = <<<JS
            function loadPart(parts)
            {
                if (parts && parts.length) {
                    parts.map(r => {
                        let sl = jQuery('input[value="' + r.name + '"]');
                        if (sl.length) {
                            sl.parent().trigger('click');
                            sl.parents('tr').find('.part_description').val(r.description);
                            sl.parents('tr').find('.part_hour').val(r.hour);
                            sl.parents('tr').find('.part_price').val(r.price);
                        }
                    });
                }
            }
            window.setTimeout(function () {
                loadPart($partsJson);
            }, 2000);
JS;
        echo '<script>' . $customJS . '</script>';
    }
    echo '<script>' . $script . '</script>';
?>
<?php include_once('footer.php'); ?>
