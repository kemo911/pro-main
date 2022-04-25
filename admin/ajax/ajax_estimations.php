<?php
session_start();
include_once( dirname(dirname(__FILE__)) . '/../classes/check.class.php');
include_once __DIR__ . '/../classes/functions.php';
protect('*');

$input = filter_var_array( $_POST, FILTER_SANITIZE_STRING );
if (!$input) {
    $input = json_decode(file_get_contents('php://input'), true);
}
$ajaxEstimation = new AjaxEstimation();

switch ( $input['action'] )
{
    default:
        $ajaxEstimation->{$input['action']}($input);
        break;
}

/**
 * Class AjaxEstimation
 *
 * @property PDO $pdo
 * @property DB $db
 */
class AjaxEstimation {

    protected $db;
    protected $pdo;

    public function __construct()
    {
        $this->db = DB::getInstance();
        $this->pdo = $this->db->dbh;
    }

    public function createInvoice() {}

    public function createEstimation() {}

    public function createReclamation() {}

    public function loadAllReclamations($request)
    {
        $data = $this->db->query('SELECT DISTINCT r.*, c.fname, c.lname, c.address, c.tel1 FROM reclamation r INNER JOIN clients c ON c.clientid = r.client_id')->toArray();
        $this->response( $data );
    }

    public function getAppointmentPhotos($request)
    {
        $data = $this->db->query('select * from appointment_photo where token = ?', [ $request['data']['token'] ])->toArray();
        $this->response( $data );
    }

    public function deleteReclamationPhoto($request)
    {
        $this->response( $this->db->delete('appointment_photo', $request['data']['id']) );
    }

    public function uploadReclamationPhoto($request)
    {
        if ( !empty($_FILES) ) {
            $destination_folder = 'assets/uploads/';
            for ($i = 0; $i < count($_FILES['file']['tmp_name']); $i++) {
                $temp_file = $_FILES['file']['tmp_name'][$i];
                $image_size_data = getimagesize($temp_file);
                if( $image_size_data ) {
                    $file_name = explode(".",$_FILES["file"]["name"][$i]);
                    $extension = $file_name[1];
                    $new_name = $file_name[0].'_'.round(microtime(true) * 1000);
                    $new_file_name = $new_name .".".$extension;
                    $target_file =  __DIR__ .'/../../assets/uploads/'. $new_file_name;
                    $result = move_uploaded_file($temp_file,$target_file);
                    if ($result) {
                        $data = array(
                            'token' => $request['data']['token'],
                            'photo_url' => $destination_folder. $new_file_name,
                            'appointment_id' => 0,
                        );
                        addAppointmentPhoto($data);
                    }
                }
            }
        }

        $this->getAppointmentPhotos($request);
    }

