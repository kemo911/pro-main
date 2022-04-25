<?php
include_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../classes/functions.php';


if ( !empty($_FILES['file']) )
{
    $tmpName = $_FILES['file']['tmp_name'];
    $csvAsArray = array_map('str_getcsv', file($tmpName));


    //assume first row is header
    $header = !empty( $csvAsArray[0] ) ? $csvAsArray[0] : [];
    if ( empty( $header ) ) {
        returnResponse( [
            'success' => 0,
            'message' => 'Please provide a valid csv file',
        ] );
    }

    //assume header contain following fields
    /*
     array (
        0 => 'Last name',
        1 => 'First name',
        2 => 'Company',
        3 => 'Email',
        4 => 'Address',
        5 => 'Tel 1',
        6 => 'Tel 2',
      ),
     */
    foreach ( ['Last name', 'First name', 'Company', 'Email', 'Address', 'Tel 1', 'Tel 2'] as $index => $headerColumn ) {
        if ($header[$index] != $headerColumn) {
            returnResponse( [
                'success' => 0,
                'message' => 'Header does not match. You must have to provide the following fields. [Last name, First name, Company, Email, Address, Tel 1, Tel 2]',
            ] );
        }
    }

    /** @var PDO $database */
    $dbInstance = DB::getInstance();
    $database = $dbInstance->dbh;
    try {
        $database->beginTransaction();

        $prepare = $database->prepare('INSERT INTO clients(cie, address, fname, lname, tel1, tel2, email, note) VALUES (?,?,?,?,?,?,?,?)');

        foreach ( $csvAsArray as $index => $clientInfo ) {
            if ( $index == 0 ) continue;
            if ( !empty($clientInfo) ) {
                $prepare->execute([
                    $clientInfo[2],
                    $clientInfo[4],
                    $clientInfo[1],
                    $clientInfo[0],
                    $clientInfo[5],
                    $clientInfo[6],
                    $clientInfo[3],
                    'System Import',
                ]);
            }
        }

        $database->commit();
        returnResponse( [
            'success' => 1,
            'message' => 'Data imported successfully!',
        ] );
    } catch (Exception $exception) {
        $database->rollBack();
        returnResponse( [
            'success' => 0,
            'message' => 'Data not imported properly! Rollback changes.',
        ] );
    }
}

function returnResponse(array $resp)
{
    echo json_encode( $resp );
    exit();
}