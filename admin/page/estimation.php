<section id="estimationPage" ng-controller="estimationCtrl">



<div  class="container">

    <div class="row">

        <div class="col-md-12">

            <div ng-show="message" class="alert alert-{{message.type}} fade in">

                <a href="javascript:void(0);" ng-click="message = {}" class="close">&times;</a>

                {{message.text}}

            </div>

        </div>



        <div class="col-md-12" style="padding-bottom: 20px;">

            <img style="max-width: 600px;" src="/admin/assets/img/promutuel-insurance-logo.jpg" alt="Promutuel Insurance">

        </div>


        <span style="
                position: absolute;
                background-color: #323736;
                color: #ffffff;
                padding: 10px;
                top: 50px;
                right: 0;
                font-size: smaller;
                font-weight: bolder;">
            Last updated at: {{ invoice.updated_at }}
        </span>

        <!--    Estimation Form    -->

        <div class="col-md-6">

<!--            <form editable-form action="#" name="estimation_form" role="form" method="post" id="estimation-form">-->

                <div class="panel panel-primary">

                    <div class="panel-body">
                        <?php if (protectThis(Permission::USER_LEVEL_1)) : ?>
                        <a ng-hide="estimation.reclamation" ng-click="showCreateReclamationBlock()" class="btn btn-primary btn-sm btn-block" href="javascript:void(0);">+ Créer une réclamation</a>
                        <?php endif; ?>

                        <a ng-show="estimation.reclamation" ng-click="editReclamation()" class="btn-block" href="javascript:void(0);">Réclamation : {{estimation.reclamation.reclamation}} <span class="pull-right">change <i class="fas fa-arrow-circle-right"></i></span></a>

                        <a ng-show="estimation.estimation_invoice_id == estimation.invoice_id" ng-click="changeInvoiceType(invoice)" class="btn btn-danger btn-sm btn-block" href="javascript:void(0);">+ Créer une facture à partir de l’estimé</a>

                        <a ng-show="estimation.estimation_invoice_id != estimation.invoice_id" ng-click="updateInvoice()" class="btn btn-primary btn-sm btn-block" href="javascript:void(0);">
                          <i class="fa fa-check-square"></i>  Update estimation
                        </a>

                        <hr>



                        <!--  paste information   -->

                        <?php

                            $fields = [

//                                'time_of_loss' => 'Date/heure de perte:',
                                'time_of_loss' => 'Date du sinistre:',

                                'franchise' => 'Franchise',

                                'type_of_loss' => 'Type de perte',

                                'created_by' => 'Estimateur',

                                'address' => 'Adresse',

                                'tel' => 'Tél',

                                'fax' => 'Fax',

                                'client' => 'Client',

                                'vin' => 'VIN',

                                'claim_collector' => 'Expert en sinistre',

                            ];

                        ?>



                        <div class="form-group">

                            <label for="date">Réclamation: <strong class="badge badge-success">{{estimation.reclamation.reclamation}}</strong></label>

                        </div>



                        <?php foreach ( $fields as $column => $value ): ?>

                            <?php if ( $column == 'client' ): ?>



                                <table class="table table-striped" ng-show="estimation.reclamation">

                                    <tr>

                                        <th>Assuré:</th>

                                        <td>{{estimation.reclamation.fname}} {{estimation.reclamation.lname}}</td>

                                    </tr>

                                    <tr>

                                        <th>Adresse:</th>

                                        <td>{{estimation.reclamation.address}}</td>

                                    </tr>

                                    <tr>

                                        <th>Tél:</th>

                                        <td>{{estimation.reclamation.tel1}}</td>

                                    </tr>

                                </table>



                            <?php elseif ( $column == 'vin' ): ?>



                                <table class="table table-striped" ng-show="estimation.reclamation">

                                    <tr>

                                        <th>NIV:</th>

                                        <td><a href="#" editable-text="estimation.reclamation.vin">{{ estimation.reclamation.vin || "empty" }}</a></td>

                                    </tr>

                                    <tr>

                                        <th>Marque:</th>

                                        <td><a href="#" editable-text="estimation.reclamation.brand">{{estimation.reclamation.brand || 'empty'}}</a></td>

                                    </tr>

                                    <tr>

                                        <th>Modèle:</th>

                                        <td><a href="#" editable-text="estimation.reclamation.model">{{estimation.reclamation.model || 'empty'}}</a></td>

                                    </tr>

                                    <tr>

                                        <th>Année:</th>

                                        <td><a href="#" editable-text="estimation.reclamation.year">{{estimation.reclamation.year || 'empty'}}</a></td>

                                    </tr>

                                    <tr>

                                        <th>Immatriculation:</th>

                                        <td><a href="#" editable-text="estimation.reclamation.inventory">{{estimation.reclamation.inventory || 'empty'}}</a></td>

                                    </tr>

                                    <tr>

                                        <th>Couleur:</th>

                                        <td><a href="#" editable-text="estimation.reclamation.color">{{estimation.reclamation.color || 'empty'}}</a></td>

                                    </tr>

                                    <tr>

                                        <th>Odomètre:</th>

                                        <td><a href="#" editable-text="estimation.reclamation.millage">{{estimation.reclamation.millage || 'empty'}}</a></td>

                                    </tr>

                                </table>



                            <?php elseif( 'created_by' == $column ): ?>

                                <div class="form-group">

                                    <label for="<?php echo $column; ?>"><?php echo $value; ?>: <span class="badge badge-success"><?php echo $currentUser['username']; ?></span> </label>

                                </div>

                            <?php else: ?>

                                <div class="form-group">

                                    <label for="<?php echo $column; ?>"><?php echo $value; ?></label>

                                    <input type="text" class="form-control" name="<?php echo $column; ?>" id="<?php echo $column; ?>" ng-model="estimation.<?php echo $column; ?>">

                                </div>

                            <?php endif; ?>

                        <?php endforeach; ?>



                        <div ng-show="estimation.reclamation.photos" class="pictures clearfix">

                            <ul>

                                <li ng-repeat="photo in estimation.reclamation.photos">

                                    <a href="/{{photo.photo_url}}" data-lightbox="image-{{photo.id}}">
                                        <img width="100px" src="/{{photo.photo_url}}">
                                    </a>
                                    <?php if (protectThis(Permission::USER_LEVEL_1)) : ?>
                                    <button type="button" ng-click="deleteReclamationPhoto(photo);" class="btn btn-xs btn-danger">delete</button>
                                    <?php endif; ?>
                                </li>

                            </ul>

                            <hr>

                            <button class="btn btn-sm btn-primary" ngf-multiple="true" ngf-select="uploadReclamationPhoto(picture)" accept="image/*" ng-model="picture"> <i class="fas fa-arrow-circle-up"></i> Choisir un fichier </button>

                        </div>



                    </div>

                </div>



