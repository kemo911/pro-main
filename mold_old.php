<?php
session_start();
function pre($arr)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    exit;
}

if( !empty( $_GET['mold_id'] ) ){
    $_SESSION['mold_id'] = $_GET['mold_id'];
}

//echo $_SESSION['mold_id'] ;exit;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
$success_msg = '';
if(!empty($_POST) && !empty($_SESSION['mold_id'])){
    unset($_POST['languageSelect']);
    $_POST['confirm_mold'] = 1;

    if( empty($_POST['estimator']) ){
        $_POST['estimator'] = !empty($_SESSION['jigowatt']['user_id']) ? $_SESSION['jigowatt']['user_id'] : 0;
    }

    $_POST['stripping_partial'] = isset($_POST['stripping_partial']) ? $_POST['stripping_partial'] : 0;
    $_POST['stripping_compact'] = isset($_POST['stripping_compact']) ? $_POST['stripping_compact'] : 0;
    $_POST['stripping_standard'] = isset($_POST['stripping_standard']) ? $_POST['stripping_standard'] : 0;
    $_POST['stripping_suv'] = isset($_POST['stripping_suv']) ? $_POST['stripping_suv'] : 0;
    $_POST['stripping_sky_roof'] = isset($_POST['stripping_sky_roof']) ? $_POST['stripping_sky_roof'] : 0;
    $_POST['stripping_dvd_video'] = isset($_POST['stripping_dvd_video']) ? $_POST['stripping_dvd_video'] : 0;
    $_POST['stripping_roof_support'] = isset($_POST['stripping_roof_support']) ? $_POST['stripping_roof_support'] : 0;

    $_POST['moulure_de_toit_d_text'] = !empty($_POST['moulure_de_toit_d_text']) ? $_POST['moulure_de_toit_d_text'] : '';
    $_POST['moulure_de_toit_g_text'] = !empty($_POST['moulure_de_toit_g_text']) ? $_POST['moulure_de_toit_g_text'] : '';

    $result = updateMold($_POST);

    if ( !empty($_POST['reclamation']) ) {
        createReclamationIfNotExists($_POST['reclamation'], array(
            'client_id' => $_POST['client_id'],
            'reclamation' => $_POST['reclamation'],
            'insurer' => $_POST['insurer'],
            'vin' => $_POST['vin'],
            'brand' => $_POST['brand'],
            'model' => $_POST['model'],
            'year' => $_POST['year'],
            'inventory' => $_POST['inventory'],
            'color' => $_POST['color'],
            'brake_type' => $_POST['brake_type'],
            'particular_area' => $_POST['particular_area'],
            'millage' => $_POST['millage'],
            'creation_style' => 'estimation',
        ));
    }

    $_SESSION['success_msg'] = '<strong>Success!</strong> Mold has been saved successfully.';
    flashMoldSession();
    header('Location: /admin/molds.php');
    exit;

}

protect("*");
$techGuys = getUsersByLevel(4);
$currentUser = getUserDetailsById($_SESSION['jigowatt']['user_id']);
$clients = getClients();

if (!empty($_GET['flush'])) {
    if ('go_ahead' == base64_decode($_GET['flush'])) {
        flashMoldSession();
    }
}

$newMold = false;

if (!empty($_GET['new']) && $_GET['new'] == 'true') {
    $newMold = true;
}

$assurers = array(
        'Promutuel' => 'Promutuel',
//        'SSQ' => 'SSQ',
//        'La Capital' => 'La Capital',
//        'Intact' => 'Intact',
//        "l\'unique" => "l\'unique"
);

//echo $_SESSION['mold_id']; exit;
$reclamationId = isset($_GET['reclamation_id']) && is_numeric($_GET['reclamation_id']) ? $_GET['reclamation_id'] : 0;
//START: Commented by Kajem
if ( empty($_SESSION['mold_id'] ) ) {
    $_SESSION['mold_id'] = createMoldDraft();

    $extraParam = $reclamationId ? '&reclamation_id=' . $reclamationId : '';

    header('Location: /admin/mold.php?new=true' . $extraParam);
    exit();
}
//END: Commented by Kajem
$mold = array();
$mold = getMold($_SESSION['mold_id']);

