<?php



include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');

protect("Admin");



if ( !isset($_POST['add_user']) && !isset($_POST['add_level']) && !isset($_POST['searchUsers']) )

	include_once('header.php');



?>

	<div class="row">

		<div class="tabbable tabs-top">

			<ul class="nav nav-tabs">

				<li><a href="#user-control" data-toggle="tab"><i class="glyphicon glyphicon-list"></i> Utilisateurs</a></li>

				<li><a href="#level-control" data-toggle="tab"><i class="glyphicon glyphicon-list"></i> Niveaux</a></li>


				<!--li><a href="settings.php"><i class="glyphicon glyphicon-cog"></i> <?php _e('Settings'); ?></a></li-->

				

			</ul>



			<div class="tab-content">



				<!-- - - - - - - - - - - - - - - - -



						Control users



				- - - - - - - - - - - - - - - - - -->

				<div class="tab-pane col-md-10 fade" id="user-control">

					<?php include_once('page/user-control.php'); ?>

				</div>



				<!-- - - - - - - - - - - - - - - - -



						Modify levels



				- - - - - - - - - - - - - - - - - -->



				<div class="tab-pane col-md-10 fade" id="level-control"></div>






			</div>

		</div>

	</div>



<?php



include_once('footer.php');

