<?php

include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';

protect( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3]));



if ( !empty($_GET['agenda_type']) ) {

    $date = $_GET['date'];

    if ( $_GET['shift'] == 1 ) {
        switch ( $_GET['agenda_type'] ) {
            case 'available_schedule':

                $db = DB::getInstance();
                $query = "SELECT sc.*, u.name FROM schedule sc LEFT JOIN login_users u ON u.user_id = sc.user_id WHERE sc.date = ? AND sc.start_time >= '07:00:00' AND sc.end_time <= '12:00:00'";
                $results = $db->query($query, [$date])->toArray();
                if( !empty($results) ) {
                    $schedules = array();
                    foreach ( $results as $result ) {
                        $schedules[] = array(
                            "start" => $result['start_time'],
                            "end" => $result['end_time'],
                            "className" => "available-schedule a__" . $result['id'],
                            "rendering" => "background",
                            "title" => $result['name'],
                        );
                    }
                }
                echo json_encode( $schedules );
                break;

            case 'estimate_schedule':
                $db = DB::getInstance();
                $query = "SELECT ap.*, ts.start_time, ts.end_time, ad.reclamation, CONCAT(c.fname, ' ', c.lname) as clientname FROM appointment AS ap
                    LEFT JOIN appointment_time_slots AS ts ON ap.appointment_slot_id = ts.id
                    LEFT JOIN appointment_details AS ad ON ad.appointment_id = ap.id
                    LEFT JOIN clients c ON c.clientid = ad.client_id
                    WHERE date = ? AND start_time >= '07:00:00' AND end_time <= '12:00:00' AND ap.type = 'estimation'";
                $results = $db->query($query, [$date])->toArray();

                if( !empty($results) ) {
                    $schedules = array();
                    foreach ( $results as $result ) {
                        $schedules[] = array(
                            "title" => !empty($result['reclamation']) ? $result['reclamation'] : $result['clientname'],
                            "start" => $result['date'] . 'T'. $result['start_time'],
                            "end" => $result['date'] . 'T'. $result['end_time'],
                            "className" => "estimate-schedule s__" . $result['schedule_id'] . " ap__" . $result['id'],
                        );
                    }
                }
                echo json_encode( $schedules );
                break;

            case 'repair_schedule':

                $db = DB::getInstance();
                $query = "SELECT ap.*, ts.start_time, ts.end_time, ad.reclamation, CONCAT(c.fname, ' ', c.lname) as clientname
                    FROM appointment AS ap
                    LEFT JOIN appointment_time_slots AS ts ON ap.appointment_slot_id = ts.id
                    LEFT JOIN appointment_details AS ad ON ad.appointment_id = ap.id
                    LEFT JOIN clients c ON c.clientid = ad.client_id
                    WHERE date = ? AND start_time >= '07:00:00' AND end_time <= '12:00:00' AND ap.type = 'repair'";
                $results = $db->query($query, [$date])->toArray();

                if( !empty($results) ) {
                    $schedules = array();
                    foreach ( $results as $result ) {
                        $schedules[] = array(
                            "title" => !empty($result['reclamation']) ? $result['reclamation'] : $result['clientname'],
                            "start" => $result['date'] . 'T'. $result['start_time'],
                            "end" => $result['date'] . 'T'. $result['end_time'],
                            "className" => "repair-schedule s__" . $result['schedule_id'] . " ap__" . $result['id'],
                        );
                    }
                }

                echo json_encode( $schedules );
                break;
        }


    } else if ( $_GET['shift'] == 2 ) {

        switch ( $_GET['agenda_type'] ) {

            case 'available_schedule':
                $db = DB::getInstance();
                $query = "SELECT sc.*, u.name FROM schedule sc LEFT JOIN login_users u ON u.user_id = sc.user_id WHERE sc.date = ? AND sc.start_time >= '13:00:00' AND sc.end_time <= '18:00:00'";
                $results = $db->query($query, [$date])->toArray();
                if( !empty($results) ) {
                    $schedules = array();
                    foreach ( $results as $result ) {
                        $schedules[] = array(
                            "start" => $result['start_time'],
                            "end" => $result['end_time'],
                            "className" => "available-schedule a__" . $result['id'],
                            "rendering" => "background",
                            "title" => $result['name'],
                        );
                    }
                }
                echo json_encode( $schedules );
                break;

            case 'estimate_schedule':
                $db = DB::getInstance();
                $query = "SELECT ap.*, ts.start_time, ts.end_time, ad.reclamation, CONCAT(c.fname, ' ', c.lname) as clientname FROM appointment AS ap
                    LEFT JOIN appointment_time_slots AS ts ON ap.appointment_slot_id = ts.id
                    LEFT JOIN appointment_details AS ad ON ad.appointment_id = ap.id
                    LEFT JOIN clients c ON c.clientid = ad.client_id
                    WHERE date = ? AND start_time >= '13:00:00' AND end_time <= '18:00:00' AND ap.type = 'estimation'";
                $results = $db->query($query, [$date])->toArray();

                if( !empty($results) ) {
                    $schedules = array();
                    foreach ( $results as $result ) {
                        $schedules[] = array(
                            "title" => !empty($result['reclamation']) ? $result['reclamation'] : $result['clientname'],
                            "start" => $result['date'] . 'T'. $result['start_time'],
                            "end" => $result['date'] . 'T'. $result['end_time'],
                            "className" => "estimate-schedule s__" . $result['schedule_id'] . " ap__" . $result['id'],
                        );
                    }
                }
                echo json_encode( $schedules );
                break;

            case 'repair_schedule':

                $db = DB::getInstance();
                $query = "SELECT ap.*, ts.start_time, ts.end_time, ad.reclamation, CONCAT(c.fname, ' ', c.lname) as clientname FROM appointment AS ap
                    LEFT JOIN appointment_time_slots AS ts ON ap.appointment_slot_id = ts.id
                    LEFT JOIN appointment_details AS ad ON ad.appointment_id = ap.id
                    LEFT JOIN clients c ON c.clientid = ad.client_id
                    WHERE date = ? AND start_time >= '13:00:00' AND end_time <= '18:00:00' AND ap.type = 'repair'";
                $results = $db->query($query, [$date])->toArray();

                if( !empty($results) ) {
                    $schedules = array();
                    foreach ( $results as $result ) {
                        $schedules[] = array(
                            "title" => !empty($result['reclamation']) ? $result['reclamation'] : $result['clientname'],
                            "start" => $result['date'] . 'T'. $result['start_time'],
                            "end" => $result['date'] . 'T'. $result['end_time'],
                            "className" => "repair-schedule s__" . $result['schedule_id'] . " ap__" . $result['id'],
                        );
                    }
                }

                echo json_encode( $schedules );
                break;

        }
    }
}