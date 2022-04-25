<?php

class BsgUser {
    const ADMIN = 1;
    const TECH = 2;
    const ESTIMATOR = 3;

    const SET1 = [self::ADMIN];
    const SET2 = [self::TECH, self::ESTIMATOR];
}

class Permission {

    const USER_LEVEL_1 = "Admin";
    const USER_LEVEL_2 = "Technicien";
    const USER_LEVEL_3 = "Estimator";

    const ACCUIEL = 'accuiel';
    const CLIENTS = 'clients';
    const RECLAMATION = 'reclamation';
    const CALENDER = 'calender';
    const CALENDER_HOUR = 'calender.hour';
    const CALENDER_ESTIMATION_REPAIR = 'calender.est.repair';
    const ESTIMATION = 'estimation';
    const FACTURE = 'facture';
    const REPORT = 'report';
    const REPORT_RECLAMATIONS = 'report.reclamation';
    const REPORT_ESTIMATION = 'report.estimation';
    const REPORT_ESTIMATION_RENDEZ = 'report.est.rendez';
    const REPORT_FACTURES = 'report.factures';
    const REPORT_PIECES = 'report.pieces';
    const USERS = 'users';

    public static $permissions = [
        1 => [
            self::ACCUIEL, self::CLIENTS, self::RECLAMATION, self::CALENDER, self::CALENDER_HOUR,
            self::CALENDER_ESTIMATION_REPAIR, self::ESTIMATION, self::FACTURE, self::REPORT,
            self::REPORT_ESTIMATION, self::REPORT_ESTIMATION_RENDEZ, self::REPORT_FACTURES,
            self::REPORT_PIECES, self::USERS
        ],
        2 => [
            self::ACCUIEL, self::CLIENTS, self::RECLAMATION, self::CALENDER, self::CALENDER_HOUR,
            self::CALENDER_ESTIMATION_REPAIR, self::ESTIMATION, self::FACTURE, self::REPORT,
            self::REPORT_ESTIMATION, self::REPORT_ESTIMATION_RENDEZ, self::REPORT_FACTURES,
            self::REPORT_PIECES, self::USERS
        ],
        3 => [
            self::ACCUIEL, self::CLIENTS, self::RECLAMATION, self::CALENDER, self::CALENDER_HOUR,
            self::CALENDER_ESTIMATION_REPAIR, self::ESTIMATION, self::FACTURE, self::REPORT,
            self::REPORT_ESTIMATION, self::REPORT_ESTIMATION_RENDEZ, self::REPORT_FACTURES,
            self::REPORT_PIECES, self::USERS
        ]
    ];

    public static function capable( $perm )
    {
        $currentUserId = !empty($_SESSION['jigowatt']['user_id']) ? $_SESSION['jigowatt']['user_id'] : null;
        if ( !$currentUserId ) {
            return false;
        }
        $user = DB::getInstance()->table('login_users')->where('user_id', $currentUserId)->get()->first();
        $user_level = unserialize( $user->user_level );

        $loginLevel = DB::getInstance()->table('login_levels')->where('id', $user_level[0])->get()->first();

        if ( $success = in_array( $perm, self::$permissions[$loginLevel->level] ) ) {
            return true;
        }

        protect('ME');
    }
}

//if ( ! function_exists( 'can' ) ) {
//    function can( $perm ) {
//        return Permission::capable( $perm );
//    }
//}

if ( ! function_exists('allow_for') ) {
    function allow_for($level, $mustReturn = false) {

        if ( is_string($level) )
            $levels = explode('|', $level);
        else
            $levels = $level;

        $currentUserId = !empty($_SESSION['jigowatt']['user_id']) ? $_SESSION['jigowatt']['user_id'] : null;

        if ( !$currentUserId ) {
            return false;
        }

        $user = DB::getInstance()->table('login_users')->where('user_id', $currentUserId)->get()->first();
        $user_level = unserialize( $user->user_level );

        if (in_array($user_level[0], $levels)) {
            return true;
        }

        if ($mustReturn)
            return false;

        if (isAjaxRequest()) {
            ajaxError('You don\'t have permission to access this feature.');
        }

        protect('ME');
    }
}

if ( ! function_exists('denied_for') ) {
    function denied_for($level, $mustReturn = false) {
        if ( !allow_for($level, true) ) {

            if ($mustReturn)
                return false;

            if (isAjaxRequest()) {
                ajaxError('You don\'t have permission to access this feature.');
            }

            http_response_code(403);
            die;
        }

        return true;
    }
}


function allowForSet1() {
    return allow_for(BsgUser::SET1, true);
}

function allowForSet2() {
    return allow_for(BsgUser::SET2, true);
}