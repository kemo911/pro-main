<?php



include_once(dirname(dirname(__FILE__)) . '/classes/check.class.php');

include_once(dirname(dirname(__FILE__)) . '/admin/classes/functions.php');

function putValue($value) {

	if ( !empty($value) ) {

		echo $value;

	}

}


$db = DB::getInstance();



$appointment_type = 'estimation';

$limit = 25;

$offset = 0;



$bind = [$appointment_type];



$additional_where = '';



$page = !empty($_GET['page']) ? intval($_GET['page']) : 1;

if ( ! $page ) $page = 1;



$start_date = !empty($_GET['start_date']) ? date('Y-m-d', strtotime($_GET['start_date'])) : null;

$end_date = !empty($_GET['end_date']) ? date('Y-m-d', strtotime($_GET['end_date'])) : null;



if ( $start_date && $end_date ) {

	$additional_where .= ' AND a.date BETWEEN ? AND ?';

	$bind[] = $start_date;

	$bind[] = $end_date;

}



$q = !empty($_GET['q']) ? $_GET['q'] : null;

if ( $q ) {

	$additional_where .= ' AND u.name LIKE "%'.$q.'%"';

	$additional_where .= ' OR ad.reclamation LIKE "%'.$q.'%"';

	$additional_where .= ' OR ad.insurer LIKE "%'.$q.'%"';

	$additional_where .= ' OR c.fname LIKE "%'.$q.'%"';

	$additional_where .= ' OR c.lname LIKE "%'.$q.'%"';

	$additional_where .= ' OR a.id LIKE "%'.$q.'%"';

	$additional_where .= ' OR c.email LIKE "%'.$q.'%"';

	$additional_where .= ' OR c.tel1 LIKE "%'.$q.'%"';

}



if ( $page > 1 ) $offset = $limit * $page;





$query = 'SELECT u.name as estimator, ad.reclamation, ad.insurer, c.fname, c.lname, a.date as appointment_date, a.id as appointment_id, c.email, c.tel1 FROM appointment_details ad

  LEFT JOIN appointment a ON a.id = ad.appointment_id AND a.type = ?

  LEFT JOIN login_users u ON u.user_id = ad.tech_id

  LEFT JOIN clients c ON ad.client_id = c.clientid

WHERE ad.checkbox_not_presented = 1 ' . $additional_where;



$query .= ' LIMIT ' . $offset . ', ' . $limit;





$estimations = $db->query($query, $bind)->toArray();



include_once('header.php');

?>



<div class="container">

	<div class="row">

		<div class="col-md-12">

			<h2 class="text-uppercase">RAPPORT DES RENDEZ-VOUS MANQUÉS POUR ESTIMATION</h2>



			<form action="/admin/estimation_no_show_report.php?n=1" method="get">

				<div class="col-md-2">

					<div class="field date">

						<label for="date">Dates</label><br/>

						<input type="text" class="form-control dp" name="start_date" value="<?php putValue($start_date); ?>"

						       placeholder="Start date"/>

						to

						<input type="text" class="form-control dp" name="end_date" value="<?php putValue($end_date); ?>"

						       placeholder="End date"/>

						<span class="clearfix"></span>

					</div>



					<div class="field">

						Recherche: <br>

						<input type="text"class="form-control" name="q"  value="<?php putValue($q); ?>">

					</div>





					<hr>

					<div class="field">

						<button class="btn btn-sm">Filtre de recherche</button>

					</div>

					<br>

					<div class="field">

						<?php

							if ( ! empty($_GET['page']) ) {

								unset($_GET['page']);

							}

							$_GET['no'] = 1;

						?>

						<a href="/admin/estimation_no_show_report.php?<?php echo http_build_query($_GET); ?>&page=<?php echo $page > 1 ? $page - 1: 1; ?>" class="btn btn-xs btn-success">Retour</a> /

						<a href="/admin/estimation_no_show_report.php?<?php echo http_build_query($_GET); ?>&page=<?php echo $page >= 1 ? $page + 1: 1; ?>" class="btn btn-xs btn-warning">Suivant</a>

					</div>



				</div>

			</form>



			<div class="col-md-9 col-md-offset-1">

				<div class="table-responsive">

					<table id="reclamation_report_dt" class="table table-striped table-bordered dt-responsive"

					       cellspacing="0">

						<thead>

						<tr>

							<th>Estimateur</th>

							<th>#Réclamation</th>

							<th>Assurreur</th>

							<th>Nom</th>

							<th>Courriel</th>

							<th>Tél</th>

							<th>Date</th>

							<th></th>

						</tr>

						</thead>

						<tbody>

							<?php foreach ( $estimations as $estimation ): ?>

								<tr>

									<td><?php echo $estimation['estimator']; ?></td>

									<td><?php echo $estimation['reclamation']; ?></td>

									<td><?php echo $estimation['insurer']; ?></td>

									<td><?php echo $estimation['fname']; ?> <?php echo $estimation['lname']; ?></td>

									<td><?php echo $estimation['email']; ?></td>

									<td><?php echo $estimation['tel1']; ?></td>

									<td><?php echo $estimation['appointment_date']; ?></td>

									<td><a href="/admin/appointment_view.php?appointment_id=<?php echo $estimation['appointment_id']; ?>">view</a></td>

								</tr>

							<?php endforeach; ?>

						</tbody>

					</table>

				</div>

			</div>





		</div>

	</div>

</div>



<?php



$pageSpecificJS = <<<EOT

<script>

	

	$(function() {

	  	$('.dp').datepicker({

	  		dateFormat: 'yy-mm-dd'

	  	});

	});

				

</script>

EOT;





?>



<?php include_once('footer.php'); ?>

