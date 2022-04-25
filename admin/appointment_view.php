<?php

include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3]));
//protect("Admin");
$estimationId = null;
$pageClass = 'appointment_view';

$appointmentId = !empty($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

$message = '';
if( !empty($_SESSION['email_sent_msg']) ){
    $message = $_SESSION['email_sent_msg'];
    unset($_SESSION['email_sent_msg']);
}
if(!empty($_POST) && $appointmentId > 0){
    //echo "<pre>"; print_r($_POST); exit;
    if( !isset($_POST['checkbox_call_back_for_appointment']) ){
        $_POST['checkbox_call_back_for_appointment'] = 0;
    }
    saveAppointment($appointmentId, $_POST);
    $appointment_save_msg = 'Appointment has been saved successfully.';
}

$appointment = getAppointment( $appointmentId );
$appointmentPhotos = getAppointmentPhotos( $appointmentId );
if ( !empty($appointment) ) {
    $reclamationId = getReclamationByAppointmentData($appointment[0]);
    $db = DB::getInstance();
    $data = $db->query('SELECT * FROM estimations WHERE reclamation_id = ?', [$reclamationId])->toArray();
    if ( !empty($data[0]) ) {
        $estimationId = $data[0]['id'];
    }
//    $estimationId = getEstimationIdByReclamation($appointment[0]['reclamation']);
}

include_once('header.php');
?>
<div class="container">
    <div class="row">

    <?php if( !empty($message) ): ?>
        <div class="alert alert-success fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

        <?php if ( !empty($appointment) ): $appointment = $appointment[0]; ?>
        <div class="col-md-7">

            <div class="panel panel-primary">
                <div class="panel-heading">Détails</div>
                <div class="panel-body">

                    <table class="table table-responsive">
                        <tr>
                            <th>Date:</th>
                            <td><?php echo date('F j, Y', strtotime($appointment['date'])) ;?></td>
                            <th>Réclamation:</th>
                            <td><span class="label label-warning">#<?php echo $appointment['reclamation'] ;?></span></td>
                        </tr>

                        <tr>
                            <th>Nom du client:</th>
                            <td><?php echo $appointment['client_name'] ;?></td>
                            <th>Courriel:</th>
                            <td><?php echo $appointment['email'] ;?></td>

                        </tr>

                        <tr>
                            <th>Compagnie:</th>
                            <td><?php echo $appointment['cie'] ;?></td>

                            <th>Téléphone:</th>
                            <td><?php echo $appointment['tel1'] ;?></td>
                        </tr>

<!--                        <tr>-->
<!--                            <th>Assureur:</th>-->
<!--                            <td>--><?php //echo $appointment['insurer'] ;?><!--</td>-->
<!--                        </tr>-->

                    </table>

                    <hr>

                    <table class="table table-responsive">
                        <tr>
                            <th>NIV: </th>
                            <td><?php echo $appointment['vin']; ?></td>
                        </tr>

                        <tr>
                            <th>Marque:</th><td><?php echo $appointment['brand']; ?></td>
                            <th>Modèle:</th><td><?php echo $appointment['model']; ?></td>
                        </tr>

                        <tr>
                            <th>Année:</th><td><?php echo $appointment['year']; ?></td>
                            <th>Immatriculation:</th><td><?php echo $appointment['inventory']; ?>
                        </tr>

                        <tr>
                            <th>Couleur:</th><td><?php echo $appointment['color']; ?></td>
                            <th>Millage:</th><td><?php echo $appointment['millage']; ?></td>
<!--                            <th>BT:</th><td>--><?php //echo $appointment['brake_type']; ?><!--</td>-->
                        </tr>

<!--                        <tr>-->
<!--                            <th>PA:</th><td>--><?php //echo $appointment['particular_area']; ?><!--</td>-->

<!--                        </tr>-->

                    </table>

                    <hr>

                    <div>
                        <?php

                            if ( 'repair' == $appointment['type'] ):
                                $invoiceId = getInvoiceByReclamation( $appointment['reclamation'] );
                            ?>
                            <?php if ( $invoiceId ): ?>
                        <a href="/admin/main.php?invoice_id=<?php echo $invoiceId; ?>" class="btn btn-primary btn-sm"> Voir la facture <i class="glyphicon glyphicon-chevron-right"></i></a>
                            <?php else: ?>
                                <a href="javascript:void(0);" id="createInvoiceFromAppointment" data-app-id="<?php echo $appointment['id']; ?>" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-plus"></i> Créer une facture <i class="glyphicon glyphicon-chevron-right"></i></a>
                            <?php endif; ?>
                            <?php if ( $estimationId ): ?>
                                <a href="/admin/estimation.php#!/estimation/<?php echo $estimationId; ?>" class="btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i> Voir estimation <i class="glyphicon glyphicon-chevron-right"></i></a>
                            <?php else: ?>
                                <a href="javascript:void(0);" class="btn btn-success disabled btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i> Voir estimation <i class="glyphicon glyphicon-chevron-right"></i></a>
                            <?php endif; ?>
                        <?php else:

                            $rdvRepairUrl = "/admin/appointment_create.php?date=" . date('Y-m-d', strtotime($appointment['date'])). "&appointment_type=repair&reclamation_id=" . $reclamationId;

                            $repairScheduleId = getRepairScheduleIdByReclamation($appointment['reclamation']);

                            if ( $repairScheduleId ) {
                                $rdvRepairUrl = "/admin/appointment_view.php?appointment_id=" . $repairScheduleId;
                            }

                            if ( $estimationId ) {
                                $estimateUrl = '/admin/estimation.php#!/estimation/' . $estimationId;
                            } else {
                                $estimateUrl = '/admin/rect2est.php?rid=' . $reclamationId . '&aid=' . $appointment['id'];
                            }

                            ?>
                            <?php if( protectThis("Admin") ) : ?>
                            <a href="<?php echo $rdvRepairUrl; ?>" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-plus"></i> RDV Réparation <i class="glyphicon glyphicon-chevron-right"></i></a>
                            <a href="<?php echo $estimateUrl; ?>" class="btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i> Estimation <i class="glyphicon glyphicon-chevron-right"></i></a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div id="appointment-photos">
                        <?php foreach ( $appointmentPhotos as $photo ): ?>
                            <div style="display: inline-block;" id="photo-div-<?php echo $photo['id']; ?>">
                        <span>
                            <a href="/<?php echo $photo['photo_url']; ?>" data-lightbox="image-<?php echo $photo['id']; ?>">
                            <img class="img img-thumbnail" data-lightbox="image-<?php echo $photo['id']; ?>" src="/<?php echo $photo['photo_url']; ?>" width="100px">
                        </a>
                        </span>
                                <br>
                                <?php $a = explode('/', $photo['photo_url']); ?>
                                <span style="color: red; font-size: 12px; text-align: center !important; text-decoration: underline; cursor: pointer;" class="delete-app-photo" data-alt="<?php echo end($a); ?>" data-id="<?php echo $photo['id']; ?>">Effacer</span>
                            </div>
                        <?php endforeach; ?>
                    </div>


                </div>
            </div>

        </div>

        <div class="col-md-5">


            <div class="panel panel-primary">
                <div class="panel-heading">Horaire</div>
                <div class="panel-body">
                    <form action="" method="post">
                        <table class="table table-bordered">

                            <tr class="<?php echo $appointment['type'] != 'repair' ? 'success' : 'danger'; ?>">
                                <th colspan="2">
                                    <?php echo ucfirst($appointment['type']); ?> Rendez-vous
                                </th>
                            </tr>

                            <tr>
                                <th>Estimateurr/Tech:</th>
                                <td><?php echo $appointment['tech_name']; ?></td>
                            </tr>
                            <tr>
                                <th>Adresse:</th>
                                <td><?php echo $appointment['schedule_address']; ?></td>
                            </tr>
                            <tr>
                                <th>Heure:</th>
                                <td><span class="label label-success"><?php echo $appointment['start_time']; ?></span> to <span class="label label-info"><?php echo $appointment['end_time']; ?></span></td>
                            </tr>

                            <?php if( protectThis("Admin") ) : ?>

                            <tr>
                                <th>Note:</th>
                                <td><input type="text" name="notes" value="<?php echo $appointment['notes']; ?>" /> </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="form-group">
                                        <input type="checkbox" data-appointment-id="<?php echo $appointment['id']; ?>" class="ad-value-update" name="checkbox_not_presented" <?php echo $appointment['checkbox_not_presented'] == 1 ? 'checked' : '' ?> value="1"> Ne c'est pas présenté <br>
                                        <input type="checkbox" data-appointment-id="<?php echo $appointment['id']; ?>" class="ad-value-update" name="checkbox_total_loss" <?php echo $appointment['checkbox_total_loss'] == 1 ? 'checked' : '' ?> value="1"> Perte totale <br>
                                        <input type="checkbox" data-appointment-id="<?php echo $appointment['id']; ?>" class="ad-value-update" name="checkbox_want_repair_appointment" <?php echo $appointment['checkbox_want_repair_appointment'] == 1 ? 'checked' : '' ?> value="1"> Ne veux pas fixer de RDV pour la réparation
                                        <br>
                                        <input type="checkbox" data-appointment-id="<?php echo $appointment['id']; ?>" class="ad-value-update" name="checkbox_monetary_compensation" <?php echo $appointment['checkbox_monetary_compensation'] == 1 ? 'checked' : '' ?> value="1"> Compensation monétaire
                                        <br>
                                        <input type="checkbox" data-appointment-id="<?php echo $appointment['id']; ?>" class="ad-value-update" name="checkbox_call_back_for_appointment" <?php echo $appointment['checkbox_call_back_for_appointment'] == 1 ? 'checked' : '' ?> value="1"> Rappel de rendez-vous
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="submit" class="btn btn-success btn-block btn-sm">sauvegarder <i class="glyphicon glyphicon-save"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a class="btn btn-block btn-sm btn-primary" href="/admin/appointment_email_notification.php?appointment_id=<?php echo $appointment['id']; ?>"> Envoyer courriel <i class="glyphicon glyphicon-send"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a class="delete-appointment btn btn-block btn-sm btn-danger" data-appointment-id="<?php echo $appointment['id']; ?>" href="javascript:void(0);">Effacer <i class="glyphicon glyphicon glyphicon-remove"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a class="btn btn-block btn-sm btn-primary" href="/admin/schedule_day_list.php?date=<?php echo $appointment['date']; ?>"> <i class="glyphicon glyphicon-chevron-left"></i> Retour</a>
                                </td>
                            </tr>

                            <?php endif; ?>

                        </table>
                    </form>
                </div>
            </div>

        </div>

        <?php endif; ?>

<?php
include_once('footer.php');
?>