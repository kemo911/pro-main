<?php


ob_start();



if (!session_id()) session_start();

if (!isset($_SESSION['jigowatt'])) {

    $_SESSION['jigowatt'] = array();

}



error_log("HELLO ADMINISTRATION HEADER");



include_once(dirname(dirname(__FILE__)) . '/classes/translate.class.php');

include_once(dirname(__FILE__) . '/classes/functions.php');



?><!DOCTYPE html>
<html ng-app="app" lang="fr">


<head>


    <meta charset="utf-8">

    <title>Bosse ESG Admin</title>


    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Bosse ESG script">

    <meta name="robots" content="noindex,nofollow">


    <!-- latest stable bootstrap framework via CDN as of 24/05/2017 -->

    <!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">-->

    <!-- Optional theme -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/flatly/bootstrap.min.css">

    <!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">-->

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.0/css/responsive.bootstrap.min.css">


    <link href="assets/css/datepicker.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">


    <link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">

    <link href="assets/js/select2/select2.min.css" rel="stylesheet">


    <link href="../assets/css/jigowatt.css?sdfgh" rel="stylesheet">

    <link href="./assets/css/invoice.css" rel="stylesheet">

    <link href="/admin/assets/signature/jquery.signaturepad.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.8.2/fullcalendar.min.css">

    <link rel="stylesheet" href="assets/css/appointments.css">
    <link rel="stylesheet" href="assets/css/new.css">

    <link rel="shortcut icon" href="../favicon.ico">
    <script src="../assets/js/axios.min.js"></script>
    <script src="../assets/js/vue.min.js"></script>
    <script src="../assets/js/vue.shared.js"></script>

</head>

<?php if (!empty($pageClass)): ?>

<body class="<?php echo $pageClass; ?>">

<?php else: ?>

<body>

<?php endif; ?>


<!-- Navigation

================================================== -->


<nav class="navbar navbar-default navbar-fixed-top" style="background-color: #000;">

    <div class="container">

        <!-- Brand and toggle get grouped for better mobile display -->

        <div class="navbar-header">

            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">

                <span class="sr-only">Menu</span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

            </button>


        </div>


        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav" id="findme">

                <li><a href="/admin/index.php">Accueil</a></li>

                <?php if (protectThis( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_2, Permission::USER_LEVEL_3]) )) : ?>
                    <li><a href="clients.php">Clients</a></li>
                    <?php if ( !denied_for([BsgUser::ESTIMATOR, BsgUser::TECH], true) ): ?>
                    <li><a href="reclamation.php">Réclamation</a></li>
                    <?php endif; ?>
                    <!--<li><a href="/admin/schedule.php">Calendrier</a></li>-->
                    <li><a href="/admin/estimation.php">Estimation</a></li>
                <?php endif; ?>

                <?php if (protectThis(Permission::USER_LEVEL_1)) : ?>
                    <li><a href="/admin/main.php">Facture</a></li>
                <?php endif; ?>

<!--                --><?php //if ( !denied_for([BsgUser::ESTIMATOR], true) ): ?>
                <li><a href="/admin/reports.php">Rapports</a></li>
<!--                --><?php //endif; ?>

                <?php if (protectThis(Permission::USER_LEVEL_1)) : ?>
                <?php //if ([BsgUser::EX_SINISTRE] == 5) : ?>
                    <!--<li><a href="users-levels.php">Utilisateurs</a></li>-->
                    <?php //echo('');?>
                    <?php //else: ?>
                    <li><a href="users-levels.php">Utilisateurs</a></li>
                <?php //endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>


        </div><!-- /.navbar-collapse -->

    </div><!-- /.container -->

</nav>


<!-- Main content

================================================== -->
<?php if ( $_SERVER['SCRIPT_NAME'] != '/admin/estimation.php' ): ?>
<div class="container">
    <div class="row">
        <div class="col-md-12" style="padding-top: 25px;">
<?php endif; ?>



					