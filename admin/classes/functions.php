<?php

/**
 * Couple functions, including a mammoth pagination one.
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
include_once( __DIR__ . '/DB.php');
include_once __DIR__ . '/../../permission.php';

function pr($arr)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    exit;
}

if (!function_exists('dd')) {
    function dd() {
        echo '<pre>';
        $args = func_get_args();
        foreach ($args as $arg) {
            var_dump($arg);
        }
        echo '</pre>';
        die();
    }
}

/* Number of rows per page. */
if ( !empty($_POST['showUsers']) )
	$_SESSION['jigowatt']['users_page_limit'] = $_POST['showUsers'];

if ( !empty($_POST['showLevels']) )
	$_SESSION['jigowatt']['levels_page_limit'] = $_POST['showLevels'];

/* Retrieve a user in table format */
function displayUsers($row) {

	global $generic;

	if(empty($row)) return false;

	/* Admin user */
	$admin = in_array(1, unserialize($row['user_level']))
			 ? " <span class='label label-danger'>" . _('admin') . "</span>"
			 : '';

	/* Restricted user */
	$restrict = !empty($row['restricted'])
				? " <span class='label label-warning'>"._('restricted')."</span>"
				: '';

	/* Registered date */
	$timestamp = strtotime($row['timestamp']);
	$reg_date  = date('M d, Y', $timestamp) . ' ' . _('at') . ' ' . date('h:i a', $timestamp);

	/* Last login */
	$params    = array( ':user_id'=> $row['user_id'] );
	$stmt      = $generic->query("SELECT `timestamp` FROM `login_timestamps` WHERE `user_id` = :user_id ORDER BY `timestamp` DESC LIMIT 0,1", $params);
	$timeRow   = $stmt->fetch(PDO::FETCH_NUM);
	$lastLogin = !empty($timeRow)
				 ? date('M d, Y', strtotime($timeRow[0])) . ' ' . _('at') . ' ' . date('h:i a', strtotime($timeRow[0]))
				 : '-';

	/* Email address */
	$email = $row['email'];

	/* Output */
	?>
	<tr>
		<td><a href="users.php?uid=<?php echo $row['user_id']; ?>"><?php echo $generic->get_gravatar($email, true, 20, 'mm', 'g', array('style' => '1')); ?> <?php echo $row['username']; ?></a><?php echo $admin . $restrict; ?></td>
		<td><?php echo $row['name']; ?></td>
		<td><?php echo $email; ?></td>
		<td><?php echo $reg_date; ?></td>
		<td><?php echo $lastLogin ; ?></td>
	</tr>
	<?php

}

