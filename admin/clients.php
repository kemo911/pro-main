<?php

include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("*");
include_once('header.php');
?>

<div class="container" ng-controller="clientCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h2 class="text-center text-uppercase text-"><strong>Liste des clients</strong></h2>
                    <br>
                    <p>
                         <?php if (protectThis( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3]) )) : ?>
                        <a class="btn btn-danger" href="/admin/client.php">Créer un client</a>
                        <span class="pull-right">
                            <button ng-click="showUploadBox = true;" class="btn btn-small btn-default">Import</button>
                            <a href="export_clients.php" download="Clients_<?php echo date('Ymd_Hi'); ?>.csv" class="btn btn-small btn-primary">Export</a>
                        </span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="panel-body">

                    <div style="border: 1px dashed #dddddd; margin: 20px auto; padding: 30px; position: relative;" ng-show="showUploadBox">
                        <div>
                            <input ng-model="dataFile" ngf-select type="file" accept=".csv">
                            <br>
                            <button ng-click="importClients()" class="btn btn-primary">Upload</button>
                        </div>
                        <button style="position: absolute; top: 0; right: 10px;" ng-click="showUploadBox=false;" class="btn btn-link btn-danger">&times;</button>
                    </div>

<!--                    --><?php //if( protectThis("Admin") ) : ?>
                    <table id="client_lists_dt" class="table table-bordered" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Compagnie</th>
                            <th>Courriel</th>
                            <th>Adresse</th>
                            <th>Tél 1</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Compagnie</th>
                            <th>Courriel</th>
                            <th>Adresse</th>
                            <th>Tél 1</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                    </table>
<!--                        --><?php //else : ?>
<!--    --><?php //endif; ?>

                </div>
            </div>
        </div>
        <div class="col-md-6">

        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>
