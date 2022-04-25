<?php

/**
 * Reset and verify a user password.
 *
 * LICENSE:
 *
 * This source file is subject to the licensing terms that
 * is available through the world-wide-web at the following URI:
 * http://codecanyon.net/wiki/support/legal-terms/licensing-terms/.
 *
 * @author       Jigowatt <info@jigowatt.co.uk>
 * @copyright    Copyright © 2009-2017 Jigowatt Ltd.
 * @license      http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 * @link         http://codecanyon.net/item/php-login-user-management/49008
 */

include_once( 'generic.class.php' );

class Forgot extends Generic {

	// Form vars
	private $key;
	private $password;
	private $password2;

	// Misc vars
	private $error;
	private $name;
	private $email;
	private $user;
	private $tmp_auth_token;
	private $sms = FALSE;

	function __construct() {

		include_once( 'integration/twilio/Twilio.php' );

		// Are they clicking from an email?
		if(isset($_GET['key']) && strlen($_GET['key']) == 32) {
			$this->key = parent::secure($_GET['key']);

			// Has the form been submitted?
			if(isset($_POST['reset'])) {
				$this->password = parent::secure($_POST['password']);
				$this->password2 = parent::secure($_POST['password2']);
			}
		  /** Redirect if not clicking from email, and modal form hasn't been submitted. */
		} else if (!isset($_GET['key']) && !isset($_POST['usernamemail'])) { header('Location: home.php'); exit(); }
	}