/* List recently registered users */
function list_registered() {

	$pagination = pagination('login_users','ORDER BY timestamp DESC');
	global $generic, $sql, $query;

	/** Check that at least one row was returned. */
	$query = $generic->query($sql);
	if($query->rowCount() > 0) {

	/** Display table of recently registered users. */
	?>
	<table class="table">
		<thead>
			<tr>
				<th><?php echo _('Username'); ?></th>
				<th><?php echo _('Name'); ?></th>
				<th><?php echo _('Email'); ?></th>
				<th><?php echo _('Registered Date'); ?></th>
				<th><?php echo _('Last Login'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		while($row = $query->fetch(PDO::FETCH_ASSOC))
			echo displayUsers($row);
		?>
		</tbody>
	</table>

	<?php

	echo $pagination;
	} else { echo _('Sorry, there are no recently registered users.'); }

}

/* Find users in the current level */
function in_level() {

	global $generic;

	if(!empty($_GET['lid'])) :

		$lid = $_GET['lid'];
		$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? (int) $_GET['page'] : 1;
		$limit = 10;
		$StartIndex = $limit*($page-1);

		$sql = "SELECT * FROM login_users";
		$stmt = $generic->query($sql);

		$count = 0;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			if( array_intersect(array($lid),unserialize($row['user_level'])) ) $count++;

		if ($count < 1) {
			echo '<p>'._('No users found!').'</p>';
			return false;
		}

		?>

		<table class="table">
			<thead>
				<tr>
					<th><?php echo _('Username'); ?></th>
					<th><?php echo _('Name'); ?></th>
					<th><?php echo _('Email'); ?></th>
					<th><?php echo _('Registered Date'); ?></th>
					<th><?php echo _('Last Login'); ?></th>
				</tr>
			</thead>
			<tbody>

		<?php

		/* Print out each user of this level */
		$params = array( ':user_level' => "%:\"$lid\";%" );
		$sql = "SELECT * FROM login_users WHERE user_level LIKE :user_level ORDER BY timestamp DESC LIMIT $StartIndex,$limit";
		$stmt = $generic->query($sql, $params);
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			echo displayUsers($row);

		?>

			</tbody>
		</table>

		<?php

		echo pagination('login_users','ORDER BY timestamp DESC',"$count");

	endif;

}

function user_levels() {

	$pagination = pagination('login_levels');

	global $sql, $query, $generic;

	/* Check that at least one row was returned */
	$stmt = $generic->query($sql);
	if($stmt->rowCount() < 1) return false;

	/* Manage levels */
	?><table class='table table-hover'>
			<thead>
				<tr>
					<th><?php echo _('Name'); ?></th>
					<th><?php echo _('Active Users'); ?></th>
					<th><?php echo _('Redirect'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php

				while($row = $stmt->fetch(PDO::FETCH_ASSOC)) :

				/* Count of users in this level */
				$lid = $row['id'];
				$params = array( ':user_level' => "%:\"$lid\";%" );
				$query = $generic->query("SELECT COUNT(user_level) as num FROM login_users WHERE user_level LIKE :user_level", $params);
				$count = $query->fetch(PDO::FETCH_ASSOC);
				$count = $count['num'];

				/* Admin level? */
				$admin = ($row['id'] == 1)
						  ? ' <span class="label label-danger">*</span>'
						  : '';

				/* Disabled level? */
				$status = !empty($row['level_disabled'])
						  ? ' <span class="label label-warning">'._('Disabled').'</span>'
						  : '';
			?>

				<tr>
					<td><a href="levels.php?lid=<?php echo $lid; ?>"><?php echo $row['level_name']; ?></a><?php echo $status; ?></td>
					<td width="15%"><?php echo $count; ?></td>
					<td><a href="<?php echo $row['redirect']; ?>"><?php echo $row['redirect']; ?></a></td>
				</tr>

			<?php endwhile; ?>
			</tbody>
			</table>

	<?php echo $pagination;

}

function pagination($table, $args = '',$total_pages = '') {

	global $sql, $query, $generic;

	/** Hashtags, a workaround for when switching pages and not being redirected to the tab. */
	$hash  = '';

	/** Desired rows per page. */
	$limit = 10;

	/* Setting the page limit and hash. */
	if($table == 'login_levels') :
		$hash = '#level-control';
		if (!empty($_SESSION['jigowatt']['levels_page_limit']))
			$limit = $_SESSION['jigowatt']['levels_page_limit'];
	endif;

	if($table == 'login_users') :
		$hash = '#user-control';
		if (!empty($_SESSION['jigowatt']['users_page_limit']))
			$limit = $_SESSION['jigowatt']['users_page_limit'];
	endif;

	/** The page number to retrieve. */
	$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? (int)$_GET['page'] : 1;

	if (!empty($_GET['info'])) {
		if ($_GET['info'] != $table)
			$page = 1;
	}

	$StartIndex = $limit*($page-1);
	$stages = 3;

	$sql = "SELECT * FROM $table $args LIMIT $StartIndex, $limit";
	$query = "SELECT COUNT(*) as num FROM $table $args";

	$next = $page + 1; $previous = ($page - 1 != 0) ? $page - 1 : $page;

	if (empty($total_pages)) :
		$stmt = $generic->query($query);
		$total_pages = $stmt->fetch();
		$total_pages = $total_pages['num'];
	endif;
	$lastPage = ceil($total_pages/$limit);
	$lastPage1 = $lastPage - 1;

	$paginate = '';
	if($lastPage > 0) :

		$paginate = '<div class=""><ul class="pagination">';

		// Previous.
		$paginate .= ($page > 1) ? '<li class="prev"><a href="?' . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$previous"))) . $hash . '">&larr; '._('Previous').'</a></li>' : '<li class="prev disabled"><a href="#">&larr; '._('Previous').'</a></li>';

		if($lastPage < 7 + ($stages * 2)) {
			for ($counter = 1; $counter <= $lastPage; $counter++)
				$paginate .= ($counter == $page) ? "<li class='active'><a href='#'>$counter</a></li>" : "<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$counter"))) . "$hash'>$counter</a></li>";
		}
		elseif($lastPage > 5 + ($stages * 2)) {

			/** Hide end pages. */
			if($page < 1 + ($stages * 2)) {
				for ($counter = 1; $counter < 4 + ($stages * 2); $counter++)
					$paginate .= ($counter == $page) ? "<li class='active'><a href='#'>$counter</a></li>" : "<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$counter"))) . "$hash'>$counter</a></li>";

				$paginate .= "
							<li><a href='#'>&hellip;</a></li>
							<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$lastPage1"))) . "$hash'>$lastPage1</a></li>
							<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$lastPage"))) . "$hash'>$lastPage</a></li>
							";
			}

			/** Hide start & end pages. */
			elseif($lastPage - ($stages * 2) > $page && $page > ($stages * 2)) {

				$paginate .= "
							<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "1"))) . "$hash'>1</a></li>
							<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "2"))) . "$hash'>2</a></li>
							<li><a href='#'>&hellip;</a></li>
							";

				for ($counter = $page - $stages; $counter <= $page + $stages; $counter++)
					$paginate .= ($counter == $page) ? "<li class='active'><a href='#'>$counter</a></li>" : "<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$counter"))) . "$hash'>$counter</a></li>";

				$paginate .= "
							<li><a href='#'>&hellip;</a></li>
							<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$lastPage1"))) . "$hash'>$lastPage1</a></li>
							<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$lastPage1"))) . "$hash'>$lastPage</a></li>
							";
			}

			/** Hide start pages. */
			else {

				$paginate .= "
							<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "1"))) . "$hash'>1</a></li>
							<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "2"))) . "$hash'>2</a></li>
							<li><a href='#'>&hellip;</a></li>
							";
				for ($counter = $lastPage - (2 + ($stages * 2)); $counter <= $lastPage; $counter++)
					$paginate .= ($counter == $page) ? "<li class='active'><a href='#'>$counter</a></li>" : "<li><a href='?" . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$counter"))) . "$hash'>$counter</a></li>";

			}
		}

		/** Next button. */
		$paginate .= ($lastPage != $page) ? '<li class="next"><a href="?' . http_build_query(array_merge($_GET, array('info' => $table, "page" => "$next"))) . $hash . '">'._('Next').' &rarr;</a></li>' : '<li class="next disabled"><a href="#">'._('Next').' &rarr;</a></li>';
		$paginate .= '</ul></div>';

	endif;

	return $paginate;

}

