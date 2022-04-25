<?php


include_once('classes/generic.class.php');

/** Check if the browser set a referrer. */
$redirect = getenv('HTTP_REFERER') ? getenv('HTTP_REFERER') : 'login.php';

/** See if the admin wants to redirect to a specific page or not. */
$redirect = $generic->getOption('signout-redirect-referrer-enable')
			? $redirect
			: $generic->getOption('signout-redirect-url');

/**
 * Begin removing their existence.
 *
 * Good bye friend :(. Promise you'll come back?!
 */
if (isset($_SESSION['jigowatt']['username'])) :
	session_unset();
	session_destroy();
endif;

/** Voila! Here we shall gently nudge them somewhere else. */
header('Location: ' . $redirect);
exit();