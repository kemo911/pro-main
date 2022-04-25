<?php


ob_start();

if ( ! session_id() ) session_start();
if ( ! isset( $_SESSION['jigowatt'] ) ) {
    $_SESSION['jigowatt'] = array();
}

error_log( "HELLO MAIN SITE HEADER" );

//include_once( dirname(__FILE__) . '/classes/generic.class.php' ); /* sets SITE_PATH define */
include_once( dirname(__FILE__) . '/classes/translate.class.php' );

?><!DOCTYPE html><html lang="en">

<head>

    <meta charset="utf-8">
    <title>BosseESG - Éco Solution Grêle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link href="assets/css/jigowatt.css" rel="stylesheet">
    <link rel="shortcut icon" href="favicon.png">
    <meta name="robots" content="noindex,nofollow">

<body style="background-color:#000">

<!-- Navigation
================================================== -->


<!-- Main content
================================================== -->
		<div class="container">
			<div class="row">

				<div class="col-md-12">