function admin_render_partials( $partials_file, array $var = array(), $extension = '.php' ) {
	ob_start();
	extract($var);
	include_once __DIR__ . '/../partials/' . $partials_file . $extension;
	return ob_get_clean();
}

function init_message_bags() {
	if ( empty( $_SESSION['message.bags'] ) ) {
		$_SESSION['message.bags'] = array();
	}
}

function put_errors( array $errors ) {
	foreach ( $errors as $field => $error ) {
		$_SESSION['message.bags']['errors'][$field] = $error;
	}
}

function flush_errors() {
	
	if ( isset($_SESSION['message.bags']['errors']) ) {
		$errors = $_SESSION['message.bags']['errors'];
		unset($_SESSION['message.bags']['errors']);
		return $errors;
	}
	
	return array();
}
function get_errors() {

	if ( isset($_SESSION['message.bags']['errors']) ) {
		$errors = $_SESSION['message.bags']['errors'];
		return $errors;
	}

	return array();
}

function put_flash_message($key, $message) {
	$_SESSION['message.bags']['flash'][$key] = $message;
}

function flush_message( $key ) {
	if ( isset($_SESSION['message.bags']['flash'][$key]) ) {
		$flash = $_SESSION['message.bags']['flash'][$key];
		unset($_SESSION['message.bags']['flash'][$key]);
		return $flash;
	}

	return null;
}