    public function CheckAndFetchRecentlyCreatedReclamationBySession($request)
    {
        $session_id = session_id();
        if ( $session_id ) {
            $data = $this->db->query('SELECT r.*, c.fname, c.lname, c.address, c.tel1 FROM reclamation r
                LEFT JOIN clients c ON c.clientid = r.client_id
WHERE r.session_id = ? ORDER BY id DESC LIMIT 1', [ $session_id ])->toArray();

            if ( isset($data[0]) && !empty($data[0]) ) {
                $this->db->update('reclamation', [
                    'session_id' => 0,
                ], $data[0]['id']);
                $this->response($data[0]);
            }

            $this->response( $data );
        }

        $this->response([]);
    }

    public function createEstimateAndInvoice($request)
    {
        can('save_estimate');

        $this->pdo->beginTransaction();
        try {
            $estimation = $request['estimation'];
            $invoice    = $request['invoice'];
            $reclamation = $estimation['reclamation'];
            $client = getClient($reclamation['client_id']);
            //create invoice
            $invoiceData = [
                'client_id' => $reclamation['client_id'],
                'date' => date('Y-m-d', strtotime($estimation['time_of_loss'])),
                'reclamation' => $reclamation['reclamation'],
                'f_name' => $client['f_name'],
                'l_name' => $client['l_name'],
                'tel' => $client['tel'],
                'email' => $client['email'],
                'company' => $client['company'],
                'vin' => $reclamation['vin'],
                'brand' => $reclamation['brand'],
                'model' => $reclamation['model'],
                'year' => $reclamation['year'],
                'inventory' => $reclamation['inventory'],
                'color' => $reclamation['color'],
                'millage' => number_format(floatval($reclamation['millage']), 2),
                'rental_agreement' => $invoice['rental_agreement'],
                'rental_car' => $invoice['rental_car'],
                'number_of_days' => !empty($invoice['number_of_days']) ? $invoice['number_of_days'] : 0,
                'payment_status' => $invoice['payment_status'],
                'payment_method' => $invoice['payment_method'],
                'signature' => $invoice['signature'],
                'signature_img' => $invoice['signature_img'],
                'created_by' => $_SESSION['jigowatt']['user_id'],
                'invoice_type' => 'estimate'
            ];

            $invElements = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,'stripping','other_fees','glazier','work_force','parts','covid'];
            foreach ( $invElements as $invElement ) {
                $invoiceData['inv_' . $invElement . '_tech']    = $invoice['inv_' . $invElement . '_tech'];
                $invoiceData['inv_' . $invElement . '_note']    = $invoice['inv_' . $invElement . '_note'];
                $invoiceData['inv_' . $invElement . '_price']   = $invoice['inv_' . $invElement . '_price'];
            }
            $invoiceData['sub_total'] = $invoice['sub_total'];
            $invoiceData['tps'] = $invoice['tps'];
            $invoiceData['tvq'] = $invoice['tvq'];
            $invoiceData['franchise'] = $invoice['franchise'];
            $invoiceData['total'] = $invoice['total'];
            $invoiceData['deposit'] = $invoice['deposit'];
            $invoiceData['balance'] = $invoice['balance'];
            $invoiceData['damages'] = $invoice['damages'];
            $invoiceData['inv_parts_note'] = $invoice['inv_parts_note'];

            if ( !empty($invoice['parts']) && count($invoice['parts']) > 0 ) {
                //$invoiceData['inv_parts_note'] = count($invoice['parts']) . ' part' . (count($invoice['parts']) > 1 ? 's' : '');
            }

            if ( !empty($invoiceData['reclamation']) && !empty($invoiceData['client_id']) ) {
                $invoiceData['confirm_invoice'] = 1;
            }

            if (isset($invoice['dots'])) {
                $invoiceData['dots'] = json_encode(json_decode(html_entity_decode($invoice['dots']), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
            }

            if (isset($invoice['shared'])) {
                $sharedObject = json_decode(html_entity_decode($invoice['shared']), true);
                if (!empty($sharedObject['windShield']) && $sharedObject['windShield']['enabled']) {
                    $invoice['parts'][] = [
                        'name' => 'Pare brise', //$sharedObject['windShield']['label'],
                        'description' => $sharedObject['windShield']['desc'],
                        'hour' => $sharedObject['windShield']['hour'],
                        'price' => $sharedObject['windShield']['price'],
                        'damage_section' => 'Pare brise', //$sharedObject['windShield']['label'],
                    ];
                }
                $invoiceData['shared'] = json_encode($sharedObject);
            }

            $invoiceId = $this->db->insert('invoice', $invoiceData);

            if (!empty($reclamation['photos'])) {
                foreach ( $reclamation['photos'] as $photo ) {
                    $this->db->insert('invoice_photo', [
                        'invoice_id' => $invoiceId,
                        'photo_url'  => $photo['photo_url'],
                    ]);
                }
            }

            //add parts
            if ( !empty($invoice['parts']) ) {
                foreach ($invoice['parts'] as $part) {
                    $existingParts = $this->db->query('SELECT id FROM parts WHERE invoice_id = ? AND name = ? AND damage_section = ?', [ $invoiceId, $part['name'], $part['damage_section'] ])->toArray();
                    $partData = [
                        'invoice_id' => $invoiceId,
                        'name' => $part['name'],
                        'description' => $part['description'],
                        'hour' => $part['hour'],
                        'price' => $part['price'],
                        'ordered' => 0,
                        'received' => 0,
                        'damage_section' => $part['damage_section'] ?? null
                    ];
                    if (!empty($existingParts)) {
                        foreach ($existingParts as $existingPart) {
                            $partData['ordered'] = $existingPart['ordered'];
                            $partData['received'] = $existingPart['received'];
                            $partData['damage_section'] = $existingPart['damage_section'] ?? $partData['damage_section'];
                            $this->db->update('parts', $partData, $existingPart['id']);
                        }
                    } else {
                        $this->db->insert('parts', $partData);
                    }
                }
            }

            //create estimation
            $estimationId = $this->db->insert('estimations', [
                'reclamation_id' => $reclamation['id'],
                'invoice_id' => $invoiceId,
                'estimation_invoice_id' => $invoiceId,
                'time_of_loss' => date('Y-m-d', strtotime($estimation['time_of_loss'])),
                'franchise' => $estimation['franchise'],
                'created_by' => $_SESSION['jigowatt']['user_id'],
                'estimator_id' => $_SESSION['jigowatt']['user_id'],
                'address' => $estimation['address'],
                'tel' => $estimation['tel'],
                'type_of_loss' => $estimation['type_of_loss'],
                'fax' => $estimation['fax'],
                'client_id' => $reclamation['client_id'],
                'claim_collector' => $estimation['claim_collector'],
            ]);

            $this->saveReclamation([
                'inventory' => $reclamation['inventory'],
                'millage' => $reclamation['millage'],
                'brand' => $reclamation['brand'],
                'model' => $reclamation['model'],
                'year' => $reclamation['year'],
                'color' => $reclamation['color'],
                'vin' => $reclamation['vin'],
            ], $reclamation['id']);

            $this->pdo->commit();

            $this->response( ['message' => 'Successfully stored!', 'id' => $estimationId] );

        } catch (Exception $exception) {
            $this->pdo->rollBack();
            $this->response(['message' => $exception->getMessage(), 500]);
        }
    }

    public function updateEstimateAndInvoice($request)
    {
        $estimation = $request['estimation'];
        if ( ! $_SESSION['is_admin'] ) {
            if ($estimation['created_by'] != $_SESSION['jigowatt']['user_id']) {
                if (isAjaxRequest()) {
                    ajaxError('You do not have permission to save the estimate!');
                }
            }
        }

        $this->pdo->beginTransaction();
        try {
            $invoice    = $request['invoice'];
            $reclamation = $estimation['reclamation'];
            $client = getClient($reclamation['client_id']);
            //create invoice
            $invoiceData = [
                'client_id' => $reclamation['client_id'],
                'date' => date('Y-m-d', strtotime($estimation['time_of_loss'])),
                'reclamation' => $reclamation['reclamation'],
                'f_name' => $client['f_name'],
                'l_name' => $client['l_name'],
                'tel' => $client['tel'],
                'email' => $client['email'],
                'company' => $client['company'],
                'vin' => $reclamation['vin'],
                'brand' => $reclamation['brand'],
                'model' => $reclamation['model'],
                'year' => $reclamation['year'],
                'inventory' => $reclamation['inventory'],
                'color' => $reclamation['color'],
                'millage' => number_format(floatval($reclamation['millage']), 2),
                'rental_agreement' => $invoice['rental_agreement'],
                'rental_car' => $invoice['rental_car'],
                'number_of_days' => $invoice['number_of_days'] ?? 0,
                'payment_status' => $invoice['payment_status'],
                'payment_method' => $invoice['payment_method'],
                'created_by' => $_SESSION['jigowatt']['user_id'],
            ];

            if ( !empty($invoice['signature']) ) {
                $invoiceData['signature'] = $invoice['signature'];
            }
            if ( !empty($invoice['signature_img']) ) {
                $invoiceData['signature_img'] = $invoice['signature_img'];
            }

            $invElements = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,'stripping','other_fees','glazier','work_force','parts','covid'];
            foreach ( $invElements as $invElement ) {
                $invoiceData['inv_' . $invElement . '_tech']    = $invoice['inv_' . $invElement . '_tech'];
                $invoiceData['inv_' . $invElement . '_note']    = $invoice['inv_' . $invElement . '_note'];
                $invoiceData['inv_' . $invElement . '_price']   = $invoice['inv_' . $invElement . '_price'];
            }
            $invoiceData['sub_total'] = $invoice['sub_total'];
            $invoiceData['tps'] = $invoice['tps'];
            $invoiceData['tvq'] = $invoice['tvq'];
            $invoiceData['franchise'] = $invoice['franchise'];
            $invoiceData['total'] = $invoice['total'];
            $invoiceData['deposit'] = $invoice['deposit'];
            $invoiceData['balance'] = $invoice['balance'];
            $invoiceData['damages'] = $invoice['damages'];

            if ( !empty($invoice['parts']) && count($invoice['parts']) > 0 ) {
                //$invoiceData['inv_parts_note'] = count($invoice['parts']) . ' part' . (count($invoice['parts']) > 1 ? 's' : '');
            }

            $invoiceId = $invoice['id'];

            if (isset($invoice['dots'])) {
                $invoiceData['dots'] = json_encode(json_decode(html_entity_decode($invoice['dots']), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
            }

            if (isset($invoice['shared'])) {
                $sharedObject = json_decode(html_entity_decode($invoice['shared']), true);
                if (!empty($sharedObject['windShield']) && $sharedObject['windShield']['enabled']) {
                    $invoice['parts'][] = [
                        'name' => 'Pare brise', //$sharedObject['windShield']['label'],
                        'description' => $sharedObject['windShield']['desc'],
                        'hour' => $sharedObject['windShield']['hour'],
                        'price' => $sharedObject['windShield']['price'],
                        'damage_section' => 'Pare brise', //$sharedObject['windShield']['label'],
                    ];
                }
                $invoiceData['shared'] = json_encode($sharedObject);
            }

            $this->db->update('invoice', $invoiceData, $invoiceId);

//            foreach ( $reclamation['photos'] as $photo ) {
//                $this->db->insert('invoice_photo', [
//                    'invoice_id' => $invoiceId,
//                    'photo_url'  => $photo['photo_url'],
//                ]);
//            }

            //add parts
            if ( !empty($invoice['parts']) ) {

                foreach ($invoice['parts'] as $part) {
                    $existingParts = $this->db->query('SELECT id FROM parts WHERE invoice_id = ? AND name = ? AND damage_section = ?', [ $invoiceId, $part['name'], $part['damage_section'] ])->toArray();
                    $partData = [
                        'invoice_id' => $invoiceId,
                        'name' => $part['name'],
                        'description' => $part['description'],
                        'hour' => $part['hour'],
                        'price' => $part['price'],
                        'ordered' => 0,
                        'received' => 0,
                        'damage_section' => $part['damage_section'] ?? null
                    ];
                    if (!empty($existingParts)) {
                        foreach ($existingParts as $existingPart) {
                            $partData['ordered'] = $existingPart['ordered'];
                            $partData['received'] = $existingPart['received'];
                            $partData['damage_section'] = $existingPart['damage_section'] ?? $partData['damage_section'];
                            $this->db->update('parts', $partData, $existingPart['id']);
                        }
                    } else {
                        $this->db->insert('parts', $partData);
                    }
                }
            }

            //create estimation
            $this->db->update('estimations', [
                'reclamation_id' => $reclamation['id'],
                'time_of_loss' => date('Y-m-d', strtotime($estimation['time_of_loss'])),
                'franchise' => $estimation['franchise'],
                'created_by' => $_SESSION['jigowatt']['user_id'],
                'estimator_id' => $_SESSION['jigowatt']['user_id'],
                'address' => $estimation['address'],
                'tel' => $estimation['tel'],
                'type_of_loss' => $estimation['type_of_loss'],
                'fax' => $estimation['fax'],
                'client_id' => $reclamation['client_id'],
                'claim_collector' => $estimation['claim_collector'],
            ], $estimation['id']);

            $this->saveReclamation([
                'inventory' => $reclamation['inventory'],
                'millage' => $reclamation['millage'],
                'brand' => $reclamation['brand'],
                'model' => $reclamation['model'],
                'year' => $reclamation['year'],
                'color' => $reclamation['color'],
                'vin' => $reclamation['vin'],
            ], $reclamation['id']);

            $this->pdo->commit();

            $this->response( ['message' => 'Successfully stored!'] );

        } catch (Exception $exception) {
            $this->pdo->rollBack();
            $this->response(['message' => $exception->getMessage(), 500]);
        }
    }

    public function changeInvoiceType($request)
    {
        $this->pdo->beginTransaction();
        try {
            $i    = $request['invoice'];
            //$this->db->query('UPDATE invoice SET invoice_type = ? WHERE id = ?', ['invoice', $invoice['id']]);
            $dbInvoice = $this->db->query('SELECT * FROM invoice WHERE id = ?', [$i['id']])->toArray();
            if ( !empty($dbInvoice[0]) ) {

                $invoice = $dbInvoice[0];
                $invoiceId = $invoice['id'];
                unset($invoice['id']);
                $invoice['invoice_type'] = 'invoice';

                $newInvoiceId = $this->db->insert('invoice', $invoice);

                $this->db->query('UPDATE estimations SET estimation_invoice_id = ? WHERE invoice_id = ?', [$invoiceId, $invoiceId]);
                $this->db->query('UPDATE estimations SET invoice_id = ? WHERE estimation_invoice_id = ?', [$newInvoiceId, $invoiceId]);

                //copy parts to new invoice
                $allParts = $this->db->query('SELECT * FROM parts WHERE invoice_id = ?', [$invoiceId])->toArray();
                foreach ($allParts as $part) {
                    $part['invoice_id'] = $newInvoiceId;
                    unset($part['id']);
                    $this->db->insert('parts', $part);
                }

                //copy photos to new invoice
                $allPhotos = $this->db->query('SELECT * FROM invoice_photo where invoice_id = ?', [$invoiceId])->toArray();
                foreach ($allPhotos as $photo) {
                    $photo['invoice_id'] = $newInvoiceId;
                    unset($photo['id']);
                    $this->db->insert('invoice_photo', $photo);
                }

                $this->pdo->commit();

                $this->response( [ 'message' => $invoice ], 200 );
            }
            $this->response( [ 'message' => 'No invoice found' ], 404 );
        } catch (Exception $exception) {
            $this->pdo->rollBack();
            $this->response(['message' => $exception->getMessage(), 500]);
        }
    }

    public function loadContentById($request)
    {
        $id = (int) $request['id'];

        if ( is_numeric( $id ) ) {
            $estimation = $this->db->query('SELECT e.*, r.reclamation FROM estimations e INNER JOIN reclamation r ON r.id = e.reclamation_id WHERE e.id = ?', [$request['id']])->toArray();
            if ( !empty($estimation[0]) ) {
                $estimation = $estimation[0];
                $invoice = $this->db->query( 'SELECT * FROM invoice WHERE id = ? ORDER BY id DESC LIMIT 1', [ $estimation['estimation_invoice_id'] ] )->first();
                $reclamation = $this->db->query('SELECT r.*, c.fname, c.lname, c.address, c.tel1 FROM reclamation r
                  LEFT JOIN clients c ON c.clientid = r.client_id WHERE r.reclamation = ? LIMIT 1', [ $estimation['reclamation'] ])->first();
                $estimationPhotos = $this->db->query('SELECT * FROM appointment_photo WHERE token = ?', [ $reclamation->token ])->toArray();
                $parts = $this->db->query('SELECT * FROM parts WHERE invoice_id = ?', [$invoice->id])->toArray();
                if ( $invoice ) {
                    $invoice->parts = $parts;
                }

                $this->response([
                    'estimation' => $estimation,
                    'invoice'    => $invoice,
                    'reclamation' => $reclamation,
                    'reclamationPhotos' => $estimationPhotos
                ]);
            }
        }

        $this->response( [ 'message' => 'Please provide a valid estimation ID' ], 404 );
    }

    public function updatePageData($data)
    {
        $response = $this->db->update('invoice',
            [
                'page_data' => json_encode($data['data']['emailOptions'])
            ], $data['data']['id']);

        $this->response($response);
    }

    private function saveReclamation($reclamation, $reclamationId)
    {
        unset($reclamation['id']);
        $this->db->update('reclamation', $reclamation, $reclamationId);
    }

    private function response( $data, $code = 200 )
    {
        if ($code == 500) {
            header("HTTP/1.0 500 Internal Server Error");
        } else {
            http_response_code($code);
        }
        echo json_encode( $data );
        exit;
    }
}
