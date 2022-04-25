<?php

/**
 * User profile edit page.
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

include_once(dirname(__FILE__) . '/check.class.php');
include_once(dirname(__FILE__) . '/integration.class.php');
$check = new Check(false);

class Profile extends Generic {

    private $settings = array();
    private $post;
	private $error;

    public $is_two_factor_auth_enable;

	public $guest;

	function __construct() {

        $this->post = $_POST;

        $this->is_two_factor_auth_enable = parent::getOption('is-two-factor-auth-enable');

        if ( isset( $this->post['phone'] ) && $this->getField( 'use_two_factor_auth' ) == 1 ) {
            $this->validatePhoneNumber();
        }

		/* Prevent guests if the admin hasn't enabled public profiles. */
		if ( !parent::getOption('profile-public-enable') )
			protect('*');

		/* If the admin requires users to update their password. */
		if ( ! empty( $_SESSION['jigowatt']['forcePwUpdate'] ) ) {
			$msg = "<div class='alert alert-warning'>" . _('<strong>Alert</strong>: The administrator has requested all users to update their passwords.') . "</div>";
        }

		// Save the username
		$this->username = !empty($_SESSION['jigowatt']['username']) ? $_SESSION['jigowatt']['username'] : _('Guest');

		$this->use_emails = parent::getOption('email-as-username-enable');
		$this->username_type = ( $this->use_emails ) ? 'email' : 'username';

		/* Check if the user is a guest to this profile. */
		$this->determineGuest();

		// Upload avatar
		if (!empty($_FILES['uploadAvatar'])) {
			$k = getimagesize($_FILES['uploadAvatar']['tmp_name']);
			if (empty($k)) {
				$this->error = sprintf('<div class="alert alert-warning">%s</div>', _('Sorry, that file is not accepted.') );
			} else {
				$uploaddir  = dirname(dirname(__FILE__)) . '/assets/uploads/avatar/';
				$uploadfile = $uploaddir . md5($_SESSION['jigowatt']['user_id'] . $_SESSION['jigowatt']['email']) . '.' . pathinfo($_FILES['uploadAvatar']['name'], PATHINFO_EXTENSION);
				if (move_uploaded_file($_FILES['uploadAvatar']['tmp_name'], $uploadfile)) {
					$this->error = sprintf('<div class="alert alert-success">%s</div>', _('Avatar change success!') );
					$_SESSION['jigowatt']['gravatar'] = parent::get_gravatar($_SESSION['jigowatt']['email'], true, 26);
				} else {
					$this->error = sprintf('<div class="alert alert-warning">%s</div>', _('Sorry, that file is not accepted.') );
				}
			}
		}

		if (!$this->guest && !empty($_POST)) :
			$this->retrieveFields();

			foreach ($_POST as $field => $value)
				$this->settings[$field] = parent::secure($value);

			// Validate fields
			$this->validate();

			// Process form
			if(empty($this->error)){
                $this->process();
                $this->updateSecurityFields();

            }

		endif;

		$this->retrieveFields();

		if(!$this->guest && isset($_GET['key']) && strlen($_GET['key']) == 32) {
			$this->key = parent::secure($_GET['key']);
			$this->updateEmailorPw();
			$this->retrieveFields();
		}

		if ( !empty ( $this->error ) || !empty ( $msg ) )
			parent::displayMessage( !empty($this->error) ? $this->error : (!empty($msg) ? $msg : ''), false);

	}

    private function validatePhoneNumber()
    {
        $this->post['phone'] = preg_replace('~[^0-9]+~','',$this->post['phone']);
        if (strlen($this->post['phone']) < 11) {
            $this->error = sprintf('<div class="alert alert-warning">%s</div>', _('Sorry, phone number is invalid.') );
        }
    }

    private function updateSecurityFields()
    {

        if (isset($this->post['use_two_factor_auth']) AND (parent::secure($this->post['use_two_factor_auth']) == 'on')) {
            $use = 1;
        } else {
            $use = 0;
        }
        $params = array (
            ':use_two_factor_auth' =>$use,
            ':phone' => parent::secure($this->post['phone']),
            ':user_id' =>$this->settings['user_id']
        );

        $sql = "UPDATE `login_users` SET `phone` = :phone, `use_two_factor_auth` = :use_two_factor_auth WHERE user_id = :user_id";
        $stmt   = parent::query($sql, $params);

    }

	private function determineGuest() {

		if ( !empty($_SESSION['jigowatt']['user_id']) && empty($_GET['uid']) )
			$this->user_id = $_SESSION['jigowatt']['user_id'];

		else if ( !empty($_GET['uid']) )
			$this->user_id = (int) $_GET['uid'];

		else
			$this->user_id = _('Guest');

		$this->guest = !( !empty($_SESSION['jigowatt']['user_id']) && $_SESSION['jigowatt']['user_id'] == $this->user_id );

	}

	// Retrieve name, email, user_id
	private function retrieveFields() {

		$params = array( ':user_id' => $this->user_id );
		$stmt   = parent::query("SELECT * FROM `login_users` WHERE `user_id` = :user_id;", $params);

		if ( $stmt->rowCount() < 1 ) {
			$this->error = sprintf('<div class="alert alert-warning">%s</div>', _('Sorry, that user does not exist.') );
			parent::displayMessage($this->error, true);
			return false;
		}

		foreach ($stmt->fetch(PDO::FETCH_ASSOC) as $field => $value) :
			$this->settings[$field] = parent::secure($value);
		endforeach;

	}

	// Return a form field
	public function getField($field) {

		if (!empty($this->settings[$field]))
			return $this->settings[$field];

	}

	// Validate form inputs
	private function validate() {

		//If demo, check that user being edited isn't any demo user accounts
		if($this->is_demo()){
			if($this->settings['username'] == 'admin' || $this->settings['username'] == 'special' || $this->settings['username'] == 'user'){
				$this->error = '<div class="alert alert-danger">You cannot edit this user because it is a demo user. Please create a new user to test out this functionality.</div>';
				return false;
			}
		}

		if(empty($this->settings['CurrentPass'])) {
			$this->error = '<div class="alert alert-danger">'._('You must enter the current password to make changes.').'</div>';
			return false;
		}

		$params = array( ':username' => $this->username );
		$sql = "SELECT `password` FROM `login_users` WHERE $this->username_type = :username;";
		$stmt = parent::query( $sql, $params );
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ( !parent::validatePassword($this->settings['CurrentPass'], $row['password']) ) {
			$this->error = '<div class="alert alert-danger">'._('You entered the wrong current password.').'</div>';
			return false;
		}

		if (empty($this->settings['name']))
				$this->error .= '<div class="alert alert-danger">'._('You must enter a name.').'</div>';

		if (!parent::isEmail($this->settings['email']))
				$this->error .= '<div class="alert alert-danger">'._('You have entered an invalid e-mail address, try again.').'</div>';

		if (!empty($this->settings['password'])) {

			if ($this->settings['password'] != $this->settings['confirm'])
				$this->error .= '<div class="alert alert-danger">'._('Your passwords did not match.').'</div>';

			if (strlen($this->settings['password']) < 5)
				$this->error = '<div class="alert alert-danger">'._('Your password must be at least 5 characters.').'</div>';

		}

		// Checkbox handling
		$sql = "SELECT * FROM `login_profile_fields`;";
		$stmt = parent::query($sql);

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) :
			$name = 'p-' . $row['id'];
			if($row['type'] == 'checkbox')
				$this->settings[$name] = !empty($this->settings[$name]) ? 1 :0;
		endwhile;

	}

	/** @todo: This is extremely ugly, needs refractored. */
	private function updateEmailorPw() {

		$params = array( ':key' => $this->key );
		$sql = "SELECT * FROM `login_confirm` WHERE `key` = :key AND `type` = 'update_emailPw';";
		$stmt = parent::query($sql, $params);

		if ($stmt->rowCount() < 1) {
			$this->error = "<div class='alert alert-danger'>Incorrect confirmation link</div>";
			return false;
		}

		$row = $stmt->fetch();

		if ( !empty($row['data']) ) :
			$params = array(
				':password' => $row['data'],
				':email'    => $row['email'],
				':username' => $this->username
			);
			$sql = "UPDATE `login_users` SET `password` = :password, `email` = :email WHERE $this->username_type = :username;";
		else :
			$params = array(
				':email'    => $row['email'],
				':username' => $this->username
			);
			$sql = "UPDATE `login_users` SET `email` = :email WHERE $this->username_type = :username;";
		endif;

		parent::query($sql, $params);

		$params = array( ':key' => $this->key );
		parent::query("DELETE FROM `login_confirm` WHERE `key` = :key AND `type` = 'update_emailPw'", $params);

		if(!empty($_SESSION['jigowatt']['forcePwUpdate'])) unset($_SESSION['jigowatt']['forcePwUpdate']);

		$this->error = "<div class='alert alert-success'>Account details successfully changed.</div>";

		$shortcodes = array (
			'site_address'  =>  SITE_PATH,
			'full_name'     =>  $this->settings['name'],
			'username'      =>  $this->username
		);

		$subj = parent::getOption('email-acct-update-success-subj');
		$msg  = parent::getOption('email-acct-update-success-msg');

		// Send an email with key
		if ( !parent::sendEmail($row['email'], $subj, $msg, $shortcodes) )
			$this->error = '<div class="alert alert-danger">'._('ERROR. Mail not sent').'</div>';

	}

	private function process() {

		$params = array (
			':name'     => $this->settings['name'],
			':username' => $this->username
		);
		parent::query("UPDATE `login_users` SET `name` = :name WHERE $this->username_type = :username", $params);

		$this->error = "<div class='alert alert-success'>"._('User information updated for')." <b>".$this->settings['name']."</b> ($this->username).</div>";

		$params = array( ':username' => $this->username );
		$stmt = parent::query("SELECT `email` FROM `login_users` WHERE $this->username_type = :username;", $params);
		$email = $stmt->fetch();
		$email = $email[0];

		if ( !empty($this->settings['password']) || $this->settings['email'] != $email ) :

			$key = md5(uniqid(mt_rand(),true));
			$params = array(
				':username' => $this->username,
				':key'      => $key,
				':email'    => $this->settings['email'],
				':type'     => 'update_emailPw',
				':data'     => empty($this->settings['password']) ? '' : parent::hashPassword($this->settings['password'])
			);
			$sql = "INSERT INTO `login_confirm` (`username`, `key`, `email`, `type`, `data`)
					VALUES (:username, :key, :email, :type, :data);";
			parent::query($sql, $params);

			$shortcodes = array(
				'site_address'  =>  SITE_PATH,
				'full_name'     =>  $this->settings['name'],
				'username'      =>  $this->username,
				'confirm'       =>  SITE_PATH . "profile.php?key=$key"
			);

			$subj = parent::getOption('email-acct-update-subj');
			$msg  = parent::getOption('email-acct-update-msg');

			// Send an email with key
			if(!parent::sendEmail($email, $subj, $msg, $shortcodes))
				$this->error = '<div class="alert alert-danger">'._('ERROR. Mail not sent').'</div>';
			else
				$this->error = "<div class='alert alert-warning'>" . _('Check your email to confirm this change.') . '</div>';

		endif;

		// Update profile fields
		foreach($this->settings as $field => $value) :
			if(strstr($field,'p-')) {
				$field = str_replace('p-', '', $field);
				parent::updateOption($field, $value, true, $this->settings['user_id']);
			}
		endforeach;

	}

}

$profile = new Profile();
