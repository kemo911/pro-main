<?php

include_once __DIR__ . '/../../config.php';

if ( !empty($_REQUEST['validationAction']) ) {

    $conn = new PDO("mysql:host=$host;dbname=$dbName", $dbUser, $dbPass);

    switch ( $_REQUEST['validationAction'] ) {
        case 'email_uniqueness_create':

            if ( empty($_REQUEST['email']) ) {
                http_response_code(404);
                exit();
            }

            $qry = 'SELECT COUNT(user_id) as totalFound FROM login_users WHERE email = :email';
            if ( isset($_REQUEST['skip']) ) {
                $qry .= ' AND user_id NOT IN ('. $_REQUEST['skip'] .')';
            }
            
            $stmt = $conn->prepare($qry);
            $stmt->execute(
                array(
                    ':email' => $_REQUEST['email']
                )
            );

            $result = $stmt->fetch(PDO::FETCH_OBJ);

            if ( !$result ) {
                http_response_code(500);exit();
            }

            if ( $result->totalFound == 0 ) {
                http_response_code(200); exit();
            }

            http_response_code(401);exit();

            break;
        case 'username_uniqueness_create':

            if ( empty($_REQUEST['username']) ) {
                http_response_code(404);
                exit();
            }
            
            $qry = 'SELECT COUNT(user_id) as totalFound FROM login_users WHERE username = :username';
            if ( isset($_REQUEST['skip']) ) {
                $qry .= ' AND user_id NOT IN ('. $_REQUEST['skip'] .')';
            }
            
            $stmt = $conn->prepare($qry);
            $stmt->execute(
                array(
                    ':username' => $_REQUEST['username']
                )
            );

            $result = $stmt->fetch(PDO::FETCH_OBJ);

            if ( !$result ) {
                http_response_code(500);exit();
            }

            if ( $result->totalFound == 0 ) {
                http_response_code(200); exit();
            }

            http_response_code(401);exit();

            break;
    }
} else {
    http_response_code(302);
}

if (!function_exists('http_response_code'))
{
    function http_response_code($newcode = NULL)
    {
        static $code = 200;
        if($newcode !== NULL)
        {
            header('X-PHP-Response-Code: '.$newcode, true, $newcode);
            if(!headers_sent())
                $code = $newcode;
        }
        return $code;
    }
}