$reclamationData = getReclamation($reclamationId);

if ( !empty($reclamationData) ) {
    $arrayMergeFields = array(
        'client_id', 'reclamation', 'insurer', 'vin',
        'brand', 'model', 'year', 'inventory', 'color',
        'brake_type', 'particular_area', 'millage'
    );


    foreach ( $arrayMergeFields as $mergeField ) {
        if ( empty($mold[$mergeField]) && !empty($reclamationData[$mergeField]) ) {
            $mold[$mergeField] = $reclamationData[$mergeField];
        }
    }

    $clientDetails = getClient($mold['client_id']);
    $clientMergeFields = array(
        'f_name', 'l_name', 'company', 'tel', 'email'
    );
    foreach ( $clientMergeFields as $mergeField ) {
        if ( empty($mold[$mergeField]) && !empty($clientDetails[$mergeField]) ) {
            $mold[$mergeField] = $clientDetails[$mergeField];
        }
    }
}

if( !empty( $_GET['mold_id'] ) && empty($mold) ){
    $_SESSION['success_msg'] = '<strong>Error!</strong> Invalid mold.';
    flashMoldSession();
    header('Location: /admin/molds.php');
    exit;
}
//pr($mold);
$moldPhotos = isset($_SESSION['mold_id']) ? getMoldPhotos($_SESSION['mold_id']) : array();

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
    15 => 'OTHER',
    'stripping' => 'Stripping',
    'other_fees' => 'Other fees',
    'glazier' => 'Glazier',
    'work_force' => 'Work force',
    'parts' => 'Parts'
);

include_once 'header.php';
echo '<script> var clients = ' . (json_encode($clients)) . '; </script>';
echo '<script> var javascriptObject = ' . (empty($invoice['javascript_object']) ? '{}' : $invoice['javascript_object']) . '; </script>';
echo '<script> var savedRequest = ' . (empty($invoice['latest_request']) ? '{}' : $invoice['latest_request']) . '; </script>';
?>
<div id="page-name" data-page-name="mold" data-status="draft"
     data-mold-id="<?php echo isset($_SESSION['mold_id']) ? $_SESSION['mold_id'] : 0; ?>"></div>
<div class="container">
    <div class="row">
        <?php if( !empty($success_msg) ): ?>
        <div class="alert alert-success fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <?php echo $success_msg; ?>
        </div>
        <?php endif; ?>
        <form action="" method="post" id="moldForm">

            <div class="col-md-12" style="padding-bottom: 20px;">
                <img src="assets/img/logo.jpg" alt="Promutuel Insurance">
            </div>

            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="col-md-12">
                        <div class="form-group" style="padding: 10px 0;">
                            <select name="client_id" class="form-control" id="clients"
                                    style="width: 45%; float: left; margin-right: 10%;">
                                <option value=""> Choisir un client</option>
                                <?php foreach ($clients as $client): ?>
                                    <?php
                                    $selected = '';

                                    if(!empty($mold['client_id']) && $mold['client_id'] == $client['clientid'] )
                                        $selected = 'selected';
                                    ?>
                                    <option <?php echo $selected; ?> value="<?php echo $client['clientid'] ?>"><?php echo $client['fname'] . ' ' . $client['fname']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a style="width: 45%; float: right;" class="btn btn-info text-right"
                               href="/admin/client.php/"><i
                                        class="glyphicon glyphicon-plus"></i> Cr??er un client</a>
                            <span class="clearfix"></span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12" style="border: 1px solid #e4e4e4; padding-top: 10px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Date:</label>
                                    <input type="text" class="form-control" name="date" id="date"
                                           value="<?php echo !empty($mold['date']) ? str_replace('-', '/', $mold['date']) : date('Y/m/d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reclamation"># R??clamation:</label>
                                    <input class="form-control" name="reclamation" id="reclamation" value="<?php echo !empty($mold['reclamation']) ? $mold['reclamation'] : ''; ?>">
                                </div>
                            </div>
                            <div class="col-md-12"
                                 style="border: 1px solid #e4e4e4; padding: 10px; padding-bottom: 0; margin-bottom: 10px;">
                                <div class="form-group">
                                    <label for="nom">Pr??nom:</label>
                                    <input type="text" class="form-control" id="client_lname" name="f_name" value="<?php echo !empty($mold['f_name']) ? $mold['f_name'] : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="prenom">Nom:</label>
                                    <input type="text" class="form-control" id="client_fname" name="l_name" value="<?php echo !empty($mold['l_name']) ? $mold['l_name'] : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="prenom">Compagnie:</label>
                                    <input type="text" class="form-control" id="client_cie" name="company" value="<?php echo !empty($mold['company']) ? $mold['company'] : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="prenom">T??l:</label>
                                    <input type="text" class="form-control" id="client_tel1" name="tel" value="<?php echo !empty($mold['tel']) ? $mold['tel'] : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="prenom">Courriel:</label>
                                    <input type="text" class="form-control" id="client_email" name="email" value="<?php echo !empty($mold['email']) ? $mold['email'] : ''; ?>">
                                </div>
                                <div class="form-group">