function get_post_values( $key = null ) {
	if ( $key ) {
		return !empty($_SESSION['post.values'][$key]) ? $_SESSION['post.values'][$key] : null;
	}

	$post_values = !empty($_SESSION['post.values']) ? $_SESSION['post.values'] : array();
	$_SESSION['post.values'] = array();
	return $post_values;
}

function flash_post_values()
{
	$_SESSION['post.values'] = array();
}

function get_user_levels()
{
	$var = [];

	global $generic;

	$query = $generic->query('select * from login_levels');

	while ( $r = $query->fetch(PDO::FETCH_OBJ) ) {
		$var[$r->id] = $r->level_name;
	}

	return $var;
}

function getUserDetailsById( $userId ) {
	global $generic;
	$query = $generic->query('select * from login_users WHERE user_id = ' . $userId);
	return $query->fetch(PDO::FETCH_ASSOC);
}

function getUsers() {
	$db = DB::getInstance();

	return $db->query('SELECT * FROM login_users')->toArray();
}

function getUsersByLevel( $level ) {
	global $generic;

	$shortenList = array();

	$query = $generic->query('select * from login_users');
	$users = $query->fetchAll(PDO::FETCH_ASSOC);

	foreach ( $users as $user ) {
		$lvl = $user['user_level'];
		$array = unserialize($lvl);

		if ( in_array( $level, $array ) ) {
			$shortenList[] = $user;
		}
	}

	return $shortenList;
}

function flashInvoiceSession() {
	if ( !empty( $_SESSION['invoice'] ) ) {
		unset($_SESSION['invoice']);
	}
}

function createInvoiceDraft() {
	
	$db = DB::getInstance();
	$db->insert('invoice', array(
	        'confirm_invoice' => 0,
            'created_by' => $_SESSION['jigowatt']['user_id']
        ));

	return $db->lastId();
}

function createInvoiceDraftFromAppointment( $appointmentId ) {
	$db = DB::getInstance();
	$appointment = getAppointment( $appointmentId );
	if ( !empty($appointment) ) {
		$appointment = $appointment[0];
		$db->insert('invoice', [
			'confirm_invoice' => 0,
			'created_by' => $_SESSION['jigowatt']['user_id'],
			'client_id' => $appointment['client_id'],
			'tech' => $appointment['tech_id'],
			'reclamation' => $appointment['reclamation'],
		]);
	} else {
		die('Error creating invoice. Please contact administrator.');
	}
}

function addInvoicePhoto($data = array()){
    $db = DB::getInstance();
    $db->insert('invoice_photo', $data);

    return $db->lastId();
}

function getInvoice( $invoiceId ) {
	$db = DB::getInstance();
	$result = $db->query('SELECT * FROM invoice WHERE id = ?', [$invoiceId])->toArray();
	return isset($result[0]) ? $result[0] : array();
}

function getInvoicesByClientId( $clientId ) {
    $db = DB::getInstance();

    return $db->table('invoice')->where( 'client_id', '=', $clientId )->get()->toArray();
}

function getInvoicePhotos( $invoiceId ) {
	$db = DB::getInstance();

	return $db->table('invoice_photo')->where('invoice_id', '=', $invoiceId)->get()->toArray();
}

function getAppointmentPhotos( $appointment ) {
	$db = DB::getInstance();

	return $db->table('appointment_photo')->where('appointment_id', '=', $appointment)->get()->toArray();
}

function getAppointmentPhotosByToken( $token ) {
	$db = DB::getInstance();

	return $db->table('appointment_photo')->where('token', '=', $token)->get()->toArray();
}

function addAppointmentPhoto($data = array()){
	$db = DB::getInstance();
	$db->insert('appointment_photo', $data);

	return $db->lastId();
}

function delAppointmentPhoto($id){
	try {
		$db = DB::getInstance();
		return $db->delete('appointment_photo', $id);
	} catch (Exception $e) {
		return FALSE;
	}
}

