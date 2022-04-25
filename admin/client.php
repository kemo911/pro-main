<?php

include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
include_once( dirname(dirname(__FILE__)) . '/admin/classes/functions.php');
if(!empty($_GET['client_id'])){
    $db = DB::getInstance();
    $invoices = getInvoicesByClientId($_GET['client_id']);
    $estimations = $db->query('SELECT * FROM mold WHERE client_id = ?', [ $_GET['client_id'] ])->toArray();
}
init_message_bags();
$client = array();
if(empty($_POST))
    include_once('header.php');

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

    $_POST = filter_var_array( $_POST, FILTER_SANITIZE_STRING );
    $_SESSION['post.values'] = $_POST;

    if ( !empty( $_POST['action'] ) ) {
        switch ( $_POST['action'] ) {
            case 'edit_client':
                flush_errors();
                $error_array = array();
                $params = array();

                $client_id = null;
                if ( !empty($_POST['client_id']) ) {
                    $client_id = $_POST['client_id'];
                    $params[':clientid'] = $client_id;
                }


                $fields = array(
                    'fname'     => 'required',
                    'lname'     => 'required',
                    'address'   => '',
                    'cie'       => '',
                    'email'     => 'required',
                    'tel1'      => '',
                    'tel2'      => '',
                    'note'      => '',
                );


                if (!empty($_POST['email'])) {
                    if ( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
                        $error_array['email'] = ucfirst('email') . ' field must be a valid email address.';
                    }
                }


                foreach ( $fields as $field => $validation_rules ) {
                    $params[':' . $field] = !empty($_POST[$field]) ? trim($_POST[$field]) : '';
                    switch ( $validation_rules ) {
                        case 'required':
                            $field_value = !empty($_POST[$field]) ? trim($_POST[$field]) : null;
                            if ( !strlen($field_value) ) {
                                if ( 'fname' == $field ) {
                                    $error_array[$field] = 'Prénom field is required.';
                                } else if ( 'lname' == $field ) {
                                    $error_array[$field] = 'Nom field is required.';
                                } else if ( 'email' == $field ) {
                                    $error_array[$field] = 'Courriel field is required.';
                                }
                                else {
                                    $error_array[$field] = ucfirst($field) . ' field is required.';
                                }
                            } else {
                                $params[':' . $field] = $_POST[$field];
                            }
                            break;
                    }
                }
                put_errors($error_array);

                if ( empty($error_array) ) {
                    //save data

                    global $generic;
                    if ( $client_id ) {
                        $generic->query('UPDATE clients SET cie = :cie, address = :address, fname = :fname, lname = :lname, email = :email, tel1 = :tel1, tel2 = :tel2, note = :note WHERE clientid = :clientid', $params);
                    } else {
                         $generic->query('INSERT INTO clients SET cie = :cie, address = :address, fname = :fname, lname = :lname, email = :email, tel1 = :tel1, tel2 = :tel2, note = :note', $params);
                     }

                    if ( !empty($_POST['redirect_to']) ) {
	                    header('Location: ' . $_POST['redirect_to'] . '?client_id=' . $generic->getLastInsertId()); exit;
                    }

                    put_flash_message('message', 'Success! Client has been '. ($client_id ? 'updated' : 'created' ) .' successfully.');
                    flash_post_values();
                    $redirect_to = !empty($_POST['redirect_to']) ? '?redirect_to=' . $_POST['redirect_to'] : '';
                    header('Location: /admin/clients.php' . $redirect_to);
                    exit();
                }

                break;
        }
    }
    empty($_POST['client_id']) ? header('Location: ' . $_SERVER['REQUEST_URI']) : header('Location: ' . $_SERVER['REQUEST_URI'] . '?client_id=' . $_POST['client_id']);
    exit();
} else {
    //flush_errors();
    $client = get_post_values();
    if ( isset($_GET['client_id']) && is_numeric($_GET['client_id']) ) {
        global $generic;
        $stmt = $generic->query('SELECT * FROM clients WHERE clientid = :clientId LIMIT 1', array(':clientId' => $_GET['client_id']));
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( !$client )
            $client = array();
    }
}
$message = flush_message('message');
?>

    <div id="message">
        <div class="alert <?php echo !empty($message) ? 'alert-info' : ''; ?>">
            <?php echo $message; ?>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <form method="post" action="/admin/client.php">
            <input type="hidden" name="redirect_to" value="<?php echo !empty($_GET['redirect_to']) ? $_GET['redirect_to'] : ''; ?>">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h2 class="text-center text-uppercase text-"><strong><?php echo !empty($client['clientid']) ? 'Changer ' : 'Créer '; ?> profile du client</strong></h2>
                    </div>
                    <div class="panel-body">
                        <?php echo admin_render_partials('client_edit', ['client' => $client]); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h2 class="text-center text-uppercase text-"><strong>Estimation</strong></h2>
                    </div>
                    <div class="panel-body">
                        <?php if( !empty($estimations) ): ?>
                            <div class="table-responsive">
                                <table id="parts_report_dt" class="table table-striped table-bordered dt-responsive"
                                       cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>#réclamation</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($estimations as $estimate): ?>
                                    <tr>
                                        <td><?php echo $estimate['reclamation']; ?></td>
                                        <td><?php echo date('m/d/Y', strtotime($estimate['date'])); ?></td>
                                        <td><a href="/admin/mold.php?id=<?php echo $estimate['id'] ?>"><i class="glyphicon glyphicon-edit"></i></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                        <h2 class="text-center text-danger">Rien de disponible</h2>
                        <?php endif;?>
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h2 class="text-center text-uppercase text-"><strong>Factures</strong></h2>
                    </div>
                    <div class="panel-body">
                        <?php if( !empty($invoices) ): ?>
                            <div class="table-responsive">
                                <table id="parts_report_dt" class="table table-striped table-bordered dt-responsive"
                                       cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th>#réclamation</th>
                                        <th>Total</th>
                                        <th>Balance</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($invoices as $invoice): ?>
                                        <tr>
                                            <td><?php echo $invoice['reclamation']; ?></td>
                                            <td><span class="label label-danger"><?php echo amount_format($invoice['total']); ?></span></td>
                                            <td><span class="label label-success"><?php echo amount_format($invoice['balance']); ?></span></td>
                                            <td><?php echo date('m/d/Y', strtotime($invoice['date'])); ?></td>
                                            <td><a href="/admin/main.php?invoice_id=<?php echo $invoice['id'] ?>"><i class="glyphicon glyphicon-edit"></i></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <h2 class="text-center text-danger">Rien de disponible</h2>
                        <?php endif;?>
                    </div>
                </div>

            </div>
            <div class="col-md-2 col-md-offset-5">
                 <?php if (protectThis( implode(',', [Permission::USER_LEVEL_1, Permission::USER_LEVEL_3]) )) : ?>
                <button type="submit" class="btn btn-danger btn-block">Sauvegarder</button>
                                <?php endif; ?>

            </div>
            </form>
        </div>
    </div>

<?php

include_once('footer.php');
