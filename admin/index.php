<?php

ob_start();

include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');

protect("*");



if ( !isset($_POST['add_user']) && !isset($_POST['add_level']) && !isset($_POST['searchUsers']) )

	include_once('header.php');



?>

	<div class="row">

        <div class="col-md-12">
            <center>
                <img src="logo.png" width="50%">
                <br/><br/>
                <a href="client.php" class="btn btn-danger">Créer une client</a>
                <?php if ( protectThis( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3])) ): ?>
                    <a href="reclamation.php" class="btn btn-danger">Réclamation</a>
                <?php endif; ?>
                <a href="estimation.php" class="btn btn-danger">Créer une estimation</a>
    <?php if ( protectThis(Permission::USER_LEVEL_1) ): ?>
                <a href="main.php" class="btn btn-danger">Créer une facture</a>
    <?php endif; ?>
            </center>
        </div>

	</div>



<?php



include_once('footer.php');

