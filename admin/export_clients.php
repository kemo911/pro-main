<?php

include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
protect("Admin");

if ( !function_exists('outputCSV') ) {
    function outputCSV($data,$file_name = 'file.csv') {
        # output headers so that the file is downloaded rather than displayed
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$file_name");
        # Disable caching - HTTP 1.1
        header("Cache-Control: no-cache, no-store, must-revalidate");
        # Disable caching - HTTP 1.0
        header("Pragma: no-cache");
        # Disable caching - Proxies
        header("Expires: 0");

        # Start the ouput
        $output = fopen("php://output", "w");

        # Then loop through the rows
        foreach ($data as $row) {
            # Add the rows to the body
            fputcsv($output, $row); // here you can change delimiter/enclosure
        }
        # Close the stream off
        fclose($output);
    }

}

$databaseConnection = DB::getInstance();

$clients = $databaseConnection->table('clients')->get()->toArray();

$data[0] = ['Last name', 'First name', 'Company', 'Email', 'Address', 'Tel 1', 'Tel 2'];

foreach ($clients as $client) {
    $data[] = [
        $client['lname'],
        $client['fname'],
        $client['cie'],
        $client['email'],
        $client['address'],
        $client['tel1'],
        $client['tel2'],
    ];
}

outputCSV($data, 'Client_' . time() . '.csv');

