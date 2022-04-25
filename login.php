<?php
ini_set('display_errors',1);
ob_start();

if ( ! session_id() ) session_start();
if ( ! isset( $_SESSION['jigowatt'] ) ) {
    $_SESSION['jigowatt'] = array();
}
?>

<?php include_once(__DIR__ . '/classes/login.class.php');

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') AND ($login->error OR $login->msg)) {
    exit;
} ?>

<?php include_once('headerlogin.php'); ?>


<div class="row">
	<div class="main login col-md-4">
		<form method="post" class="form normal-label" action="login.php">
            <?php if ($login->sms_form !== FALSE): ?>
                <?php echo $login->sms_form; ?>
            <?php else: ?>
		<fieldset>

			<img src="logo.png" width="50%" style=" padding-bottom:50px;">
			
            <div class="form-group">
			<input class="form-control" id="username" name="username" placeholder="Utilisateur" type="text"/>
			</div>

			<div class="form-group">
				<input class="form-control" id="password" name="password" size="30" placeholder="Mot de passe" type="password"/>
			</div>
		</fieldset>

		<input type="hidden" name="token" value="<?php echo $_SESSION['jigowatt']['token']; ?>"/>
		<input type="submit" value="Connexion" class="btn btn-danger btn-md btn-block login-submit" id="login-submit" name="login"/>




		<?php if ( !empty($jigowatt_integration->enabledMethods) ) : ?>

		<div class="">
			<?php foreach ($jigowatt_integration->enabledMethods as $key ) : ?>
				<p><a href="login.php?login=<?php echo $key; ?>"><img src="assets/img/<?php echo $key; ?>_signin.png" alt="<?php echo $key; ?>"></a></p>
			<?php endforeach; ?>
		</div>

		<?php endif; ?>
            <?php endif; ?>
		</form>

	</div>

</div>

<?php

include_once('footerlogin.php');
