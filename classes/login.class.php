<?php

/**
 * Verify and execute the login process.
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

include_once( 'integration.class.php' );

class Login extends Jigowatt_integration {

	// Post vars
	private $user;
	private $pass;

	public $use_emails = false;

	// Misc vars
	private $token;
	private $valid;
	private $result;
    public $error;
    public $msg;

	public $is_two_factor_auth_enable = FALSE;
	public $sms_form = FALSE;
    private $is_user_enable_two_factor_auth = FALSE;
    private $post_sms = FALSE;
    private $sms_life_time;
    private $tmp_auth_token = '';
	private $two_factor_auth_sid = '';
	private $two_factor_auth_number = '';
	private $two_factor_auth_token = '';

	function __construct() {

		// Disable users from logging in?
		if (parent::getOption('disable-logins-enable')) {
			parent::displayMessage('<div class="alert alert-danger">' . _('The admin has disabled logins.') . '</div>');
		}

		if ((parent::getOption('is-two-factor-auth-enable') == 1) AND (parent::getOption('two_factor_auth_number') != '') AND (parent::getOption('two_factor_auth_sid') != '')AND (parent::getOption('two_factor_auth_token') != '')) {

			include_once( 'integration/twilio/Twilio.php' );

			$this->is_two_factor_auth_enable = TRUE;

            $this->sms_life_time = parent::getOption('sms_life_time');
			$this->two_factor_auth_number = parent::getOption('two_factor_auth_number');
			$this->two_factor_auth_sid = parent::getOption('two_factor_auth_sid');
			$this->two_factor_auth_token = parent::getOption('two_factor_auth_token');

		}

		$this->use_emails = parent::getOption('email-as-username-enable');
		$this->username_type = ( $this->use_emails ) ? 'email' : 'username';

		// Redirect the logging in user
		if ( parent::getOption('signin-redirect-referrer-enable') )
			$_SESSION['jigowatt']['referer'] = (!empty($_SESSION['jigowatt']['referer'])) ? $_SESSION['jigowatt']['referer'] : 'home.php';
		else
			$_SESSION['jigowatt']['referer'] = parent::getOption('signin-redirect-url');

		// Are they attempting to access a secure page?
		$this->isSecure();

		// Only allow guests to view this page
		parent::guestOnly();

		// Generate a unique token for security purposes
		parent::generateToken();

		// Login form post data
		if(isset($_POST['login']) OR (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {

            $this->user = parent::secure($_POST['username']);
			$this->pass = parent::secure($_POST['password']);
            if (isset($_POST['sms'])) {
                $this->post_sms = parent::secure($_POST['sms']);
            }


			$this->token = !empty($_POST['token']) ? $_POST['token'] : '';
			$this->process();
        }

		if( !empty($_GET['login']) || !empty($_GET['link']) )
			!empty($_GET['link']) ? parent::link_account($_GET['link'], true) : parent::link_account($_GET['login'], true);

		foreach (parent::$socialLogin as $provider) :
			if (!empty($_SESSION['jigowatt'][$provider])) {
				$this->social_login($provider);
				break;
			}
		endforeach;

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') AND !$this->error AND !$this->msg) {
            if ($this->error OR $this->msg) {
                echo $this->error;
                echo $this->msg;
                exit;
            }
        }
		// Display the errors and do not exit the page
		$this->error ? parent::displayMessage($this->error, false) : parent::displayMessage($this->msg, false);

	}

	private function social_login($provider) {

		$params = array( ':session' => $_SESSION['jigowatt'][$provider] );
		$stmt = parent::query("SELECT `user_id` FROM `login_integration` WHERE `$provider` = :session;", $params);

		if ($stmt->rowCount() > 0) {

			$result = $stmt->fetch();

			$params = array( ':user_id' => $result['user_id'] );
			$stmt = parent::query("SELECT * FROM `login_users` WHERE `user_id` = :user_id;", $params);

			$this->result = $stmt->fetch();

			$username = $this->username_type;
			$this->user = $this->result[$username];

			$this->login();

		} else {

			unset(
				$_SESSION['jigowatt']['ot'],
				$_SESSION['jigowatt']['ots'],
				$_SESSION['jigowatt'][$provider]
			);

			header('Location: sign_up.php?new_social');
			exit();

		}

	}

	private function isSecure() {

		if(isset($_GET['e'])) :
			if (parent::getOption('block-msg-out-enable'))
				$this->msg = '<div class="alert alert-danger">'.parent::getOption('block-msg-out').'</div>';
		endif;
	}

	private function process() {
		// Check that the token is valid, prevents exploits
		if(!parent::valid_token($this->token)) {
			$this->error = '<div class="alert alert-danger">'._('Invalid login attempt').'</div>';
			return false;
		}

		// Confirm all details are correct

		if ($this->validate() == false) {
			return false;
		}

        if ($this->is_two_factor_auth_enable === TRUE) {
            if ($this->sms_process() === FALSE) {
                //translation
				$sms_code = _('SMS-code');
				$check_sms_code = _('Check code');
                $this->sms_form = "
                    <div class='form-group'>
                        <label for='sms' class='login-label'>$sms_code</label>
                        <input class='form-control' id='sms' name='sms' size='30' placeholder='SMS-code' type='text'/>
                    </div>
                    <input value='$this->user' id='username' name='username' type='hidden'/>
				    <input value='$this->pass' id='password' name='password' size='30' type='hidden'/>
				    <input value='$this->token' id='token' name='token' size='30' type='hidden'/>
                    <input type='submit' value='$check_sms_code' class='btn btn-default login-submit' id='login-submit' name='login'/>
            ";
                return false;
            }

        }

		// Log the user in
		$this->login();
	}

    private function sms_process()
    {

        $params = array( ':username' => $this->user, ':email' => $this->user );
        $sql    = "SELECT * FROM login_users WHERE email = :email OR username = :username;";
        $stmt   = $this->query($sql, $params);
        $this->result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->is_user_enable_two_factor_auth = $this->result['use_two_factor_auth'];

        if ($this->is_user_enable_two_factor_auth == 0) {
            return TRUE;
        }

        //Is SMS already send?
        if ($this->result['tmp_auth_token'] != '') {

            //
            if ($this->post_sms !== FALSE) {
                if ($this->result['tmp_auth_token'] == $this->post_sms) {

                    $params = array( ':username' => $this->user, ':email' => $this->user);
                    $sql = "UPDATE login_users SET tmp_auth_token = NULL WHERE email = :email OR username = :username";
                    $stmt   = $this->query($sql, $params);
                    return TRUE;
                } else {
                    $this->error = '<div class="alert alert-danger">'._('Invalid SMS-code').'</div>';
                    return FALSE;
                }
            }
            return FALSE;
        }

        //If not send - do it
        if ($this->result['phone'] AND ($this->result['sms_time'] < (time() - parent::getOption('sms_life_time')))) {
            if ($this->send_auth_sms($this->result['phone']) === TRUE) {
                $params = array( ':username' => $this->user, ':email' => $this->user , ':tmp_auth_token' => $this->tmp_auth_token, ':sms_time' => time());
                $sql = "UPDATE `login_users` SET `tmp_auth_token` = :tmp_auth_token, `sms_time` = :sms_time WHERE email = :email OR username = :username";
                $stmt   = $this->query($sql, $params);
            }
            return FALSE;
        }

        return TRUE;
    }

    private function send_auth_sms($user_phone)
    {
        $client = new Services_Twilio($this->two_factor_auth_sid, $this->two_factor_auth_token);

        $code = $this->generate_sms_code();
        $content = "Your code = $code";

        try {
            $item = $client->account->sms_messages->create(
                $this->two_factor_auth_number,
                '+'.$user_phone,
                $content
            );
            return TRUE;
        } catch (Exception $e) {
            $this->error = '<div class="alert alert-danger">'._('Something went wrong').'</div>';
            return FALSE;
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

	private function validate()
	{

		if (!empty($this->error)) {
			return false;
		}

		if (empty($this->user)) {
			$this->error = '<div class="alert alert-danger">' . (($this->use_emails) ? _('You must enter an email address.') : _('You must enter a username.')) . '</div>';
			return false;
		}


		if (empty($this->pass)) {
			$this->error = '<div class="alert alert-danger">' . _('You forgot your password, silly.') . '</div>';
			return false;
		}

		$username = $this->username_type;
		$params = array('username' => $this->user);
		$stmt = parent::query("SELECT * FROM login_users WHERE {$username} = :username", $params);

		$this->result = $stmt->fetch();

		if (!parent::validatePassword($this->pass, $this->result['password'])) {

			$username = $this->username_type;
			$this->error = "<div class=\"alert alert-danger\">" . sprintf(_('Incorrect %s or password.'), $username) . "</div>";
			return FALSE;
		}
		return true;
	}

	// Once everything's filled out
	public function login() {

		// Just double check there are no errors first
		if( !empty($this->error) ) return false;

		// Session expiration
		$minutes = parent::getOption('default_session');
		ini_set('session.cookie_lifetime', 60 * $minutes);

		session_regenerate_id();

		/* See if the admin requires new users to activate */
		if ( parent::getOption('user-activation-enable') ) :

			/** Check if user still requires activation. */
			$params = array( ':user' => $this->user );
			$username = $this->username_type;
			$stmt = parent::query("SELECT * FROM `login_confirm` WHERE `{$username}` = :user AND `type` = 'new_user'", $params);

			$count = $stmt->rowCount();

			if ($count > 0) $_SESSION['jigowatt']['activate'] = 1;

		endif;

		// Save if user is restricted
		if ( !empty($this->result['restricted']) ) $_SESSION['jigowatt']['restricted'] = 1;

		// Is the admin forcing a password update if encryption is not the desired method?
		if (parent::getOption('pw-encrypt-force-enable')) :

			$type = $this->getOption('pw-encryption');

			if (strlen($this->result['password']) == 32 && $type == 'SHA256')
				$_SESSION['jigowatt']['forcePwUpdate'] = 1;

			if (strlen($this->result['password']) != 32 && $type == 'MD5')
				$_SESSION['jigowatt']['forcePwUpdate'] = 1;

			if (strlen($this->result['password']) != 60 && $type == 'BCRYPT')
				$_SESSION['jigowatt']['forcePwUpdate'] = 1;

		endif;

		// Save user's current level
		$user_level = unserialize($this->result['user_level']);
		$_SESSION['jigowatt']['user_level'] = $user_level;

		$_SESSION['read_only_mode'] = false;

        $_SESSION['is_admin'] = false;
        $_SESSION['permission'] = [];
        if (in_array(BsgUser::ADMIN, $user_level)) {
            $_SESSION['is_admin'] = true;
            $_SESSION['permission'] = ['*'];
        }

        if (in_array(BsgUser::ESTIMATOR, $user_level) || in_array(BsgUser::TECH, $user_level)) {
            $_SESSION['permission'] = [
                'save_client',
                'save_estimate',
                'print_estimate'
            ];
        }

		$_SESSION['jigowatt']['email'] = $this->result['email'];

		$_SESSION['jigowatt']['gravatar'] = parent::get_gravatar($this->result['email'], true, 26);

		/** Check whether the user's level is disabled. */
		$params = array( ':level' => $user_level[0] );
		$stmt = parent::query("SELECT `level_disabled`, `redirect` FROM `login_levels` WHERE `id` = :level;", $params);

		$disRow = $stmt->fetch();

		if ( !empty($disRow['level_disabled']) ) $_SESSION['jigowatt']['level_disabled'] = 1;
		if ( !empty($disRow['redirect']) ) $redirect = $disRow['redirect'];

		// Stay signed via checkbox?
		if(isset($_POST['remember'])) {
			ini_set('session.cookie_lifetime', 60*60*24*100); // Set to expire in 3 months & 10 days
			session_regenerate_id();
		}

		/** Store a timestamp. */
		if( parent::getOption('profile-timestamps-enable') ) {

			$params = array(
				':user_id'    => $this->result['user_id'],
				':ip'         => $this->getIPAddress()
			);
			$stmt = parent::query("INSERT INTO `login_timestamps` (`user_id` ,`ip` ,`timestamp`) VALUES (:user_id, :ip, CURRENT_TIMESTAMP);", $params);

		}

		// And our magic happens here ! Let's sign them in
		$username = $this->username_type;
		$_SESSION['jigowatt']['username'] = $this->result[$username];

		// User ID of the logging in user
		$_SESSION['jigowatt']['user_id'] = $this->result['user_id'];

		if ( empty($redirect) ) $redirect = $_SESSION['jigowatt']['referer'];

		unset(
			$_SESSION['jigowatt']['referer'],
			$_SESSION['jigowatt']['token'],
			$_SESSION['jigowatt']['facebookMisc'],
			$_SESSION['jigowatt']['twitterMisc'],
			$_SESSION['jigowatt']['openIDMisc']
		);

		// Redirect after it's all said and done
		header("Location: " . $redirect);
		exit();

	}

}

$login = new Login();
