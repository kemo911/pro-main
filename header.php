<?php

/**
 * Header file for main site.
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

ob_start();

if ( ! session_id() ) session_start();
if ( ! isset( $_SESSION['jigowatt'] ) ) {
    $_SESSION['jigowatt'] = array();
}

error_log( "HELLO MAIN SITE HEADER" );

//include_once( dirname(__FILE__) . '/classes/generic.class.php' ); /* sets SITE_PATH define */
include_once( dirname(__FILE__) . '/classes/translate.class.php' );

?><!DOCTYPE html><html lang="fr">

<head>

    <meta charset="utf-8">
    <title>Bosse ESG</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bosse ESG script">
     

    <!-- latest stable bootstrap framework via CDN as of 24/05/2017 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <link href="assets/css/jigowatt.css" rel="stylesheet">

    <link rel="shortcut icon" href="favicon.ico">

</head>

<body>

<!-- Navigation
================================================== -->

	<nav class="navbar navbar-default navbar-fixed-top">
	  <div class="container" >
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header" style="background-color: #000;">
	      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
	        <span class="sr-only">Menu</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      
	    </div>



	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" >
	      <ul class="nav navbar-nav">
				
			</ul>
		<?php if(isset($_SESSION['jigowatt']['username'])) { ?>
		<ul class="nav navbar-nav navbar-right">
			<li class="dropdown">
				<p class="navbar-text dropdown-toggle" data-toggle="dropdown" id="userDrop">
					<?php echo $_SESSION['jigowatt']['gravatar']; ?>
					<a href="#"><?php echo $_SESSION['jigowatt']['username']; ?></a>
					<b class="caret"></b>
				</p>
				<ul class="dropdown-menu">
		<?php if(in_array(1, $_SESSION['jigowatt']['user_level'])) { ?>
					<li><a href="admin/index.php"><i class="glyphicon glyphicon-home"></i> <?php _e('Control'); ?></a></li>
					<li><a href="admin/settings.php"><i class="glyphicon glyphicon-cog"></i> <?php _e('Configuration'); ?></a></li> <?php } ?>
					<li><a href="profile.php"><i class="glyphicon glyphicon-user"></i> <?php _e('Mon compte'); ?></a></li>
					<li class="divider"></li>
					<li><a href="logout.php"><?php _e('Se déconnecter'); ?></a></li>
				</ul>
			</li>
		</ul>
		<?php } else { ?>
		
		<?php } ?>
   </div><!-- /.navbar-collapse -->
  </div><!-- /.container -->
</nav>

<!-- Main content
================================================== -->
		<div class="container" >
			<div class="row">

				<div class="col-md-12">