<!--                                    <select name="insurer" class="form-control" id="clients">-->
<!--                                        --><?php //foreach ($assurers as $key => $value): ?>
<!--                                            --><?php
//                                            $assurer_selected = '';
//                                            if( $mold['assureur'] == $key) $assurer_selected = 'selected';
//                                            ?>
<!--                                        <option --><?php //echo $assurer_selected; ?><!-- value="--><?php //echo $key ?><!--">--><?php //echo $value ?><!--</option>-->
<!--                                        --><?php //endforeach; ?>
<!--                                    </select>-->
                                    <input type="hidden" value="Promutuel">
                                </div>
                            </div>
                        </div>
                        <div class="input-group" style="padding: 10px 0">
                            <input type="text" placeholder="Type VIN" class="form-control col-md-9" name="vin" id="vin" value="<?php echo !empty($mold['vin']) ? $mold['vin'] : ''; ?>">
                            <span class="input-group-btn">
                                    <button class="btn btn-primary" id="scanVIN" type="button">Scan VIN</button>
                                </span>
                        </div>
                        <div class="col-md-12" id="vinArea" style="border: 1px solid #e4e4e4; padding-top: 10px;">
                            <div id="vinAreaOverlay" style="display: none;"><p><img
                                            src="/admin/assets/img/ajax-loader.gif">
                                </p></div>


                            <div class="form-group">
                                <div id="vinResults">
                                    <div class="list-group">
                                        <!--                                    <a class="list-group-item selectClient" href="javascript:void(0);">Item 1</a>-->
                                    </div>
                                </div>
                                <div id="vinOutput">
                                    <?php
                                    $number_type_fields = array('year', 'inventory', 'millage');
                                    foreach (array(
                                                       'brand' => 'Marque',
                                                       'model' => 'Mod??le',
                                                       'year' => 'Ann??e',
                                                       'inventory' => '#Inventaire',
                                                       'color' => 'Couleur',
                                                       'particular_area' => 'P.A',
                                                       'brake_type' => 'B.T',
                                                       'millage' => 'Millage',
                                                   ) as $k => $v): ?>
                                        <div class="col-md-6 <?php echo $k == 'sn' ? 'hidden' : ''; ?>">
                                            <div class="form-group">
                                                <label for="<?php echo $k; ?>"><?php echo $v; ?>:</label>
                                                <input type="<?php echo in_array($k, $number_type_fields) ? 'number' : 'text'; ?>" class="form-control"
                                                       id="<?php echo $k; ?>" name="<?php echo $k; ?>" value="<?php echo !empty($mold[$k]) ? $mold[$k] : ''; ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"
                             style="border: 1px solid #e4e4e4; padding-top: 10px; min-height: 100px; padding-bottom: 15px; margin-bottom: 5px;">
                            <h4> Photos: </h4>
                            <div action="/admin/ajax/ajax_mold_file_upload.php"  id="dropzone" class="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file" multiple/>
                                </div>
                            </div>

                            <!--                        --><?php //if (  $newMold == false ): ?>
                            <div id="photo_view">
                                <?php foreach ($moldPhotos as $photo): ?>
                                    <a href="/<?php echo $photo['photo_url']; ?>"
                                       data-lightbox="image-<?php echo $photo['id']; ?>">
                                        <img class="img img-thumbnail" data-lightbox="image-<?php echo $photo['id']; ?>"
                                             src="/<?php echo $photo['photo_url']; ?>" width="100px">
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <!--                        --><?php //endif; ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 rightside-input">
                <div class="panel panel-primary" id="mold-critical-print-section">
                    <div class="text-center droit-passenger">
                        DROIT (PASSAGER)
                    </div>
                    <div class="panel-body">
                        <div class="col-md-3 avant">
                            <div class="avant1">
                                <div class="down-window"></div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="avant1" name="avant1" value="<?php echo !empty($mold['avant1']) ? $mold['avant1'] : ''; ?>"/>
                                </div>
                            </div>
                            <div class="form-group avant2">
                                <textarea class="form-control" id="avant2" name="avant2" rows="10"> <?php echo !empty($mold['avant2']) ? $mold['avant2'] : ''; ?> </textarea>
                            </div>
                            <div class="avant3">
                                <input type="text" class="form-control" id="avant3" name="avant3" value="<?php echo !empty($mold['avant3']) ? $mold['avant3'] : ''; ?>">
                                <div class="up-window"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="droit1">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="droit1" name="droit1" value="<?php echo !empty($mold['droit1']) ? $mold['droit1'] : ''; ?>">
                                </div>
                                <div class="form-inline">
                                    <div class="checkbox text-right" style="padding-right:1px">
                                        <label class="checkbox-label">LECHE VITRE <span class="custom-checkbox <?php echo !empty($mold['droit_leche_vitre1']) ? 'checked' : ''; ?>"></span></label>
                                        <input id="droit_leche_vitre1" name="droit_leche_vitre1" value="<?php echo !empty($mold['droit_leche_vitre1']) ? $mold['droit_leche_vitre1'] : 0; ?>"  type="hidden">

                                        <label class="checkbox-label">MOULURE DE CITIERE <span class="custom-checkbox <?php echo !empty($mold['droit_moulure_de_citiere1']) ? 'checked' : ''; ?>"></span></label>
                                        <input id="droit_moulure_de_citiere1" name="droit_moulure_de_citiere1" value="<?php echo !empty($mold['droit_moulure_de_citiere1']) ? $mold['droit_moulure_de_citiere1'] : 0; ?>" type="hidden"  >

                                        <label class="checkbox-label">APPLIQUE <span class="custom-checkbox <?php echo !empty($mold['droit_applique1']) ? 'checked' : ''; ?>"></span></label>
                                        <input id="droit_applique1" name="droit_applique1" value="<?php echo !empty($mold['droit_applique1']) ? $mold['droit_applique1'] : 0; ?>" type="hidden">
                                    </div>
                                </div>
                            </div>
                            <div class="droit2">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="droit2" name="droit2" value="<?php echo !empty($mold['droit2']) ? $mold['droit2'] : ''; ?>">
                                </div>
                                <div class="form-inline">
                                    <div class="checkbox">
                                        <label class="checkbox-label"><span class="custom-checkbox <?php echo !empty($mold['droit_leche_vitre2']) ? 'checked' : ''; ?>"></span> LECHE VITRE</label>
                                        <input id="droit_leche_vitre2" name="droit_leche_vitre2" value="<?php echo !empty($mold['droit_leche_vitre2']) ? $mold['droit_leche_vitre2'] : 0; ?>" type="hidden">

                                        <label class="checkbox-label"><span class="custom-checkbox <?php echo !empty($mold['droit_moulure_de_citiere2']) ? 'checked' : ''; ?>"></span> MOULURE DE CITIERE</label>
                                        <input id="droit_moulure_de_citiere2" name="droit_moulure_de_citiere2" value="<?php echo !empty($mold['droit_moulure_de_citiere2']) ? $mold['droit_moulure_de_citiere2'] : 0; ?>" type="hidden">

                                        <label class="checkbox-label"><span class="custom-checkbox <?php echo !empty($mold['droit_applique2']) ? 'checked' : ''; ?>"></span> APPLIQUE</label>
                                        <input id="droit_applique2" name="droit_applique2" value="<?php echo !empty($mold['droit_applique2']) ? $mold['droit_applique2'] : 0; ?>" type="hidden">
                                    </div>
                                </div>
                            </div>
                            <div class="droit3">
                                <div class="form-group">
                                    <textarea class="form-control" id="droit3" name="droit3"
                                              style="height: 113px;"> <?php echo !empty($mold['droit1']) ? $mold['droit1'] : ''; ?> </textarea>
                                </div>
                            </div>

                            <div class="clearfix"></div>

                            <div class="longeron-droit">
                                <input type="text" class="form-control" id="longeron_droit" placeholder="LONGERON DROIT" name="longeron_droit" value="<?php echo !empty($mold['longeron_droit']) ? $mold['longeron_droit'] : ''; ?>">
                            </div>
                            <div class="moulure-de-toit-d">
                                <div class="top-yellow-bg"></div>
                                <div class="form-group">
                                    <div class="checkbox" style="padding-right: 10px;">
                                        <div class="form-group">
                                            <input type="text" class="moulure-de-toit-d-text" name="moulure_de_toit_d_text" value="<?php echo $mold['moulure_de_toit_d_text']; ?>">
                                        <label for="moulure_de_toit_d" class="checkbox-label">MOULURE DE TOIT D.<span class="custom-checkbox <?php echo !empty($mold['moulure_de_toit_d']) ? 'checked' : ''; ?>"></span></label>
                                        <input id="moulure_de_toit_d" name="moulure_de_toit_d" value="<?php echo !empty($mold['moulure_de_toit_d']) ? $mold['moulure_de_toit_d'] : 0; ?>" type="hidden" >
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="moulure-de-toit-g-text" name="moulure_de_toit_g_text" value="<?php echo $mold['moulure_de_toit_g_text']; ?>">
                                        <label for="moulure_de_toit_g" class="checkbox-label">MOULURE DE TOIT G.<span class="custom-checkbox <?php echo !empty($mold['moulure_de_toit_g']) ? 'checked' : ''; ?>"></span></label>
                                        <input id="moulure_de_toit_g" name="moulure_de_toit_g" value="<?php echo !empty($mold['moulure_de_toit_g']) ? $mold['moulure_de_toit_g'] : '0'; ?>" type="hidden" >
                                        </div>
                                    </div>
                                </div>
                                <div class="bottom-yellow-bg"></div>
                            </div>
                            <div class="longeron-gauche">
                                <input placeholder="LONGERON GAUCHE" type="text" class="form-control" id="longeron_gauche" name="longeron_gauche" value="<?php echo !empty($mold['longeron_gauche']) ? $mold['longeron_gauche'] : ''; ?>">
                            </div>

                            <div class="gauche-vitre1">
                                <div class="form-inline">
                                    <div class="checkbox text-right">
                                        <label class="checkbox-label">LECHE VITRE <span class="custom-checkbox <?php echo !empty($mold['gauche_leche_vitre1']) ? 'checked' : ''; ?>"></span></label>
                                        <input id="gauche_leche_vitre1" name="gauche_leche_vitre1" value="<?php echo !empty($mold['gauche_leche_vitre1']) ? $mold['gauche_leche_vitre1'] : 0; ?>" type="hidden">

                                        <label class="checkbox-label">MOULURE DE CITIERE <span class="custom-checkbox <?php echo !empty($mold['gauche_moulure_de_citiere1']) ? 'checked' : ''; ?>"></span></label>
                                        <input id="gauche_moulure_de_citiere1" name="gauche_moulure_de_citiere1" value="<?php echo !empty($mold['gauche_moulure_de_citiere1']) ? $mold['gauche_moulure_de_citiere1'] : 0; ?>" type="hidden">

                                        <label class="checkbox-label">APPLIQUE <span class="custom-checkbox <?php echo !empty($mold['gauche_applique1']) ? 'checked' : ''; ?>"></span></label>
                                        <input id="gauche_applique1" name="gauche_applique1" value="<?php echo !empty($mold['gauche_applique1']) ? $mold['gauche_applique1'] : 0; ?>" type="hidden">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="gauche1" name="gauche1" value="<?php echo !empty($mold['gauche1']) ? $mold['gauche1'] : ''; ?>">
                                </div>
                            </div>
                            <div class="gauche-vitre2">
                                <div class="form-inline">
                                    <div class="checkbox">
                                        <label class="checkbox-label"><span class="custom-checkbox <?php echo !empty($mold['gauche_leche_vitre2']) ? 'checked' : ''; ?>"></span> LECHE VITRE</label>
                                        <input id="gauche_leche_vitre2" name="gauche_leche_vitre2" value="<?php echo !empty($mold['gauche_leche_vitre2']) ? $mold['gauche_leche_vitre2'] : 0; ?>" type="hidden">

                                        <label class="checkbox-label"><span class="custom-checkbox <?php echo !empty($mold['gauche_moulure_de_citiere2']) ? 'checked' : ''; ?>"></span> MOULURE DE CITIERE</label>
                                        <input id="gauche_moulure_de_citiere2" name="gauche_moulure_de_citiere2" value="<?php echo !empty($mold['gauche_moulure_de_citiere2']) ? $mold['gauche_moulure_de_citiere2'] : 0; ?>" type="hidden">

                                        <label class="checkbox-label"><span class="custom-checkbox <?php echo !empty($mold['gauche_applique2']) ? 'checked' : ''; ?>"></span> APPLIQUE</label>
                                        <input id="gauche_applique2" name="gauche_applique2" value="<?php echo !empty($mold['gauche_applique2']) ? $mold['gauche_applique2'] : 0; ?>" type="hidden">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="gauche2" name="gauche2" value="<?php echo !empty($mold['gauche2']) ? $mold['gauche2'] : ''; ?>">
                                </div>
                            </div>
                            <div class="gauche3">
                                <div class="form-group" style="margin-left: 5px;">
                                    <textarea class="form-control" id="gauche3" name="gauche3" style="height: 113px;"
                                              ><?php echo !empty($mold['gauche3']) ? $mold['gauche3'] : ''; ?> </textarea>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-md-3 vitre">
                            <div class="vitre-custcode1">
                                <div class="down-window"></div>
                                <div class="form-group">
                                    <input type="text" name="vitre_custcode_txt_1" value="<?php echo !empty($mold['vitre_custcode_txt_1']) ? $mold['vitre_custcode_txt_1'] : ''; ?>" class="form-control"/>
                                    <label for="vitre_custcode1" class="checkbox-label">
                                        <span class="custom-checkbox vitre-custcode1-check <?php echo !empty($mold['vitre_custcode1']) ? 'checked' : ''; ?>"></span>
                                    </label>
                                    <input type="hidden"  name="vitre_custcode1" value="<?php echo !empty($mold['vitre_custcode1']) ? $mold['vitre_custcode1'] : 0; ?>"/>
                                    <span class="span1">VITRE/</span><br/>
                                    <span class="span2">MOULURE CUSTOME</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" id="vitre1" name="vitre1"
                                          style="width: 30%; float: left; height: 213px;"> <?php echo !empty($mold['vitre1']) ? $mold['vitre1'] : ''; ?> </textarea>
                                <textarea class="form-control vitre_custcode2" id="vitre2" name="vitre2"  cols="20"
                                          style="width: 70%; float: left; height: 213px;"> <?php echo !empty($mold['vitre2']) ? $mold['vitre2'] : ''; ?> </textarea>
                            </div>
                            <div class="clearfix"></div>
                            <div class="vitre-custcode2">
                                <div class="up-window"></div>
                                <label for="vitre_custcode2" class="checkbox-label">
                                    <span class="custom-checkbox vitre-custcode2-check <?php echo !empty($mold['vitre_custcode2']) ? 'checked' : ''; ?>"></span>
                                </label>
                                <input type="hidden" id="vitre_custcode2" name="vitre_custcode2" value="<?php echo !empty($mold['vitre_custcode2']) ? $mold['vitre_custcode2'] : 0; ?>"/>
                                <span class="span1">VITRE/</span><br/>
                                <span class="span2">MOULURE CUSTOME</span>
                                <input type="text" name="vitre_custcode_txt_2" value="<?php echo !empty($mold['vitre_custcode_txt_2']) ? $mold['vitre_custcode_txt_2'] : ''; ?>" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="text-center" style="padding-bottom: 20px; font-size: 24px; color: #ff8f73;">GAUCHE (CONDUCTEUR)</div>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <div style="display: none;"  class="col-md-4">
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-primary">
                                    <div class="panel-heading" role="tab" id="strippingHead">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                               href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                D??garnissage
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                         aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <div class="form-inline">
                                                <div class="checkbox"
                                                     style="border-bottom: 1px solid #e4e4e4; padding-bottom: 10px;">
                                                    <label><input id="stripping_partial" name="stripping_partial" data-price="150"
                                                                  class="stripping" value="1"
                                                                  type="checkbox" <?php echo !empty($mold['stripping_partial']) ? 'checked = "checked"' : ''; ?> > Partial stripping</label>
                                                    <label><input id="stripping_compact" name="stripping_compact" class="stripping"
                                                                  data-price="250" value="1" type="checkbox" <?php echo !empty($mold['stripping_compact']) ? 'checked = "checked"' : ''; ?> >
                                                        Compact</label>
                                                    <label><input id="stripping_standard" name="stripping_standard" class="stripping"
                                                                  data-price="325" value="1" type="checkbox" <?php echo !empty($mold['stripping_standard']) ? 'checked = "checked"' : ''; ?> >
                                                        Standard</label>
                                                    <label><input id="stripping_suv" name="stripping_suv" class="stripping" data-price="400"
                                                                  value="1" type="checkbox" <?php echo !empty($mold['stripping_suv']) ? 'checked = "checked"' : ''; ?> > SUV</label>
                                                    <label><input id="stripping_sky_roof" name="stripping_sky_roof" class="stripping"
                                                                  data-price="75" value="1" type="checkbox" <?php echo !empty($mold['stripping_sky_roof']) ? 'checked = "checked"' : ''; ?> > Sky
                                                        roof</label>
                                                    <label><input id="stripping_dvd_video_acc" name="stripping_dvd_video" class="stripping"
                                                                  data-price="75" value="1"
                                                                  type="checkbox" <?php echo !empty($mold['stripping_dvd_video']) ? 'checked = "checked"' : ''; ?> > DVD Video Acc.</label>
                                                    <label><input id="stripping_roof_support" name="stripping_roof_support" class="stripping"
                                                                  data-price="75" value="1" type="checkbox" <?php echo !empty($mold['stripping_roof_support']) ? 'checked = "checked"' : ''; ?> >
                                                        Roof support</label>
                                                </div>
                                            </div>
                                            <div class="input-details" style="padding-top: 10px;">
                                                <div class="form-group">
                                                    <label for="stripping_note">Note:</label>
                                                    <textarea name="stripping_note" class="form-control"
                                                              id="stripping_note"
                                                              style="width: 100%;"><?php echo !empty($mold['stripping_note']) ? $mold['stripping_note'] : ''; ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <button id="stripping_add" type="button" class="btn btn-primary btn-md">Add to
                                                        Invoice
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-primary">
                                    <div class="panel-heading" role="tab" id="otherFeesHead">
                                        <h4 class="panel-title">
                                            <a class="collapsed" role="button" data-toggle="collapse"
                                               data-parent="#accordion" href="#collapseTwo" aria-expanded="false"
                                               aria-controls="collapseTwo">
                                                Other Fees
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                         aria-labelledby="headingTwo">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="other_fees_description">Description: </label>
                                                <textarea name="other_fees_description" id="other_fees_description"  class="form-control"><?php echo !empty($mold['other_fees_description']) ? $mold['other_fees_description'] : ''; ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <button id="other_fees_add_button" type="button" class="btn btn-primary btn-md">Add to
                                                    Invoice
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="damages">Notes: </label>
                                <textarea name="damages_notes" id="damages" class="form-control"
                                          rows="11"><?php echo !empty($mold['damages_notes']) ? $mold['damages_notes'] : ''; ?></textarea>
                            </div>
                            <div class="form-group" style="padding-top: 10px;">
                                <a href="/admin/main.php" id="create-invoice-button" data-value="Create Invoice"
                                   class="btn btn-danger">+ CR??ER UNE FACTURE</a>
                                <input type="submit" style="margin-right: 16px;" id="save-mold"
                                       value="SAVE" class="btn btn-danger"/>
                                <a href="javascript:void(0);" id="print-preview" class="btn btn-primary">Imprimer</a>
                            </div>
                        </div>
                        <div class="col-md-6" style="display: none;">
                            <div>
                                <p>Je soussign??(e), assur??(e) ou repr??sentant de l???assur??(e), d??clare avoir pris
                                    connaissance des r??parations de d??bosselage sans peinture ex??cuter sur mon v??hicule
                                    par Eco Solution Gr??le, et je m???en d??clare enti??rement satisfait(e).</p>
                            </div>

                            <div id="signature">
                                <?php if ( !empty($mold['signature_img']) ): ?>
                                    <img src="<?php echo $mold['signature_img']; ?>" alt="Signature Image">
                                <?php endif; ?>
                            </div>
                            
                            <div id="signature-pad" class="clearfix">
                                <canvas class="pad"></canvas>
                                <br>
                                <a href="#" class="btn btn-xs btn-danger" id="signature-clear">Clear Signature</a>
                            </div>
                            <div class="username">
                                <?php
                                if( !empty($_GET['mold_id']) ){
                                    $estimator = !empty($mold['username']) ? $mold['username'] : '';
                                }else{
                                    $estimator = !empty($_SESSION['jigowatt']['username']) ? $_SESSION['jigowatt']['username'] : '';
                                }
                                echo $estimator.'<input type="hidden" name="estimator" value="'.$estimator.'"/>'
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<div id="print-section" style="display: none;">
    <h2 class="text-center">Estimation</h2>
    <div id="client-section">

        <table class="table table-responsive">
            <tr>
                <th>Date:</th>
                <td><?php echo date('F j, Y', strtotime($mold['date'])) ;?></td>
                <th>R??clamation:</th>
                <td><span class="label label-warning">#<?php echo $mold['reclamation'] ;?></span></td>
            </tr>

            <tr>
                <th>Nom du client:</th>
                <td><?php echo $mold['f_name'] ;?> <?php echo $mold['l_name'] ;?></td>
                <th>Courriel du client:</th>
                <td><?php echo $mold['email'] ;?></td>

            </tr>

            <tr>
                <th>Compagnie:</th>
                <td><?php echo $mold['company'] ;?></td>

                <th>T??l??phone:</th>
                <td><?php echo $mold['tel'] ;?></td>
            </tr>

            <tr>
                <th>Assureure:</th>
                <td><?php echo $mold['insurer'] ;?></td>
            </tr>

        </table>
    </div>
    <hr>
    <div id="critical-section"></div>
    <hr>
    <div id="bottom-section">
        <p><strong>Notes: </strong></p>
        <p><?php echo !empty($mold['damages_notes']) ? $mold['damages_notes'] : ''; ?></p>
    </div>
</div>

<link href="./assets/css/mold.css?askgdffhf1" rel="stylesheet">
<style>
    @media print
    {
        html, body {
            height:100%;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden;
        }

        body * { visibility: hidden; }
        #print-section * { visibility: visible; }
        #print-section { position: absolute; top: 0; left: 0; }
        #critical-section canvas {
            width: 730px !important;
            height: 550px!important;
        }
    }
</style>

<?php ob_start(); ?>
<script>
$(function() {
    $('#print-preview').click(function() {
        $('#print-section').show();
        var h2c = html2canvas(document.querySelector('#mold-critical-print-section'));
        h2c.then(function(canvas) {
            $('#critical-section').html(canvas);
            window.setTimeout(function () {
                window.print();
                $('#print-section').hide();
            }, 1500);
        });
    });
});
</script>
<?php $pageSpecificJS = ob_get_clean(); ?>

<?php include_once('footer.php'); ?>

