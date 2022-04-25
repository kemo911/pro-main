<?php
include_once(dirname(dirname(dirname(__FILE__))) . '/classes/check.class.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/admin/classes/functions.php');
include_once (dirname(dirname(dirname(__FILE__))) . '/admin/classes/send_email.class.php');

if ( empty($_GET['token']) ) {
    die('No Invoice');
}

$log  = "User";

file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);;

if ( $_GET['token'] == 'creedDefaultToken' ) {
    $invoice_id = @$_GET['invoice_id'];
} else {
    $token = base64_decode($_GET['token']);
    $invoice_id = str_replace('creedToken::', '', $token);
    $invoice_id = str_replace('::frToken', '', $invoice_id);
    $invoice_id = $invoice_id / 333;
}

//$invoice_id = @$_GET['invoice_id'];

if ( !$invoice_id ) {
    die('No Invoice');
}

$db = DB::getInstance();

$invoice = $db->query('SELECT * FROM invoice WHERE id = ?', [$invoice_id])->toArray();
$estimation = $db->query('SELECT * FROM estimations where invoice_id = ? ORDER BY id desc LIMIT 1', [$invoice_id])->toArray();

if ( empty($invoice) )
    die('No Invoice');
$invoice = $invoice[0];
$estimation = isset($estimation[0]) ? $estimation[0] : [];
$reclamation = $db->query('SELECT r.*, c.fname, c.lname, c.address, c.tel1 FROM reclamation r
                  LEFT JOIN clients c ON c.clientid = r.client_id WHERE r.reclamation = ? LIMIT 1', [ $invoice['reclamation'] ])->first();


$photos = [];
$pageData = json_decode($invoice['page_data'], true) ?? ['email_to' => '', 'email_images' => false];
if ($pageData['email_images']) {
    $photos = $db->query('SELECT * FROM appointment_photo WHERE token = ?', [ $reclamation->token ])->toArray();
    if (count($photos) > 0){
        $zip = new ZipArchive();
        $zip->open("photos_$invoice_id.zip",ZipArchive::CREATE );
        foreach ($photos as $photo){
            if(@file_get_contents(dirname(dirname(dirname(__FILE__))).'/'.$photo['photo_url']) !== false){
                $file = file_get_contents(dirname(dirname(dirname(__FILE__))).'/'.$photo['photo_url']);
                $file_name =  pathinfo ( dirname(dirname(dirname(__FILE__))).'/'.$photo['photo_url'], PATHINFO_BASENAME);
                $zip->addFromString(pathinfo ( $file_name, PATHINFO_BASENAME), $file);        }
            }
    }
}


$reclamation = (array) $reclamation;

$title = ( isset($_GET['title']) ) ? $_GET['title'] : 'ESTIMATION';

$label = $invoice_id;
$logo = '/admin/assets/img/logo.jpg';
$height = '75px';
if ( $invoice['invoice_type'] == 'invoice') {
    //$logo = '/admin/invoice/logo.png';
    $height = '60px';
    $title = 'FACTURE';
}  else {
    $label = $invoice['reclamation'];
}


if ( isset($_GET['email']) && $_GET['email'] == 'y' ) {

    if ( filter_var($invoice['email'], FILTER_VALIDATE_EMAIL) ) {

        $to = $invoice['email'];
        $subject = 'Your invoice# ES' . $label . ' from ' . $_SERVER['HTTP_HOST'];
        $from = 'no-reply@bossesg.com';


        //$title = isset($_GET['title']) ? $_GET['title'] : null;

        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );


        $generic = new Send_email();

        $message = file_get_contents( 'http://' . $_SERVER['HTTP_HOST'] . '/admin/invoice/index-email.php?bps=nscreed&token=creedDefaultToken&invoice_id=' . $invoice_id . ($title ? '&title=' . $title : ''),false,stream_context_create($arrContextOptions));

        $invoicePublicURL = 'http://' . $_SERVER['HTTP_HOST'] . '/admin/invoice/index-email.php?token=' . base64_encode('creedToken::' . $invoice_id * 333 .'::frToken');

        $message = str_ireplace('{{+PUBLIC_URL+}}', $invoicePublicURL, $message);


        $files = [];
        if (isset($zip)){
            //echo json_encode($zip->getFromName());

            $file = $zip->filename;
            $files[] = $_SERVER['DOCUMENT_ROOT']."/admin/invoice/photos_$invoice_id.zip";
            $zip->close();
        }

        // Sending email
        $emailAddress = [$to];; //@TODO : add info main email and $to
        if (filter_var($pageData['email_to'], FILTER_VALIDATE_EMAIL)) {
            $emailAddress[] = $pageData['email_to'];
        }

        $generic->sendEmail($emailAddress, $subject, $message,null,null,$files,true);
    } else {
        echo '<h2>Client does not have a valid email address <b>'. $invoice['email'] .'</b>.</h2>';
    }

    //die;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />

    <title><?php echo $title; ?></title>

    <style>
        /*
	 CSS-Tricks Example
	 by Chris Coyier
	 http://css-tricks.com
*/
        @import url('https://fonts.googleapis.com/css?family=Cousine');
        * { margin: 0; padding: 0; }
        body {
            font-family: 'Cousine', monospace;
            font-size: 14px;
        }
        #page-wrap { width: 800px; margin: 0 auto; }

        textarea { border: 0; font: 14px Georgia, Serif; overflow: hidden; resize: none; }
        table { border-collapse: collapse; }
        table td, table th { border: 1px solid black; padding: 2px; }

        #header { height: 15px; width: 100%; margin: 10px 0; background: #222; text-align: center; color: white; font: bold 15px Helvetica, Sans-Serif; text-decoration: uppercase; letter-spacing: 20px; padding: 8px 0px; }

        #address { width: 420px; float: left; }
        #customer { overflow: hidden; }

        #logo { text-align: right; float: right; position: relative; margin-top: 25px; max-width: 150px; max-height: 150px; overflow: hidden; }
        /*#logo:hover, #logo.edit { border: 1px solid #000; margin-top: 0px; max-height: 125px; }*/
        #logoctr { display: none; }
        /*#logo:hover #logoctr, #logo.edit #logoctr { display: block; text-align: right; line-height: 25px; background: #eee; padding: 0 5px; }*/
        #logohelp { text-align: left; display: none; font-style: italic; padding: 10px 5px;}
        #logohelp input { margin-bottom: 5px; }
        .edit #logohelp { display: block; }
        .edit #save-logo, .edit #cancel-logo { display: inline; }
        .edit #image, #save-logo, #cancel-logo, .edit #change-logo, .edit #delete-logo { display: none; }
        #customer-title { font-size: 16px; font-weight: 500; float: left; }

        #meta { margin-top: 1px; width: 300px; float: right; }
        #meta td { text-align: right;  }
        #meta td.meta-head { text-align: left; background: #eee; }
        #meta td textarea { width: 100%; height: 20px; text-align: right; }

        #items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
        #items th { background: #eee; }
        #items textarea { width: 80px; height: 50px; }
        #items tr.item-row td { border: 0; vertical-align: top; }
        #items td.description { width: 300px; }
        #items td.item-name { width: 450px; }
        #items td.description textarea, #items td.item-name textarea { width: 100%; }
        #items td.total-line { border-right: 1px solid #444444; text-align: right; background: #e4e4e4 !important; }
        #items td.total-value { border-left: 0; padding: 2px; }
        #items td.total-value textarea { height: 20px; background: none; }
        #items td.balance { background: #eee; }
        #items td.blank { border: 0; }

        #items .item-row td:last-child { text-align: right; width: 60px; }

        #terms { text-align: center; margin: 20px 0 0 0; }
        #terms h5 { text-transform: uppercase; font: 13px Helvetica, Sans-Serif; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }
        #terms textarea { width: 100%; text-align: center;}

        textarea:hover, textarea:focus, #items td.total-value textarea:hover, #items td.total-value textarea:focus, .delete:hover { background-color:#EEFF88; }

        .delete-wpr { position: relative; }
        .delete { display: block; color: #000; text-decoration: none; position: absolute; background: #EEEEEE; font-weight: bold; padding: 0px 3px; border: 1px solid; top: -6px; left: -22px; font-family: Verdana; font-size: 12px; }

        #address { width: 450px; }
        #address .address-details {  margin-right: 10px; display: inline-block; width: 400px !important; }

        .address-signature {
            /*width: 450px;*/
            margin-top: 20px;
            /*float: left;*/
            font-size: 11px;
        }
        .address-signature img {
            height: 50px;
        }
        .address-signature h3 {
            font-size: 10px;
            border-top: 1px dashed #666;
            padding-top: 5px;
        }
        .half-w { width: 45%; float: left; }
        .half-w+.half-w { float: right; }
        .car-info {
            margin-top: 15px;
            padding-top: 10px;
        }
    </style>

</head>

<body>

<?php if ( isset($_GET['bps']) && $_GET['bps'] == 'nscreed' ): ?>
    <h4 style="text-align: center; margin-top: 20px;">
        See your invoice in the <b><a href="{{+PUBLIC_URL+}}"><?php echo $_SERVER['HTTP_HOST']; ?></a></b> website.
    </h4>
<?php endif; ?>

<div id="page-wrap">

    <div id="header"><?php echo $title; ?></div>

    <div id="identity">

        <div style="margin-top: 20px;" id="address">
            <div class="address-details">
                <strong>Nom:</strong> <?php echo $invoice['f_name'] . ' ' . $invoice['l_name']; ?><br>
                <strong>Compagnie:</strong> <?php echo $invoice['company']; ?><br>
                <strong>Courriel:</strong> <?php echo $invoice['email']; ?><br>
                <strong>Tel:</strong> <?php echo $invoice['tel']; ?> <br>
            </div>
        </div>

        <div id="logo3">
            <?php if ($logo): ?>
                <img id="image" style="height: <?php echo $height; ?>; float: right;" src="<?php echo $logo; ?>" alt="logo" />
                <div style="float: right;padding-top: 4px; text-align: right;">
                    1-844-482-2224 <br>
                    info@eco-solutiongrele.com
                </div>
            <?php else: ?>
                <div style="text-align: right;">
                    <strong style="text-transform: uppercase; font-size: large;">PROMUTUEL ASSURANCE</strong><br>
                    <span>
                    2000, boulevard Lebourgneuf, 4e étage<br>
                    Québec (Québec) G2K 0B6<br>
                    promutuelassurance.ca
                </span>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <div style="clear:both"></div>

    <div id="customer">

        <div id="customer-title">
<br>
            <?php if ( $invoice['invoice_type'] == 'invoice' ): ?>
                <span style="color: #ff8f73; font-weight: 600; text-transform: uppercase; line-height: 2;">Eco Solution Grêle</span>
                <br><br>
                <span style="font-size: 14px; color: #2f3640;">
                    1441 boul. des Laurentides <br>
                    Laval, QC H7N 4Y5 <br>
                    info@eco-solutiongrele.com <br>
                    eco-solutiongrele.com
                </span>
            <?php endif; ?>

        </div>

        <table id="meta">
            <tr>
                <td class="meta-head">Facture #</td>
                <td><textarea>ES<?php echo $label; ?></textarea></td>
            </tr>
            <tr>
                <td class="meta-head">Date</td>
                <td><textarea id="date"><?php echo date('F j, Y', strtotime($invoice['created_at'])); ?></textarea></td>
            </tr>
            <tr>
                <td class="meta-head">Réclamation #</td>
                <td><textarea id="date"><?php echo $invoice['reclamation']; ?></textarea></td>
            </tr>
            <tr>
                <td class="meta-head">Status</td>
                <td style="color: <?php echo $invoice['payment_status'] == 1 ? 'green' : 'red'; ?>;"><?php echo $invoice['payment_status'] == 1 ? 'PAID' : 'UNPAID'; ?></td>
            </tr>
            <tr>
                <td class="meta-head">Paiement</td>
                <td><?php echo $invoice['payment_method']; ?></td>
            </tr>
            <?php if (array_key_exists('time_of_loss',$estimation)): ?>
                <tr>
                    <td class="meta-head">Date du sinistre</td>
                    <td><textarea id="date"><?php echo date('F j, Y', strtotime($estimation['time_of_loss'])); ?></textarea></td>
                </tr>
            <?php endif; ?>
        </table>

    </div>

    <?php include __DIR__ . '/common/reclamation-info.php'; ?>

    <div class="car-info" style="margin-top: 8px">
        <strong>Contrat de location:</strong> <?php echo $invoice['rental_agreement'] ?? '✕'; ?>
        <strong>Jours:</strong> <?php echo $invoice['number_of_days'] > 0 ? $invoice['number_of_days'] : '✕'; ?>
        <strong>Véhicule de location:</strong> <?php echo $invoice['rental_car'] ? '✔' : '✕' ; ?>
    </div>

    <table id="items">

        <tr>
            <th>Items</th>
            <th>Débosselage sans peinture</th>
            <th>Prix</th>
        </tr>

        <?php
        $items = array(
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
            'other_fees' => 'Autres',
            'glazier' => 'Vitrier',
            'work_force' => 'Main d`oeuvre',
            'parts' => 'Pièces',
            'covid' => 'COVID',
        );
        ?>

        <?php foreach ( $items as $key => $item ):

            //if ( $invoice['inv_' . $key . '_price'] <= 0 ) continue;

            ?>
            <tr class="item-row">
                <td>
                    <?php echo $item; ?>
                </td>
                <td class="item-name">
                    <div class="delete-wpr"><?php echo $invoice['inv_' . $key . '_note']; ?></div>
                </td>
                <td>
                    <span class="price">$<?php echo $invoice['inv_' . $key . '_price']; ?></span>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr class="item-row">
            <td colspan="3"></td>
        </tr>

        <tr>
            <td colspan="2" class="total-line">Sous-total</td>
            <td class="total-value">
                <div id="subtotal">$<?php echo $invoice['sub_total']; ?></div>
            </td>
        </tr>
        <tr>
            <!--		      <td class="blank"> </td>-->
            <td  colspan="2" class="total-line">142389147 TPS(5%)</td>
            <td class="total-value">
                <div id="subtotal">$<?php echo $invoice['tps']; ?></div>
            </td>
        </tr>
        <tr>
            <!--		      <td class="blank"> </td>-->
            <td colspan="2"  class="total-line">1021225025 TVQ(9.975%)</td>
            <td class="total-value">
                <div id="subtotal">$<?php echo $invoice['tvq']; ?></div>
            </td>
        </tr>
        <tr>
            <!--		      <td class="blank"> </td>-->
            <td  colspan="2" class="total-line">Franchise</td>
            <td class="total-value">
                <div id="subtotal">$<?php echo $invoice['franchise']; ?></div>
            </td>
        </tr>

        <tr>
            <!--		      <td class="blank"> </td>-->
            <td  colspan="2" class="total-line">Total</td>
            <td class="total-value">
                <div id="subtotal">$<?php echo $invoice['total']; ?></div>
            </td>
        </tr>

        <tr>
            <!--		      <td class="blank"> </td>-->
            <td colspan="2"  class="total-line">Dépôt</td>
            <td class="total-value">
                <div id="subtotal">$<?php echo $invoice['deposit']; ?></div>
            </td>
        </tr>

        <tr>
            <!--		      <td class="blank"> </td>-->
            <td  colspan="2" class="total-line">Balance</td>
            <td class="total-value">
                <div id="subtotal">$<?php echo $invoice['balance']; ?></div>
            </td>
        </tr>

    </table>
    		<div id="terms">
    		  <p><?php echo $invoice['damages']; ?></p>
    		</div>

    <div class="address-signature">
        <div class="half-w">
            <p>Je soussigné(e), assuré(e) ou représentant de l’assuré(e), déclare avoir pris connaissance des réparations de débosselage sans peinture exécuter sur mon véhicule par Eco Solution Grêle, et je m’en déclare entièrement satisfait(e).</p>
        </div>
        <div class="half-w">
            <img src="<?php echo $invoice['signature_img']; ?>" alt="Signature of <?php echo $invoice['f_name'] . ' ' . $invoice['l_name']; ?>">
            <h3>Signature du client</h3>
        </div>
    </div>

</div>

</body>

</html>