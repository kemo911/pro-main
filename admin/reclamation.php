<?php

include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');

include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');

protect( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3]));

//denied_for([BsgUser::TECH]);

$reclamation = null;

$reclamationDetails = array();

if ( !empty($_GET['id']) ) {

    $reclamation = getReclamation($_GET['id']);

    if ( ! $reclamation ) {

        header('Location: /admin/reclamation.php');

        exit;

    }

}

$reclamationDetails = $reclamation;

$clients = getClients();



if ( !$reclamation ) {

    $token = getToken();

} else {

    $token = $reclamation['token'];

}

$_SESSION['appointment.token'] = $token;

$appointmentPhotos = getAppointmentPhotosByToken( $token );

$pageClass = 'reclamation';

$techGuys = getUsersByLevel( 2 );

$estimatorGuys = getUsersByLevel( 3 );



include_once('header.php');

?>





<div class="container">

    <div class="row">

        <div class="col-md-12">
            <?php if (!empty($_SESSION['message.reclamation'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message.reclamation']; unset($_SESSION['message.reclamation']); ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">



            <div class="panel panel-primary">

                <div class="panel-heading">

                    <h2 class="text-center text-uppercase text-"><strong>Réclamation</strong></h2>

                    <br>

                    <p><a class="btn btn-danger" href="/admin/client.php?redirect_to=/admin/reclamation.php">Créer un client</a></p>

                </div>

                <div class="panel-body">



                    <input type="hidden" id="token" value="<?php echo $_SESSION['appointment.token']; ?>">

                    <input type="hidden" id="reclamation_id" value="<?php echo !empty($_GET['id']) ? $_GET['id'] : ''; ?>">



                    <div>

                        <input type="text" readonly="readonly" name="reclamation_date" id="reclamation_date" value="<?php echo date('Y-m-d'); ?>" class="form-control">

                    </div>



                    <br>



                    <div id="appointment-common-details" class="form-group">

                        <select name="client_id" id="client_id" class="form-control">

                            <option value="">-- Choisir client --</option>

                            <?php foreach ( $clients as $client ): ?>

                                <option <?php echo (!empty($reclamationDetails) && $reclamationDetails['client_id'] == $client['clientid']) ? ' selected="selected" ' : '' ?> value="<?php echo $client['clientid']; ?>"><?php echo $client['fname']; ?> <?php echo $client['lname']; ?></option>

                            <?php endforeach; ?>

                        </select>

                        <hr>

                        <div id="client-division" style="display: none;">

                            <table class="table table-bordered">

                                <tr>

                                    <th>Nom: </th>

                                    <td id="client-name"></td>

                                </tr>

                                <tr>

                                    <th>Courriel: </th>

                                    <td id="client-email"></td>

                                </tr>

                                <tr>

                                    <th>Compagnie: </th>

                                    <td id="client-company"></td>

                                </tr>

                                <tr>

                                    <th>Téléphone: </th>

                                    <td id="client-telephone"></td>

                                </tr>

                            </table>

                        </div>

                        <div class="form-group">

                            <label for="reclamation">RÉCLAMATION:</label>

                            <input class="form-control reclamation-check" value="<?php echo !empty($reclamationDetails) ? $reclamationDetails['reclamation'] : '';  ?>" name="reclamation" id="reclamation">

                        </div>

<!--                        <div class="form-group">-->

<!--                            <label for="insurer">ASSUREURE:</label>-->
                            <input type="hidden" value="Promutuel" name="insurer" id="insurer">
<!--                            <select name="insurer" class="form-control" id="insurer">-->

<!--                                <option value="Promutuel">Promutuel</option>-->

<!--                                <option --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer']  == 'Desjardins') ? ' selected="selected" ' : '' ?><!-- value="Desjardins">Desjardins</option>-->
<!---->
<!--                                <option --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer']  == 'SSQ') ? ' selected="selected" ' : '' ?><!-- value="SSQ">SSQ</option>-->
<!---->
<!--                                <option  --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer'] == 'La Capital') ? ' selected="selected" ' : '' ?><!--value="La Capital">La Capital</option>-->
<!---->
<!--                                <option  --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer'] == 'Intact') ? ' selected="selected" ' : '' ?><!--value="Intact">Intact</option>-->
<!---->
<!--                                <option  --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer'] == "l\&#39;unique") ? ' selected="selected" ' : '' ?><!--value="l\'unique"> L'unique</option>-->

<!--                            </select>-->

<!--                        </div>-->

<!--                        <hr>-->

                        <div class="col-md-12" id="vinArea" style="border: 1px solid #e4e4e4; padding-top: 10px;">

                            <div id="vinAreaOverlay" style="display: none;"><p><img src="/admin/assets/img/ajax-loader.gif"></p></div>



                            <div class="input-group">

                                <input type="text" placeholder="Entrez NIV" value="<?php echo !empty($reclamationDetails) ? $reclamationDetails['vin'] : '';  ?>" class="form-control col-md-9" id="vin">

                                <span class="input-group-btn">

                               <button class="btn btn-primary" id="scanVIN" type="button">Recherche</button>

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

                                                        //'pa' => 'P.A',

                                                        //'bt' => 'B.T',

                                                        'millage' => 'Odomètre',

                                                    ) as $k => $v ): $key = $k;

                                        if ( $k == 'pa' ) $key = 'particular_area';

                                        if ( $k == 'bt' ) $key = 'brake_type';

                                        ?>

                                        <div class="col-md-6 <?php echo $k == 'sn' ? 'hidden' : ''; ?>">

                                            <div class="form-group">

                                                <label for="<?php echo $k; ?>"><?php echo $v; ?>:</label>

                                                <input type="text" class="form-control" id="<?php echo $k; ?>" value="<?php echo isset($reclamationDetails[$key]) ? $reclamationDetails[$key] : '';  ?>">

                                            </div>

                                        </div>

                                    <?php endforeach; ?>
                                    <input type="hidden" name="particular_area" value="<?php echo isset($reclamationDetails['particular_area']) ? $reclamationDetails['particular_area'] : '';  ?>" id="pa">
                                    <input type="hidden" name="brake_type" value="<?php echo isset($reclamationDetails['brake_type']) ? $reclamationDetails['brake_type'] : '';  ?>" id="bt">

                                </div>

                            </div>

                        </div>

                        <hr>

                        <div class="col-md-12" style="border: 1px solid #e4e4e4; padding-top: 10px; min-height: 100px; padding-bottom: 15px; margin-bottom: 5px;">
                            <h4>Photos: </h4>
                            <form action="/admin/ajax/ajax_file_upload_for_appointment.php" id="dropzone" class="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file" multiple />
                                </div>
                            </form>

                            <!--                        --><?php //if ( $newInvoice == false ): ?>
                            <div id="photo_view">
                                <?php foreach ( $appointmentPhotos as $photo ): ?>
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

        </div>
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading"></div>
                <div class="panel-body">
                    <label for="call_back" class="checkbox-label"><input type="checkbox" <?php echo isset($reclamationDetails['call_back']) && $reclamationDetails['call_back'] == 1 ? "checked" : "" ?> name="call_back" id="call_back"> Rappel pour rendez-vous</label>
                    <button type="button" style="margin-top: 20px;" class="btn btn-primary btn-block create-reclamation" value="book" id="create-reclamation">Créer un rendez-vous</button>
                    <br>
                    <button type="button" style="margin-top: 20px;" class="btn btn-success btn-block create-reclamation" value="save" id="save-reclamation">Sauvegarder</button>
                    <br>
                    <div id="message-area" class="text-danger" style="margin-top: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<input type="hidden" id="cid_val" value="<?php echo !empty($_GET['client_id']) ? $_GET['client_id'] : 0; ?>">

<?php

$pageSpecificJS = <<<EOT
<script>
    
    $(function() {
        setTimeout(function() {
            if ( $('#cid_val').val() > 0 ) {
                $('#client_id').val($('#cid_val').val()).trigger('change');
            }
        }, 1500);
        $('select#client_id').select2();
    });        
    
</script>
EOT;
;

?>

<?php include_once('footer.php'); ?>