function deleteInvoicePhotos( $photoId ) {
	try {
		$db = DB::getInstance();

		$photoObj = $db->table('invoice_photo')->where('id', '=', $photoId)->get()->first();

		if ( $photoObj )
			$photo = $photoObj->toArray();

		return $db->delete('invoice_photo', $photoId);
		
		$UploadFileDirectory = __DIR__ . '/../../assets/uploads/';
		if ( file_exists( $UploadFileDirectory . $photo['photo_url'] ) ) {
			if ( unlink( $UploadFileDirectory . $photo['photo_url'] ) )
				return $db->delete('invoice_photo', $photoId);
		}

		return FALSE;
	} catch (Exception $e) {
		return FALSE;
	}
}

function deleteMoldPhotos( $photoId ) {
	try {
		$db = DB::getInstance();

		$photoObj = $db->table('mold_photo')->where('id', '=', $photoId)->get()->first();

		if ( $photoObj )
			$photo = $photoObj->toArray();

		return $db->delete('mold_photo', $photoId);

		$UploadFileDirectory = __DIR__ . '/../../assets/uploads/';
		if ( file_exists( $UploadFileDirectory . $photo['photo_url'] ) ) {
			if ( unlink( $UploadFileDirectory . $photo['photo_url'] ) )
				return $db->delete('mold_photo', $photoId);
		}

		return FALSE;
	} catch (Exception $e) {
		return FALSE;
	}
}

function getIvoiceParts( $invoiceId ) {
    $db = DB::getInstance();

    return $db->table('parts')->where('invoice_id', '=', $invoiceId)->get()->toArray();
}

function addMoldPhoto($data = array()){
    $db = DB::getInstance();
    $db->insert('mold_photo', $data);

    return $db->lastId();
}

function createMoldDraft() {
    $db = DB::getInstance();
    $db->insert('mold', array('confirm_mold' => 0));

    return $db->lastId();
}

function flashMoldSession() {
    if ( !empty( $_SESSION['mold_id'] ) ) {
        unset($_SESSION['mold_id']);
    }
}

function updateMold($data) {
    $db = DB::getInstance();
    $db->update('mold', $data, $_SESSION['mold_id']);

    return $db->lastId();
}

function getMold( $moldId ) {

    $db = DB::getInstance();
    $query = 'SELECT mold.*, login_users.username FROM mold LEFT JOIN login_users ON (mold.estimator = login_users.user_id) WHERE mold.id = '.$moldId;
    $result = $db->query($query)->first();

    return !empty($result) ? $result->toArray() : array();
}

function getMoldPhotos( $moldId ) {
    $db = DB::getInstance();

    return $db->table('mold_photo')->where('mold_id', '=', $moldId)->get()->toArray();
}

function getMolds(){
    $db = DB::getInstance();
    $molds = $db->table('mold')->where('confirm_mold', '=', 1)->orderBy('id', 'desc')->get();

    if ( $molds )
        return $molds->toArray();

    return [];
}

function getClients() {
	$db = DB::getInstance();
	$clients = $db->table('clients')->get();

	if ( $clients )
		return $clients->toArray();

	return [];
}

function getClient( $id ) {
	$db = DB::getInstance();
	$result = $db->query('SELECT fname f_name, lname l_name, cie company, address, tel1 tel, email FROM clients WHERE clientid = ?', [ $id ])->toArray();

	if ( !empty($result) ) {
		return $result[0];
	}

	return null;
}

function getClientName($clientId){
    $db = DB::getInstance();
    $clients = $db->table('clients')->select('fname,lname')->where('clientid', '=', $clientId)->get()->toArray();
    if ( $clients[0] )
        return $clients[0]['fname'].' '.$clients[0]['lname'];

    return '';
}

