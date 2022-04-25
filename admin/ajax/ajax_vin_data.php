<?php

if (isset($_POST['action'])) {
    $action  =  $_POST['action'];
    switch ( $action ) {

        case 'vin':

            $vinNumber = !empty( $_POST['vin_number'] ) ? $_POST['vin_number'] : null;

            if ( !$vinNumber ) {
                echo json_encode(array(
                    'status' => 0
                ));
                exit();
            }
            $url = 'https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinValuesExtended/'. $vinNumber .'?format=json';
            $content = file_get_contents($url);
            $result = json_decode( $content, true );
            $response = $result['Results'][0];

            $particular_area = array();
            if(!empty($response['PlantCity'])){
                $particular_area[] = $response['PlantCity'];
            }
            if(!empty($response['PlantState'])){
                $particular_area[] = $response['PlantState'];
            }
            if(!empty($response['PlantCountry'])){
                $particular_area[] = $response['PlantCountry'];
            }

            $vin = array(
                'brand' => $response['Make'],
                'model' => $response['Model'],
                'year' => $response['ModelYear'],
                'inventory' => '',
                'sn' => $response['Series'],
                'color' => '',
                'pa' => !empty($particular_area) ? implode(', ', $particular_area) : '',
                'bt' => $response['BrakeSystemType'],
            );
            echo json_encode(array(
                'status' => 1,
                'results' => $vin
            ));exit;
            break;

    }
}