<!--            </form>-->

        </div>

        <!--    Invoice Form -->

        <div class="col-md-6">

            <div class="panel panel-primary">

                <div class="panel-body">

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

                            <a role="button" data-toggle="collapse" data-parent="#accordion" data-target="#collapseOne" href="javascript:void(0);" aria-expanded="true" aria-controls="collapseOne">

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

                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="javascript:void(0);" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

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
                <?php require_once __DIR__ . '/../page/components/BackWindow.vue'; ?>
            </div>





        </div>



        <div class="col-md-12" style="border: 1px solid #e4e4e4; margin-bottom: 10px;">

            <h3>Réparateur</h3>

            <div class="col-md-6">

                <table class="table table-striped">

                    <?php foreach (

                            [

                                  'Réparateur' => 'Eco Solution Grêle',

                                  'Address' => '7 rue du Tournois',

                                  'Ville province C.P.' => 'Blainville, QC J7C 4Y2',

                                  'Courriel' => 'info@eco-solutiongrele.com',

                            ]

                            as $label => $value): ?>

                    <tr>

                        <th><?php echo $label; ?>:</th>

                        <td><?php echo $value; ?></td>

                    </tr>

                    <?php endforeach; ?>

                </table>

            </div>

            <div class="col-md-6">

                <table class="table table-striped">

                    <?php foreach (

                        [

                            'Contact' => 'JOEL VALOIS',

                            'Travail/Jour' => '(450)629-6442',

                            'Dom./Soir' => '(450)629-6484',

                            'Sans Frais' => '1-844-482-2224',

                        ]

                        as $label => $value): ?>

                        <tr>

                            <th><?php echo $label; ?>:</th>

                            <td><?php echo $value; ?></td>

                        </tr>

                    <?php endforeach; ?>

                </table>

            </div>

        </div>



        <!--        invoice-->

        <div class="col-md-12">

            <div class="panel panel-info">

                <div class="panel-heading">

                    <div class="text-center text-uppercase"><strong>Estimation</strong></div>

                </div>

                <div class="panel-body">

                    <form id="invoice" action="" class="form-horizontal" method="post">

                        <div class="form-group">

                            <label class="control-label col-sm-2" for="invoice_location">Contrat de location:</label>

                            <div class="col-sm-4">

                                <input type="text" class="form-control" name="invoice[location]" id="invoice_location" placeholder="Contrat de location">

                            </div>



                            <label class="control-label col-sm-2" for="invoice_location">Jours:</label>

                            <div class="col-sm-2">

                                <input type="number" id="number_of_days" class="form-control" placeholder="Nombre de jours">

                            </div>



                            <div class="col-sm-2">

                                <div class="checkbox">

                                    <label><input name="invoice[rental_car]" id="rental_car" value="1" type="checkbox"> Véhicule de location:</label>

                                </div>

                            </div>

                        </div>



                        <table class="table table-responsive">

                            <tr>

                                <th>#</th>

                                <th>Tech</th>

                                <th>Débosselage sans peinture</th>

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

                                            <input type="text" id="inv_<?php echo $key; ?>_note" class="form-control" name="invoice[damage<?php echo $key; ?>][description]">

                                        </td>

                                        <td>

                                            <input type="number" min="0" id="inv_<?php echo $key; ?>_price" class="form-control invoice_price" name="invoice[damage<?php echo $key; ?>][price]">

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

                                                <input type="text" class="form-control" id="inv_<?php echo $key; ?>_tech" name="invoice[damage<?php echo $key; ?>][tech]">

                                            <?php else: ?>

                                                <select class="form-control <?php echo ( in_array($key, ['other_fees','work_force','parts', 'covid']) ) ? 'hidden' : ''; ?>" id="inv_<?php echo $key; ?>_tech" name="invoice[damage<?php echo $key; ?>][tech]">

                                                    <?php foreach ( $techGuys as $techGuy ): ?>

                                                        <option value="<?php echo $techGuy['user_id']; ?>" <?php echo $currentUser['user_id'] == $techGuy['user_id'] ? 'selected' : '' ?>><?php echo $techGuy['name']; ?></option>

                                                    <?php endforeach; ?>

                                                </select>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <input type="text" class="form-control" id="inv_<?php echo $key; ?>_note" name="invoice[damage<?php echo $key; ?>][description]">

                                        </td>

                                        <td>

                                            <input type="number" min="0" class="form-control invoice_price" id="inv_<?php echo $key; ?>_price" name="invoice[damage<?php echo $key; ?>][price]">

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </table>

                        <div class="col-md-12">

                            <div class="col-md-9 clearfix">

                                <!--div>

                                    <p>Je soussigné(e), assuré(e) ou représentant de l’assuré(e), déclare avoir pris connaissance des réparations de débosselage sans peinture exécuter sur mon véhicule par Eco Solution Grêle, et je m’en déclare entièrement satisfait(e).</p>

                                </div-->



                                <div id="signature">
                                    <img ng-show="invoice.signature_img" src="{{invoice.signature_img}}">
                                </div>



                                <div id="signature-pad" class="clearfix">

                                    <canvas class="pad"></canvas>

                                    <br>

                                    <a href="#" class="btn btn-xs btn-danger" id="signature-clear">Effacer la signature</a>

                                </div>



                                <br>



                                <div id="payment_type">

                                    <label for="p_check"> <input id="p_check"  name="payment_method" type="radio" value="check"> Chèque</label> &nbsp;

                                    <label for="p_interac"> <input id="p_interac" name="payment_method" type="radio" value="interac"> Interac</label> &nbsp;

                                    <label for="p_visa"> <input id="p_interac" name="payment_method" type="radio" value="visa"> Visa</label> &nbsp;

                                    <label for="p_cash"> <input id="p_cash" name="payment_method" type="radio" value="cash"> Contant</label>

                                </div>



                            </div>

                            <div class="col-md-3">

                                <table>

                                    <tr>

                                        <td>Sous Total: </td>

                                        <td><input type="number" id="sub_total" class="form-control invoice_price_calculate" name="invoice[subtotal]"></td>

                                    </tr>

                                    <tr>

                                        <td>TPS (5%): </td>

                                        <td><input type="number" id="tps" class="form-control invoice_price_calculate" name="invoice[tps]"></td>

                                    </tr>

                                    <tr>

                                        <td>TVQ (9.975%): </td>

                                        <td><input type="number" id="tvq" class="form-control invoice_price_calculate" name="invoice[tvq]"></td>

                                    </tr>

                                    <tr>

                                        <td>Franchise: </td>

                                        <td><input type="number" id="invoice_franchise" min="0" step="1" class="form-control invoice_franchise invoice_price_calculate" name="invoice[franchise]"></td>

                                    </tr>

                                    <tr>

                                        <td>Total: </td>

                                        <td><input type="number" id="total" class="form-control invoice_price_calculate" name="invoice[total]"></td>

                                    </tr>

                                    <tr>

                                        <td>Dépôt:  </td>

                                        <td><input type="number" id="deposit" class="form-control invoice_price_calculate" name="invoice[deposit]"></td>

                                    </tr>

                                    <tr>

                                        <td>Balance:  </td>

                                        <td><input type="number" id="balance" class="form-control invoice_price_calculate" name="invoice[balance]"></td>

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

                                    <textarea name="invoice[damages_notes]" id="damages" class="form-control"></textarea>

                                </div>

                            </div>

                        </div>

                    </form>

                </div>

                <div class="panel-footer clearfix">
                    <?php if (protectThis( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3]) )) : ?>
                    <button ng-hide="invoice.id > 0" class="btn btn-primary btn-lg pull-right" ng-click="createInvoice()" type="button">Sauvegarder</button>

                    <button ng-show="invoice.id > 0" class="btn btn-primary btn-lg pull-right" ng-click="updateInvoice()" type="button">Sauvegarder</button>

                    <a ng-show="invoice.id" class="btn btn-warning" target="_blank" href="/admin/invoice/index.php?invoice_id={{invoice.id}}&print=y&title={{invoice.invoice_type == 'estimate' ? 'ESTIMATION' : 'FACTURE'}}">Imprimer</a>
                    <a ng-hide="invoice.id" onclick="alert('Estimation is not ready yet. Please save data first.');" class="btn btn-warning" href="javascript:void(0);">Imprimer</a>
                    |
                    <a ng-show="invoice.id" class="btn btn-success" target="_blank" href="/admin/invoice/index-email.php?invoice_id={{invoice.id}}&email=y&token=creedDefaultToken&title={{invoice.invoice_type == 'estimate' ? 'ESTIMATION' : 'FACTURE'}}">Email</a>
                    <a ng-hide="invoice.id" onclick="alert('Estimation is not ready yet. Please save data first.');" class="btn btn-success" href="javascript:void(0);">Email</a>

                    <?php require __DIR__ . '/../page/components/EmailOptions.vue'; ?>

                    <?php endif; ?>
                    
                </div>

            </div>

        </div>



    </div>