function createSchedule( $schedule ) {
    $db = DB::getInstance();
    $schedule['date'] = date('Y-m-d', strtotime($schedule['date']));
    //check if already 4 schedule created for the day

	if ( time() >= strtotime($schedule['date']) ) {
		return array(0, 'You can not create schedule for an old date.');
	}

    $schedule['start_time'] = ($schedule['start_time'] < 10 ? "0" . $schedule['start_time'] : $schedule['start_time']) . ":00";
    $schedule['end_time'] = ($schedule['end_time'] < 10 ? "0" . $schedule['end_time'] : $schedule['end_time']) . ":00";


    $start_date = $schedule['start_time'] = date('H:i:s', strtotime($schedule['start_time']));
    $end_date = date('H:i:s', strtotime($schedule['end_time']));
    $end_date = $schedule['end_time'] = date('H:i:s', strtotime($schedule['end_time']) - 1);

    $schedules = $db->query('SELECT * FROM schedule WHERE date = ?', [ $schedule['date'] ])->toArray();
    $totalCreatedForTheDay = count($schedules);

    if ( $totalCreatedForTheDay <= 4 ) {
        //check if same user schedule are not collapsing each other
        $ownSchedules = $db->query('SELECT * FROM schedule WHERE date = ? AND ( (start_time BETWEEN ? AND ? ) OR ( end_time BETWEEN ? AND ? ) ) AND user_id = ?', [
            $schedule['date'], $start_date, $end_date,  $start_date, $end_date, $schedule['user_id']
        ])->toArray();

        if ( ! count($ownSchedules) ) {
            $scheduleId = $db->insert('schedule', $schedule);
			createTimeSlots($scheduleId, $start_date, $end_date, $schedule['time_block']);
            return array($scheduleId, $scheduleId . ' created successfully.');
        } else {
            return array(0, 'Already a schedule block available for this user on ' . $schedule['date']);
        }

    } else {
        return array(0, 'Already a maximum number of schedule blocks created for this day.');
    }
}

function getSchedules( $date ) {
    $db = DB::getInstance();
    return $schedules = $db->query('SELECT * FROM schedule WHERE date = ? ORDER BY start_time', [ $date ])->toArray();
}

function getSchedulesByType( $date, $type ) {
    $db = DB::getInstance();
    return $schedules = $db->query('SELECT * FROM schedule WHERE date = ? AND user_type = ? ORDER BY start_time', [ $date, $type ])->toArray();
}

function getSchedulesRangeDate( $start_date, $end_date ) {
    $db = DB::getInstance();
    return $schedules = $db->query('SELECT * FROM schedule WHERE date BETWEEN ? AND ? ORDER BY start_time', [ $start_date, $end_date ])->toArray();
}

function getUserNameById( $id ) {
    $db = DB::getInstance();
    $result = $db->query('SELECT name FROM login_users  WHERE user_id = ?', [ $id ])->toArray();
    return $result[0]['name'];
}

function createTimeSlots($scheduleId, $start_date, $end_date, $timeBlock) {
	$db = DB::getInstance();
	$slots = getScheduleSlots( $timeBlock, 0, $start_date, $end_date );

	foreach ( $slots as $slot ) {
		$data = array(
			'schedule_id' => $scheduleId,
			'start_time'  => $slot['start_time'],
			'end_time'    => $slot['end_time'],
		);
		$db->insert('appointment_time_slots', $data);
	}
}

function getAppointments( $scheduleId ) {
	$db = DB::getInstance();
	$slots = array();

	$schedule = $db->query('SELECT * FROM schedule WHERE id = ?', [ $scheduleId ])->toArray();
	if ( empty($schedule) ) {
		return FALSE;
	}

	$startTime = $schedule[0]['start_time'];
	$endTime = $schedule[0]['end_time'];
	$timeBlock = $schedule[0]['time_block'];

	$periods = getScheduleSlots( $timeBlock, 0, $startTime, $endTime );
	echo '<pre>';
	var_export($periods);
}

function getServiceScheduleSlots($duration,$break, $stTime,$enTime)
{
	$start = new DateTime($stTime);
	$end = new DateTime($enTime);
	$interval = new DateInterval("PT" . $duration. "M");
	$breakInterval = new DateInterval("PT" . $break. "M");
	$periods = array();
	for ($intStart = $start;
		 $intStart < $end;
		 $intStart->add($interval)->add($breakInterval)) {

		$endPeriod = clone $intStart;
		$endPeriod->add($interval);
		if ($endPeriod > $end) {
			$endPeriod=$end;
		}
		$periods[] = $intStart->format('H:iA') .
			' - ' .
			$endPeriod->format('H:iA');
	}

	return $periods;
}