	private function validate() {

		// Further security check right here
		if(isset($_POST['reset']) && isset($this->key)) {

			// Just some input validation
			if($this->password != $this->password2) {
				$this->error = '<div class="alert alert-danger">'._('Your passwords did not match, try again.').'</div>';
			} else if(strlen($this->password) < 5) {
				$this->error = '<div class="alert alert-danger">'._('Your password must be at least 5 characters.').'</div>';
			}

			// No errors, then lets double check the key
			if(empty($this->error) && isset($this->key)) {

				$params = array( ':key' => $this->key );
				$stmt = parent::query("
						SELECT `login_confirm`.`email`, `login_confirm`.`key`, `login_users`.`email`, `login_users`.`name`, `login_users`.`username`
						FROM   `login_confirm`,         `login_users`
						WHERE  `login_confirm`.`key`  =  :key
						AND    `login_users`.`email`  = `login_confirm`.`email`
						AND    `login_confirm`.`type` = 'forgot_pw';
						", $params);

				$row = $stmt->fetch();

				/** Key is invalid, nice try sucka. */
				if( $stmt->rowCount() < 1 ) {
					$this->error = '<div class="alert alert-danger">'._('Verification failed.').'</div>';
				} else {
					$this->email = $row['email'];
					$this->name  = $row['name'];
					$this->user  = $row['username'];
				}
			}

		}
	}

	private function resetpw() {

		// Further security
		if(empty($this->error) && isset($_POST['reset']) && isset($this->key)) {

			// Delete the recovery key so it can't be reused
			$params = array( ':email' => $this->email );
			parent::query("DELETE FROM `login_confirm` WHERE `email` = :email AND `type` = 'forgot_pw'", $params);

			// Resets their password
			$params = array(
				':password' => parent::hashPassword($this->password),
				':email'    => $this->email
			);
			parent::query("UPDATE `login_users` SET `password` = :password, `tmp_auth_token` = NULL, `sms_time` = NULL WHERE `email` = :email;", $params);

			$shortcodes = array(
				'site_address'	=>	SITE_PATH,
				'full_name'		=>	$this->name,
				'username'		=>	$this->user
			);

			$subj = parent::getOption('email-forgot-success-subj');
			$msg = parent::getOption('email-forgot-success-msg');

			// Send an email confirming their password reset
			if(!parent::sendEmail($this->email, $subj, $msg, $shortcodes))
				$this->error = "ERROR. Mail not sent";

			echo "<div class='alert alert-success'>"._('Successfully reset your password')."</div>";
			echo "<h2>"._('Account Recovery')."</h2>";
			echo "<p>"._('If you need any further assistance please contact the website administrator:')." " . address . "</p>";
			include_once('footer.php');
			exit();

		} else echo $this->error;

	}

	private function reset_form() {

		if(isset($this->key)) { ?>
			<div class="row">
				<div class="col-md-6">
					<form class="" method="post">
						<fieldset>
							<legend><?php _e('Account Recovery'); ?></legend>
							<div class="form-group">
								<label class="control-label" for="password"><?php _e('New password'); ?></label>
								<div class="controls">
									<input type="password" class="form-control input-xlarge" id="password" name="password">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label" for="password2"><?php _e('Confirm password'); ?></label>
								<div class="controls">
									<input type="password" class="form-control input-xlarge" id="password2" name="password2">
								</div>
							</div>
						</fieldset>
						<div class="form-actions">
							<button type="submit" class="btn btn-primary" name="reset"><?php _e('Reset Password'); ?></button>
						</div>
					</form>
				</div>
			</div>
<?php	}
	}

	public function modal_process()
	{

		if (isset($_POST['usernamemail'])) {

			$usernamemail = parent::secure($_POST['usernamemail']);

			if (isset($_POST['sms'])) {
				$this->sms = parent::secure($_POST['sms']);
			}


			// The input field wasn't filled out
			if (empty($usernamemail)) {
				$this->error = '<div class="alert alert-danger">' . _('Please enter your username or email address.') . '</div>';
			} else {

				$params = array(':usernameEmail' => $usernamemail);
				$stmt = parent::query("SELECT * FROM `login_users` WHERE `username` = :usernameEmail OR `email` = :usernameEmail;", $params);

				if ($stmt->rowCount() > 0) {

					$row = $stmt->fetch();

					if ((parent::getOption('is-two-factor-auth-enable') == 1) AND (parent::getOption('two_factor_auth_number') != '') AND (parent::getOption('two_factor_auth_sid') != '') AND (parent::getOption('two_factor_auth_token') != '') AND ($row['use_two_factor_auth'] == 1)) {

						if (($this->sms === FALSE)) {


							$send = $this->send_auth_sms($row['phone']);

							if ($send !== TRUE) {

								echo $send;

							} else {

								$params = array(':user_id' => $row['user_id'], ':tmp_auth_token' => $this->tmp_auth_token, ':sms_time' => time());
								$stmt = parent::query("UPDATE `login_users` SET `tmp_auth_token` = :tmp_auth_token, `sms_time` = :sms_time WHERE user_id = :user_id", $params);

								echo "
						<form action='forgot.php' name='smsforgotform' class='smsforgotform' id='smsforgotform' method='post'>
                            <div class='form-group'>
                                <label for='sms' class='login-label'>SMS-code</label>
                                <input class='form-control' id='sms' name='sms' size='30' placeholder='SMS-code' type='text'/>
                                <input class='form-control' id='usernamemail' name='usernamemail' size='30' value='$usernamemail' type='hidden'/>
                            </div>

                            <input type='submit' value='Check code' class='btn btn-default login-submit'  name='forgotten'/>
                        ";

							}
							exit;
						} elseif ( ($this->sms !== FALSE) AND ($row['tmp_auth_token'] == $this->sms)) {

						} elseif ($row['tmp_auth_token'] != $this->sms) {
							echo '<div class="alert alert-danger">' . _('Invalid SMS-code') . '</div>';
							exit;
						} else {
							echo '<div class="alert alert-danger">' . _('Sorry, some trouble') . '</div>';
							exit;
						}
					}


					// Reuse the email variable.
					$email = $row['email'];

					// Check that a recovery key doesn't already exist, if it does, remove it.
					$params = array(':email' => $email);
					$stmt = parent::query("SELECT * FROM `login_confirm` WHERE `email` = :email AND `type` = 'forgot_pw';", $params);

					if ($stmt->rowCount() > 0)
						parent::query("DELETE FROM `login_confirm` WHERE email = :email AND `type` = 'forgot_pw';", $params);

					// Generate a new recovery key
					$key = md5(uniqid(mt_rand(), true));
					$params = array(
						':email' => $email,
						':key' => $key
					);

					parent::query("INSERT INTO `login_confirm` (`email`, `key`, `type`) VALUES (:email, :key, 'forgot_pw');", $params);

					$shortcodes = array(
						'site_address' => SITE_PATH,
						'full_name' => $row['name'],
						'username' => $row['username'],
						'reset' => SITE_PATH . "forgot.php?key=$key"
					);

					$subj = parent::getOption('email-forgot-subj');
					$msg = parent::getOption('email-forgot-msg');

					// Send an email confirming their password reset
					if (!parent::sendEmail($email, $subj, $msg, $shortcodes))
						$this->error = '<div class="alert alert-danger">' . _('ERROR. Mail not sent') . '</div>';
					else
						$this->error = "<div class='alert alert-success'>" . _('We\'ve emailed you password reset instructions. Check your email.') . "</div>";

				} else {
					$this->error = '<div class="alert alert-danger">' . _('This account does not exist.') . '</div>';
				}
			}

			echo $this->error;

		}

	}

	private function send_auth_sms($user_phone)
	{
		$client = new Services_Twilio(parent::getOption('two_factor_auth_sid'), parent::getOption('two_factor_auth_token'));

		$code = $this->generate_sms_code();
		$content = "You code $code";

		try {
			$item = $client->account->sms_messages->create(
				parent::getOption('two_factor_auth_number'),
				'+'.$user_phone,
				$content
			);
			return TRUE;
		} catch (Exception $e) {
			$error = '<div class="alert alert-danger">'._('Something went wrong').'</div>';
			return $error;
		}

	}

	private function generate_sms_code($length = 5)
	{

		$characters = '0123456789';
		$chars = strlen($characters);
		$sms = '';
		for ($i = 0; $i < $length; $i++) {
			$sms .= $characters[rand(0, $chars - 1)];
		}
		$this->tmp_auth_token = $sms;
		return $sms;
	}

	public function process() {

		// Only allow guests to view this page
		parent::guestOnly();

		// Check for correct and complete values
		$this->validate();

		// If there are no errors, let's reset the password
		$this->resetpw();

		// Show the form if $_GET key is set
		$this->reset_form();

	}

}