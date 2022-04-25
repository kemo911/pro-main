<?php

/**
 * Establish a mySQL connection and select a database.
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

/**
 * Class Connect
 * @property PDO $dbh
 */
class Connect {

	private $error;
	public static $dbh;

	/**
	 * Checks if installation is complete by seeing if config.php exists.
	 *
	 * The user will be prompted to visit home.php and click "Begin Install" if
	 * there is no config.php yet setup. This prompt will be persistent and won't
	 * allow any pages to load until config.php is created.
	 *
	 * @return    string    The error message if an install does not exist.
	 */
	public function checkInstall() {

		if ( ! file_exists( dirname(dirname(__FILE__)) . '/config.php' )) :

			return "<div class='alert alert-warning'>"._('Installation has not yet been ran!')."</div>
					<h1>"._('Woops!')."</h1>
					<p>"._('You\'re missing a config.php file preventing a database connection from being made.')."</p>
					<p>"._('Please click')." <a href='home.php'>"._('Begin Install')."</a> " ._('on the home page to create a config file.')."</p>
					";

		endif;

	}

	/**
	 * Connect to mySQL and select a database.
	 *
	 * The credentials used to connect to the database are pulled from `config.php` in the root directory.
	 *
	 * @return    string    Error message for any incorrect database connection attempts.
	 */
	public function dbConn() {

		include ( dirname(dirname(__FILE__)) . '/config.php' );

		try {
			self::$dbh = new PDO("mysql:host={$host};dbname={$dbName}", $dbUser, $dbPass);
			self::$dbh->setAttribute( PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_USE_BUFFERED_QUERY );
		} catch (PDOException $e) {
			return '<div class="alert alert-danger">'._('Database error: '). $e->getMessage() . '</div>';
		}


	}

}

// Instantiate the Connect class
$connect = new Connect();
