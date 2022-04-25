<?php

$clients = getClients();

//echo "<pre>"; print_r($clients);exit;

$techGuys = getUsersByLevel( 4 );

$currentUser = getUserDetailsById($_SESSION['jigowatt']['user_id']);

$invoicePhotos = !empty($_SESSION['invoice']) ? getInvoicePhotos( $_SESSION['invoice'] ) : array();

?>

<!-- Modal -->

<div class="modal fade" id="estimationAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="estimationAppointmentModalTitle" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="exampleModalLongTitle">ESTIMATION APPOINTMENT</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <div class="row">

                <div class="col-md-6">

                    <div class="panel panel-primary">

                        <div class="panel-heading">

                            <a class="btn btn-info text-right" href="/admin/client.php/"><i class="glyphicon glyphicon-plus"></i> Create Client</a>

                        </div>

                        <div class="panel-body">

                            <div class="col-md-12">

                                <div class="form-group">

                                    <label for="clients">Choose a Clients</label>

                                    <select name="client" class="form-control" id="clients">

                                        <option value=""> --Choose-- </option>

                                        <?php foreach ( $clients as $client ): ?>

                                            <option value="<?php echo $client['clientid'] ?>" ><?php echo $client['fname'] . ' ' . $client['fname']; ?></option>

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

                                        <label for="reclamation">RECLAMATION:</label>

                                        <input class="form-control" id="reclamation">

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-12" style="border: 1px solid #e4e4e4; padding-top: 10px;">

                                <input type="hidden" name="clientid" id="clientid" value="">

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label for="client_fname">First Name:</label>

                                        <input readonly="readonly" type="text" class="form-control" id="client_fname">

                                    </div>

                                    <div class="form-group">

                                        <label for="client_lname">Last Name:</label>

                                        <input readonly="readonly" type="text" class="form-control" id="client_lname">

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label for="client_tel1">Telephone:</label>

                                        <input readonly="readonly" type="text" class="form-control" id="client_tel1">

                                    </div>

                                    <div class="form-group">

                                        <label for="client_email">Email:</label>

                                        <input readonly="readonly" type="email" class="form-control" id="client_email">

                                    </div>

                                </div>

                                <div class="col-md-12">

                                    <div class="form-group">

                                        <label for="client_cie">Company:</label>

                                        <input readonly="readonly" type="text" class="form-control" id="client_cie">

                                    </div>

                                </div>

                                <div class="col-md-12">

                                    <div class="form-group">

                                        <label for="insurer">INSURER:</label>

                                        <select name="insurer" class="form-control" id="insurer">

<!--                                            <option value="Desjardins">Desjardins</option>-->
<!--                                    <option value="SSQ">SSQ</option>-->
<!--                                    <option value="La Capital">La Capital</option>-->
<!--                                    <option value="Intact">Intact</option>-->
                                    <option value="Promutuel">Promutuel</option>
<!--                                    <option value="RBC">RBC</option>-->
<!--                                    <option value="Aviva">Aviva</option>-->
<!--                                    <option value="CAA">CAA</option>-->
<!--                                    <option value="TD">TD</option>-->
<!--                                    <option value="Industrielle alliance">Industrielle alliance</option>-->
<!--                                    <option value="Co-operators">Co-operators</option>-->

                                        </select>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-12" id="vinArea" style="border: 1px solid #e4e4e4; padding-top: 10px;">

                                <div id="vinAreaOverlay" style="display: none;"><p><img src="/admin/assets/img/ajax-loader.gif"></p></div>



                                <div class="input-group">

                                    <input type="text" placeholder="Type VIN" class="form-control col-md-9" id="vin">

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

                                                            'brand' => 'Brand',

                                                            'model' => 'Model',

                                                            'year' => 'Year',

                                                            'inventory' => '#Inventory',

                                                            'sn' => '#SN',

                                                            'color' => 'Color',

                                                            'pa' => 'P.A',

                                                            'bt' => 'B.T',

                                                            'millage' => 'Millage',

                                                        ) as $k => $v ): ?>

                                            <div class="col-md-6 <?php echo $k == 'sn' ? 'hidden' : ''; ?>">

                                                <div class="form-group">

                                                    <label for="<?php echo $k; ?>"><?php echo $v; ?>:</label>

                                                    <input type="text" required="required" class="form-control" id="<?php echo $k; ?>" value="">

                                                </div>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-12" style="border: 1px solid #e4e4e4; padding-top: 10px; min-height: 100px; padding-bottom: 15px; margin-bottom: 5px;">

                                <h4>Upload Photos: </h4>

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

                </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                <button type="button" class="btn btn-primary">Save changes</button>

            </div>

        </div>

    </div>

</div>