</div>



    <div ng-show="showCreateReclamationForm" id="create-reclamation-division">

        <div class="container">

            <div class="col-md-12">

                <div class="center clearfix">

                    <a ng-click="closeReclamation()" class="hide-division" href="javascript:void(0);">&times;</a>

                    <br>

                    <p class="text-center">

                        <a ng-hide="showNewReclamationBlock" target="_blank" ng-click="createNewReclamation()" href="javascript:void(0);"><i class="fas fa-plus-circle"></i> Create new reclamation</a>

                        <a ng-show="showNewReclamationBlock" ng-click="showNewReclamationBlock = false;" href="javascript:void(0);">

                            <i class="fas fa-arrow-circle-left"></i> Choose existing reclamation

                        </a>

                    </p>

                    <h3 class="text-center text-danger" ng-show="showNewReclamationBlock">

                        Please create the reclamation from the reclamation create page after then this form will automatically fillup.

                    </h3>

                    <br>

                    <div ng-hide="showNewReclamationBlock" class="or-division">OR</div>

                    <br>

                    <!-- Choose an existing reclamation -->

                    <div ng-hide="showNewReclamationBlock" id="reclamation_division" class="form-group">

                        <label for="reclamation_value">Reclamation ( if already exists please type here): <button ng-hide="showReclamationChooseWidget" ng-click="editReclamationSelection()" class="btn btn-xs btn-danger">change</button> </label>

                        <input ng-readonly="!showReclamationChooseWidget" ng-model="reclamation_value" id="reclamation_value" placeholder="type reclamation number" type="text" name="reclamation_value" class="form-control">

                        <ul ng-show="reclamation_value.length && showReclamationChooseWidget" id="reclamation_lists">

                            <li ng-repeat="reclamation in reclamations | filter:reclamation_value">

                                <a ng-click="selectReclamation(reclamation)" href="javascript:void(0);">

                                    {{ reclamation.reclamation }}

                                    ({{ reclamation.fname ? reclamation.fname + reclamation.lname : 'No Client' }})

                                    <span><i class="fas fa-angle-right"></i></span>

                                </a>

                            </li>

                        </ul>

                        <br>

                        <button ng-show="estimation.reclamation && !showReclamationChooseWidget" ng-click="ifReclamationSetProperlyThenContinue()" class="btn btn-danger btn-sm pull-right"> Save & Continue <i class="fas fa-arrow-circle-right"></i></button>

                        <br> <br> <br>

                        <div ng-show="estimation.reclamation && !showReclamationChooseWidget" class="reclamation_details">



                            <table class="table  table-striped">

                                <tr>

                                    <td colspan="2"><strong>Reclamation Number:</strong> {{ estimation.reclamation.reclamation }}</td>

                                </tr>

                                <tr>

                                    <td colspan="2"><strong>Client:</strong> {{ estimation.reclamation.fname }} {{estimation.reclamation.lname}}</td>

                                </tr>

                                <tr>

                                    <th colspan="2">

                                        <table class="table table-bordered">

                                            <tr>

                                                <th>VIN</th>

                                                <th>Brand</th>

                                                <th>Model</th>

                                                <th>Year</th>

                                                <th>Inventory</th>

                                            </tr>

                                            <tr>

                                                <td>{{estimation.reclamation.vin}}</td>

                                                <td>{{estimation.reclamation.brand}}</td>

                                                <td>{{estimation.reclamation.model}}</td>

                                                <td>{{estimation.reclamation.year}}</td>

                                                <td>{{estimation.reclamation.inventory}}</td>

                                            </tr>

                                        </table>

                                    </th>

                                </tr>

                            </table>



                            <div class="pictures clearfix">

                                <ul>

                                    <li ng-repeat="photo in estimation.reclamation.photos">

                                        <img width="100px" src="/{{photo.photo_url}}">

                                        <button ng-click="deleteReclamationPhoto(photo)" class="btn btn-xs btn-danger">delete</button>

                                    </li>

                                </ul>

                                <hr>

                                <button class="btn btn-sm btn-primary" ngf-multiple="true" ngf-select="uploadReclamationPhoto(picture)" accept="image/*" ng-model="picture"> <i class="fas fa-arrow-circle-up"></i> Upload new file </button>

                            </div>



                        </div>

                    </div>



                </div>

            </div>

        </div>

    </div>



    <div ng-show="showCreateInvoiceForm" id="create-invoice-division">

        <a ng-click="showCreateInvoiceForm = false" class="hide-division" href="javascript:void(0);">&times;</a>

        <div class="container">

            <div class="col-md-12">

                <div class="center">



                </div>

            </div>

        </div>

    </div>

    <input type="hidden" id="dots">
    <input type="hidden" id="shared">
    <?php require __DIR__ . '/components/__dots.vue'; ?>
    <?php require __DIR__ . '/components/dots.vue'; ?>

</section>