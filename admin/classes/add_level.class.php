<?php

/**
 * Add a new level to the database.
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

include_once(dirname(dirname(dirname(__FILE__))) . '/classes/generic.class.php');

class Add_level extends Generic {

	private $error;
	private $level;

	function __construct() {

		// jQuery form validation
		parent::checkExists();

		if(isset($_POST['searchLevels'])) {
			$this->searchLevels();
			exit();
		}

		if(isset($_POST['add_level'])) {
			$this->level = parent::secure($_POST['level']);

			// Confirm all details are correct
			$this->verify();

			// Create the level
			$this->addlevel();

		}

	}

	/** @todo: Should be in a different class, not add_user. */
	private function searchLevels() {

		if(empty($_POST['searchLevels'])) return false;

		$params = array(
			':searchQ'  => $_POST['searchLevels'] . '%',
			':searchQ2' => '%' . $_POST['searchLevels'] . '%'
		);
		$sql = "SELECT level_name as suggest, id
				FROM login_levels
				WHERE level_name LIKE :searchQ
				OR redirect LIKE :searchQ2
				ORDER BY level_name
				LIMIT 0, 5";

		$stmt = parent::query($sql, $params);

		if ( $stmt->rowCount() < 1 ) {
			echo '<h5>' . _('No suggestions') . '</h5>
				  <p class="help-block">' . _('Try searching by name, level, or redirect url.') . '</p>';
			return false;
		}

		echo '<h5>' . _('Suggestions') . '</h5>';

		while($suggest = $stmt->fetch(PDO::FETCH_ASSOC))
			echo "<p><a href='levels.php?lid=" . $suggest['id'] . "'>" . $suggest['suggest'] . "</a></p>\n";

	}

	// Return a value if it exists
	public function getPost($var) {

		if(!empty($this->$var)) {
			return $this->$var;
		} else return false;

	}

	private function verify() {

		if(empty($this->level)) {
			$this->error = '<div class="alert alert-danger">'._('You must enter a level name.').'</div>';
			return false;
		}

	}

	private function addlevel() {

		if(isset($_POST['add_level']) && empty($this->error)) {

			$params = array( ':name' => $this->level );
			$stmt   = parent::query("SELECT * FROM `login_levels` WHERE `level_name` = :name", $params);

			if($stmt->rowCount() > 0) {
				$this->error = '<div class="alert alert-danger">'._('Level name').' <b>'.$this->level.'</b> '._('already exists').'</b>.</div>';
				return false;
			}

			$params = array(
				':level_name' => $this->level,
			);

			parent::query("INSERT INTO `login_levels` (`level_name`, `level_disabled`)
						   VALUES (:level_name, '0')", $params);

			$this->error = 	"<div class='alert alert-success'>" . sprintf(_('Successfully added level <b>%s</b> to the database.'), $this->level) . "</div>";

			$this->level = '';

		}

		echo $this->error;
		exit();

	}

}

$addLevel = new Add_level();