function getScheduleSlots($duration, $break, $stTime, $enTime)
{
	$start = new DateTime($stTime);
	$end = new DateTime($enTime);
	$interval = new DateInterval("PT" . $duration. "M");
	$breakInterval = new DateInterval("PT" . $break. "M");

	$periods = array();

	for ($intStart = $start;
		 $intStart < $end;
		 $intStart->add($interval)->add($breakInterval)) {

		$endPeriod = clone $intStart;
		$endPeriod->add($interval);
		if ($endPeriod > $end) {
			$endPeriod=$end;
		}
		$periods[] = array(
			'start_time' => $intStart->format('H:i:s'),
			'end_time'   => $endPeriod->format('H:i:s')
		);
	}

	return $periods;
}


function getToken( $length = 40 ){
	$token = "";
	$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	$codeAlphabet.= "0123456789";
	$max = strlen($codeAlphabet); // edited

	for ($i=0; $i < $length; $i++) {
		$token .= $codeAlphabet[mt_rand(0, $max-1)];
	}

	return $token;
}

function getAppointmentsByDate( $date ) {
	$db = DB::getInstance();

	$query = <<<SQL
SELECT ap.*, ad.client_id, ad.tech_id, ad.reclamation, CONCAT(c.fname, ' ', c.lname) AS client_name, u.name as tech_name, ats.start_time, ats.end_time FROM appointment AS ap
  LEFT JOIN appointment_details AS ad ON ad.appointment_id = ap.id
  LEFT JOIN appointment_time_slots AS ats ON ats.id = ap.appointment_slot_id
  LEFT JOIN clients AS c ON c.clientid = ad.client_id
  LEFT JOIN login_users u ON u.user_id = ad.tech_id
WHERE ap.date = ? ORDER BY ats.start_time ASC
SQL;
	return $db->query($query, [ $date ])->toArray();
}

function getTotalAppointmentsFromScheduleId( $scheduleId ) {
	$db = DB::getInstance();

	$results = $db->query('SELECT COUNT(*) as total FROM appointment WHERE schedule_id = ?', [ $scheduleId ])->first()->toArray();

	return $results['total'];
}

function getAppointment( $id ) {
	$db = DB::getInstance();
	$query = <<<SQL
SELECT ad.*,
  s.address as schedule_address,
  ap.type, ap.schedule_id, ap.appointment_slot_id, ap.date,
  ats.start_time, ats.end_time,
  CONCAT(c.fname, ' ', c.lname) client_name, c.email, c.cie, c.tel1, c.fname client_fname, c.lname client_lname,
  u.name as tech_name
FROM appointment_details ad
  LEFT JOIN appointment ap ON ap.id = ad.appointment_id
  LEFT JOIN appointment_time_slots ats ON ats.appointment_id = ad.appointment_id
  LEFT JOIN login_users u ON ad.tech_id = u.user_id
  LEFT JOIN clients c ON ad.client_id = c.clientid
  LEFT JOIN schedule s ON ap.schedule_id = s.id
WHERE ad.appointment_id = ?
SQL;
	return $db->query($query, [ $id ])->toArray();
}

function saveAppointment( $appointment_id, $data ) {
    $db = DB::getInstance();
    $db->update('appointment_details', $data, $appointment_id);

    return $db->lastId();
}

function amount_format( $amount, $currency = '$' ) {
	return $currency . number_format($amount, 2);
}

function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

	$dates = array();
	$current = strtotime($first);
	$last = strtotime($last);

	while( $current <= $last ) {

		$dates[] = date($output_format, $current);
		$current = strtotime($step, $current);
	}

	return $dates;
}


function getReclamation( $id ) {

	$db = DB::getInstance();
	$result = $db->query('SELECT * FROM reclamation WHERE id = ?', [ $id ])->toArray();

	if ( !empty($result) )
		return $result[0];

	return null;
}

