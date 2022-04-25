<?php

session_start();

ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

ini_set('html_errors', 1);

error_reporting(E_ALL);



include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');

include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');





//protect( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3, Permission::USER_LEVEL_4]));



$currentUser = getUserDetailsById($_SESSION['jigowatt']['user_id']);

$techGuys = getUsersByLevel( 4 );

$damageListForm = array(

    1 => 'CAPOT',

    2 => 'PAVILLON',

    3 => 'DESSUS HAYON',

    4 => 'VALISE HAYON',

    5 => 'PAN. LATERAL G',

    6 => 'PORTE ARR. G',

    7 => 'PORTE AV. G',

    8 => 'LONGERON G',

    9 => 'AILE G',

    10 => 'AILE D',

    11 => 'PORTE AV. D',

    12 => 'PORTE ARR. D',

    13 => 'PAN LATERAL D',

    14 => 'LONGERON D',

    15 => 'Supplément',

    'stripping' => 'Dégarnissage',

    'other_fees' => 'Autres frais',

    'glazier' => 'Vitrier',

    'work_force' => 'Main d\'oeuvre',

    'parts' => 'Pièces',
    'covid' => 'COVID',
);



include_once 'header.php';



include_once 'page/estimation.php';

//echo '<ng-view></ng-view>';



include_once 'footer.php';

