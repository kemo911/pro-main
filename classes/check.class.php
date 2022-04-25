<?php

/**
 * Runs several checks against the user before allowing access to a page.
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

class Check extends Generic {

	function __construct($forceLogin = true) {

		$this->isGuest($forceLogin);
		$this->isActivated();
		$this->isRestricted();
		$this->forcePwUpdate();

	}

    /**
     * Checks whether or not the user has logged in.
     *
     * If the user is not logged in, we will store the page the user
     * is coming from and redirect the user later after logging in.
     *
     * @param $forceLogin
     * @return bool
     * @internal param bool $redirect Ask the user to sign in or not.
     */
	private function isGuest($forceLogin) {

		if ( !$forceLogin )
			return empty( $_SESSION['jigowatt']['user_id'] );

		if ( empty($_SESSION['jigowatt']['user_id']) ) :

			// IIS compatibility
			// http://davidwalsh.name/iis-php-server-request_uri
			if (!isset($_SERVER['REQUEST_URI'])) {
			       $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
			       if (isset($_SERVER['QUERY_STRING'])) { $_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING']; }
			}

			$_SESSION['jigowatt']['referer'] = $_SERVER['REQUEST_URI'];

			$page = parent::getOption('guest-redirect');
			header('Location: ' . $page);
			exit();

		endif;

	}

	/**
	 * Verifies if the user's account is activated.
	 *
	 * If the account is not activated, we redirect them to the
	 * activate.php page where further instruction is given.
	 */
	private function isActivated() {

		if ( !empty($_SESSION['jigowatt']['activate']) ) :
			header('Location: '. SITE_PATH . 'activate.php');
			exit();
		endif;

	}

	/**
	 * Checks if the user's account is restricted.
	 *
	 * The user is redirected to disabled.php if the account is restricted.
	 */
	private function isRestricted() {

		if ( !empty($_SESSION['jigowatt']['restricted']) || !empty($_SESSION['jigowatt']['level_disabled']) ) :
			header('Location: '. SITE_PATH . 'disabled.php');
			exit();
		endif;

	}

	/**
	 * Forces user to update password if using a non-preferred password hash.
	 *
	 * The admin can enable forcing password updates through the admin panel.
	 * A user will only be requested to update his password if the stored password
	 * for that user does not match the password hash method the admin sets.
	 */
	private function forcePwUpdate() {

		if ( !empty($_SESSION['jigowatt']['forcePwUpdate']) && basename($_SERVER['PHP_SELF']) != 'profile.php') :
			header('Location: '. SITE_PATH . 'profile.php?pe=1');
			exit();
		endif;

	}

	/**
	 * Checks if the user can access a requested page.
	 *
	 * @param    string    $level    The level allowed to view a page, eg "1,2,3".
	 */
	public function protectPage($level) {

		/**
		* Because $level is one string, we must explode it into multiple parts,
		* that is, an array, in order to verify against the user_level array.
		*/
        $valids  = array();
        if (isset($_SESSION['jigowatt']['user_level'])) {
            $user_level = $_SESSION['jigowatt']['user_level'];

        } else {
            $user_level = array();
        }

        if (isset($_GET['ns-debug']) && $_GET['ns-debug'] == 1) {
            echo '<script>window.console.debug("Testing With: ")</script>';
            echo '<script>window.console.debug(' . json_encode($user_level) . ')</script>';
        }

		$levels = array_map( 'trim', explode(",", trim($level)) );
		$comma_separated = "'".implode("','", $levels)."'";
		$sql   = "SELECT level_name, id FROM login_levels WHERE level_name IN (" . $comma_separated . ") OR id IN (" . $comma_separated . ")";
		$stmt = parent::query($sql);
		while($row = $stmt->fetch()) {
			$valids[] = $row['id'];
		}

        if (isset($_GET['ns-debug']) && $_GET['ns-debug'] == 1) {
            echo '<script>window.console.debug("Got Valid: ")</script>';
            echo '<script>window.console.debug(' . json_encode($valids) . ')</script>';
        }

		if( ! array_intersect($valids, $user_level) && $levels != array('*') ) {
			$this->deny_access();
		}

	}

    /**
     * Checks if the user can access a requested enclosed content.
     *
     * @param    string $level The level allowed to view a page, eg "1,2,3".
     * @return bool
     */
	public function protectThis($level) {

		/**
		* Because $level is one string, we must explode it into multiple parts
		* (an array) in order to verify against the user_level session array.
		*/
        $valids = array();
        if (isset($_SESSION['jigowatt']['user_level'])) {
            $user_level = $_SESSION['jigowatt']['user_level'];

        } else {
            $user_level = array();
        }
		$levels = array_map( 'trim', explode(",", trim($level)) );
		$comma_separated = "'".implode("','", $levels)."'";
		$sql   = "SELECT level_name, id FROM login_levels WHERE id IN (" . $comma_separated . ")  OR level_name IN (" . $comma_separated . ")";
		$stmt = parent::query($sql);
		while($row = $stmt->fetch()) {
			$valids[] = $row['id'];
		}

		if ( empty( $_SESSION['jigowatt']['user_id'] ) && $levels == array('*') )
			return false;

		if ( ! array_intersect($valids, $user_level) && $levels != array('*') )
			return false;

		return true;

	}

	/**
	 * Message shown to users when access is denied.
	 */
	public function deny_access() {

		if ( !parent::getOption('block-msg-enable') )
			parent::displayMessage(' ');

		$error = "<div class='row'>
					<div class='col-md-12'>
						" . html_entity_decode(parent::getOption('block-msg')) . "
					</div>
				  </div>";

		parent::displayMessage($error);

	}

}

/* See public function protectPage() */
function protect($level) {

    if (isset($_GET['ns-debug']) && $_GET['ns-debug'] == 1) {
        echo '<script>window.console.debug("Allowed User Level: '.$level.'")</script>';
    }

	$check = new Check();
	$check->protectPage($level);

}

/* See public function protectThis() */
function protectThis($level) {

	$check = new Check(false);
	return $check->protectThis($level);

}