function getReclamationByAppointmentData( $appointment ) {
	$db = DB::getInstance();
	$result = $db->query('SELECT * FROM reclamation WHERE reclamation = ?', [ $appointment['reclamation'] ])->toArray();

	if ( !empty($result) ) {
		return $result[0]['id'];
	} else {
		$rid = createReclamationIfNotExists($appointment['reclamation'], array(
			'client_id' => $appointment['client_id'],
			'reclamation' => $appointment['reclamation'],
			'insurer' => $appointment['insurer'],
			'vin' => $appointment['vin'],
			'brand' => $appointment['brand'],
			'model' => $appointment['model'],
			'year' => $appointment['year'],
			'inventory' => $appointment['inventory'],
			'color' => $appointment['color'],
			'brake_type' => $appointment['brake_type'],
			'particular_area' => $appointment['particular_area'],
			'millage' => $appointment['millage'],
			'creation_style' => $appointment['type'],
		));

		return $rid;
	}
}

function createReclamationIfNotExists( $reclamation, $data ) {
	$db = DB::getInstance();
	$reclamation = $db->query('SELECT COUNT(id) as result FROM reclamation WHERE reclamation = ?', [$reclamation])->first();
	if ( $reclamation->result == 0 ) {
		return $db->insert('reclamation',$data);
	}
}

function getEstimationIdByReclamation( $reclamation ) {
	$db = DB::getInstance();
	$estimation = $db->query('SELECT id FROM mold WHERE reclamation = ?', [$reclamation])->toArray();
	if ( isset($estimation[0]['id']) ) {
		return $estimation[0]['id'];
	}

	return 0;
}

function getRepairScheduleIdByReclamation( $rec ) {
	$db = DB::getInstance();
	$result = $db->query('SELECT a.id, ad.reclamation
					FROM appointment a 
					LEFT JOIN appointment_details ad ON ad.appointment_id = a.id
						WHERE ad.reclamation = ?
						AND a.type = ?
						', [$rec, 'repair'])->toArray();
	if ( isset($result[0]['id']) ) {
		return $result[0]['id'];
	}

	return FALSE;
}

function getInvoiceByReclamation( $rec, $returnWholeRow = false) {
	$db = DB::getInstance();

	$result = $db->query('
		SELECT * FROM invoice WHERE reclamation = ?
	', [$rec])->toArray();

	if ( isset($result[0]['id']) ) {
		return $returnWholeRow ? $result[0] : $result[0]['id'];
	}

	return FALSE;
}

function isUser( $type, $userId ) {
	$db = DB::getInstance();
	$result = $db->query('SELECT user_level FROM login_users WHERE user_id = ?', [$userId])->first();
	$loginLevel = $db->query('select id from login_levels WHERE id = ?', [$type])->first();
	$userLevels = unserialize($result->user_level);
	foreach ( $userLevels as $level ) {
		if ( $level == $loginLevel->id ) {
			return true;
		}
	}
	
	return false;
}

function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function ajaxError($message, $data = []) {
    http_response_code(500);
    echo json_encode(['message' => $message, 'debug' => $data]);
    die;
}

/**
 * @param $operation
 * @return bool
 */
function can($operation) {
    $permissions = $_SESSION['permission'] ?? [];

    $can = in_array($operation, $permissions) || in_array('*', $permissions);

    if ($can) return true;

    if (isAjaxRequest()) {
        ajaxError('You do not have permission to run this operation!', $_SESSION);
    }

    protect('my_ass');

    return false;
}

function isAdmin()
{
    $user = isset($_SESSION['jigowatt']) ? $_SESSION['jigowatt'] : null;

    if ($user) {
        return isUser(BsgUser::ADMIN, $user['user_id']);
    }

    return false;
}

function isTech()
{
    $user = isset($_SESSION['jigowatt']) ? $_SESSION['jigowatt'] : null;

    if ($user) {
        return isUser(BsgUser::TECH, $user['user_id']);
    }

    return false;
}

function isEstimator()
{
    $user = isset($_SESSION['jigowatt']) ? $_SESSION['jigowatt'] : null;

    if ($user) {
        return isUser(BsgUser::ESTIMATOR, $user['user_id']);
    }

    return false;
}

function isExSinstre()
{
    $user = isset($_SESSION['jigowatt']) ? $_SESSION['jigowatt'] : null;

    if ($user) {
        return isUser(BsgUser::EX_SINISTRE, $user['user_id']);
    }

    return false;
}

function userId()
{
    $user = isset($_SESSION['jigowatt']) ? $_SESSION['jigowatt'] : null;

    if ($user) {
        return $user['user_id'];
    }

    return null;
}
