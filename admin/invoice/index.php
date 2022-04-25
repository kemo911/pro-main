<?php
include_once(dirname(dirname(dirname(__FILE__))) . '/classes/check.class.php');
include_once(dirname(dirname(dirname(__FILE__))) . '/admin/classes/functions.php');

$invoice_id = @$_GET['invoice_id'];

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
$reclamation = (array) $reclamation;

$title = ( isset($_GET['title']) ) ? $_GET['title'] : 'ESTIMATION';

if ( ! ( isAdmin() ) ) {
    if (isEstimator() || isTech()) {
        if ( userId() != $invoice['created_by'] || $estimation['created_by'] != userId()) {
            die('You do not have permission to this ' . $title);
        }
    }
}

$label = $invoice_id;
$logo = '/admin/assets/img/logo.jpg';
$height = '60px';
if ( $invoice['invoice_type'] == 'invoice' ) {
    //$logo = '/admin/invoice/logo.png';
    $title = 'FACTURE';
} else {
    $label = $invoice['reclamation'];
}

if ( isset($_GET['email']) && $_GET['email'] == 'y' ) {

    if ( filter_var($invoice['email'], FILTER_VALIDATE_EMAIL) ) {

	    $to = 'faisal.islam70@gmail.com';
	    $subject = 'Your invoice# ES' . $label . ' from ' . $_SERVER['HTTP_HOST'];
	    $from = 'no-reply@bossesg.com';

// To send HTML mail, the Content-type header must be set
	    $headers  = 'MIME-Version: 1.0' . "\r\n";
	    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

// Create email headers
	    $headers .= 'From: '.$from."\r\n".
	                'Reply-To: '.$from."\r\n" .
	                'X-Mailer: PHP/' . phpversion();
	    $message = file_get_contents( 'http://' . $_SERVER['HTTP_HOST'] . '/admin/invoice/index.php?bps=nscreed&invoice_id=' . $invoice_id);

// Sending email
	    if(mail($to, $subject, $message, $headers)){
		    echo 'Your mail has been sent successfully to ' . $invoice['email'];
	    } else{
		    echo 'Unable to send email. Please try again.';
	    }


    } else {
        echo '<h2>Client does not have a valid email address <b>'. $invoice['email'] .'</b>.</h2>';
    }

    die;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
	
	<title><?php echo $title; ?></title>
	
	<link rel='stylesheet' type='text/css' href='css/style.css' />
	<link rel='stylesheet' type='text/css' href='css/print.css' media="print" />

</head>

<body>

	<div id="page-wrap">

		<div id="header"><?php echo $title; ?></div>
		
		<div id="identity">
		
            <div style="margin-top: 10px;" id="address">
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
                    <td class="meta-head"><?php echo $title; ?> #</td>
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
                <?php if ($estimation['time_of_loss']): ?>
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

	<?php if ( isset($_GET['print']) && $_GET['print'] == 'y' ): ?>

		<script>
			window.setTimeout(function () {
				window.print();
			}, 500);
		</script>

	<?php endif; ?>

</body>

</html>