<?php

if ( ! GIVE_ACCESS ) {
    die('No direct access.');
}
?>

<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">Changez la date pour voir l'horaire</div>
        <div class="panel-body">
            <div class="form-group">
                <label for="appointment-day"></label>
                <input id="appointment-day" value="<?php echo $_GET['date']; ?>" type="text" class="form-control" readonly="readonly"/>
                <span class="help-block text-info">Vous pouvez navigez les dates en utilisant le "widget".</span>
            </div>
            <hr>

            <?php if ( count($daySchedules) == 0 ): ?>
                    <div class="alert alert-danger">Pas <span class="label label-success"><?php echo $type; ?></span> d'horaire disponible le <span class="label label-info"><?php echo $date; ?></span>.
                        <br>
                        <br>
                        <a class="btn btn-xs btn-primary" href="/admin/schedule_create.php?date=<?php echo $date; ?>">Créer un horaire</a></div>
            <?php else: ?>
                <table id="schedule-table" class="table">
                    <thead>
                    <tr class="info">
                        <!--                        <th>ID</th>-->
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Horraire</th>
                        <th>Heure de rendez-vous</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $daySchedules as $schedule ): ?>
                        <tr>
                            <!--                            <td>--><?php //echo $schedule['id']; ?><!--</td>-->
                            <td><?php echo getUserNameById($schedule['user_id']); ?></td>
                            <td><?php echo $schedule['address']; ?></td>
                            <td><span class="label label-info"><?php echo $schedule['start_time']; ?></span> to <span class="label label-success"><?php echo $schedule['end_time']; ?></span></td>
                            <td><span class="badge badge-info"><?php echo $schedule['time_block']; ?></span> mins</td>
                            <td><a class="use-block btn btn-xs btn-info"
                                   data-schedule-id="<?php echo $schedule['id']; ?>"
                                   data-user-id="<?php echo $schedule['user_id']; ?>"
                                   data-user-name="<?php echo getUserNameById($schedule['user_id']); ?>"
                                   data-user-address="<?php echo $schedule['address']; ?>"
                                   href="javascript:void(0);">Horaire <i class="glyphicon glyphicon-chevron-right"></i></a></td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
                <input type="hidden" id="schedule-id">
                <input type="hidden" id="tech-id">
<!--                <input type="hidden" id="tech-address">-->
            <?php endif; ?>

        </div>
    </div>
</div>

<div class="col-md-8">
    <div class="panel panel-primary">
        <div class="panel-heading">Détails </div>
        <div class="panel-body">
            <div id="appointment-complete-step" style="display: <?php echo !empty($reclamationDetails) ? 'none': 'block'; ?>;">

                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <div class="text-center"><strong>Choisir un bloque d'horaire</strong></div>
                    </div>
                </div>

            </div>

            <div id="appointment-common-details" style="display: <?php echo !empty($reclamationDetails) ? 'block': 'none'; ?>;" class="form-group">
                <select name="client_id" id="client_id" class="form-control">
                    <option value="">-- Choisir un client --</option>
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
                    <label for="reclamation">RÉLAMATION:</label>
                    <input class="form-control" value="<?php echo !empty($reclamationDetails) ? $reclamationDetails['reclamation'] : '';  ?>" name="reclamation" id="reclamation">
                </div>
                <div class="form-group">
                    <label for="insurer">ASSUREUR:</label>
                    <select name="insurer" class="form-control" id="insurer">
                        <option value="Promutuel">Promutuel</option>
<!--                        <option --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer']  == 'Promutuel') ? ' selected="selected" ' : '' ?><!-- value="Promutuel">Promutuel</option>-->
<!--                        <option --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer']  == 'Desjardins') ? ' selected="selected" ' : '' ?><!-- value="Desjardins">Desjardins</option>-->
<!--                        <option --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer']  == 'SSQ') ? ' selected="selected" ' : '' ?><!-- value="SSQ">SSQ</option>-->
<!--                        <option  --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer'] == 'La Capital') ? ' selected="selected" ' : '' ?><!--value="La Capital">La Capital</option>-->
<!--                        <option  --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer'] == 'Intact') ? ' selected="selected" ' : '' ?><!--value="Intact">Intact</option>-->
<!--                        <option  --><?php //echo (!empty($reclamationDetails) && $reclamationDetails['insurer'] == "l\&#39;unique") ? ' selected="selected" ' : '' ?><!--value="l\'unique"> L'unique</option>-->
                    </select>
                </div>
                <hr>
                <div class="col-md-12" id="vinArea" style="border: 1px solid #e4e4e4; padding-top: 10px;">
                    <div id="vinAreaOverlay" style="display: none;"><p><img src="/admin/assets/img/ajax-loader.gif"></p></div>

                    <div class="input-group">
                        <input type="text" placeholder="Type VIN" value="<?php echo !empty($reclamationDetails) ? $reclamationDetails['vin'] : '';  ?>" class="form-control col-md-9" id="vin">
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
                                                'inventory' => '#Inventaire',
                                                'sn' => '#NS',
                                                'color' => 'Couleur',
                                                'pa' => 'P.A',
                                                'bt' => 'B.T',
                                                'millage' => 'Millage',
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
        <div class="panel-heading">Horraire de rendez-vous</div>
        <div class="panel-body">
            <div class="form-group">
                <table class="table table-bordered" id="appointment-table" style="display: none;">
                    <tr>
                        <th>Tech/Estimateur:</th>
                        <td id="user_name"></td>
                    </tr>
                    <tr>
                        <th>Adresse:</th>
                        <td id="address"></td>
                    </tr>
                </table>
                <div id="appointment-time-block" style="display: none;">
                    <label for="appointment-available-time">Choisir : </label>
                    <select class="form-control" name="appointment[available_time]" id="appointment-available-time"></select>
                </div>

                <div id="appointment-details" style="display: none;">
                    <div class="form-group">
                        <label for="note">Note: </label>
                        <textarea name="note" class="form-control" id="note" style="width: 100%;"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="checkbox_not_presented" value="1"> Ne c'est pas présenté <br>
                        <input type="checkbox" name="checkbox_total_loss" value="1"> Perte totale <br>
                        <input type="checkbox" name="checkbox_want_repair_appointment" value="1"> Ne veux pas fixer de RDV pour la réparation
                        <br>
                        <input type="checkbox" name="checkbox_monetary_compensation" value="1"> Compensation monétaire
                        <br>
                        <input type="checkbox" name="checkbox_call_back_for_appointment" value="1"> Rappel pour rendez-vous
                    </div>

                    <hr>

                    <button type="button" id="button-create-appointment" class="btn btn-primary">Créer</button>

                    <br>
                    <div id="message-area" class="text-danger" style="margin-top: 10px;"></div>

                </div>

            </div>
        </div>
    </div>
</div>