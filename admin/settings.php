<?php

include_once( dirname(dirname(__FILE__)) . '/classes/check.class.php');
protect("Admin");

if(empty($_POST))
	include_once('header.php');

include_once('classes/settings.class.php');
?>

	<div id="message"></div>

	  <div class="tabbable tabs-left">

		<ul class="nav nav-tabs">
			<li><a href="#general-options" data-toggle="tab"><i class="glyphicon glyphicon-cog"></i> <?php _e('General'); ?></a></li>
			<li><a href="#denied" data-toggle="tab"><i class="glyphicon glyphicon-exclamation-sign"></i> <?php _e('Denied'); ?></a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-envelope"></i> <?php _e('Emails'); ?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#emails-welcome" data-toggle="tab"><?php _e('Welcome'); ?></a></li>
                <li><a href="#emails-activate" data-toggle="tab"><?php _e('Activate'); ?></a></li>
                <li><a href="#emails-forgot" data-toggle="tab"><?php _e('Forgot'); ?></a></li>
                <li><a href="#emails-add-user" data-toggle="tab"><?php _e('Add user'); ?></a></li>
                <li><a href="#emails-acct-update" data-toggle="tab"><?php _e("'My Account' changes"); ?></a></li>
              </ul>
            </li>
			<li><a href="#user-profiles" data-toggle="tab"><i class="glyphicon glyphicon-user"></i> <?php _e('Profiles'); ?></a></li>
			<li><a href="#integration" data-toggle="tab"><i class="glyphicon glyphicon-random"></i> <?php _e('Integration'); ?></a></li>
			<li><a href="#update" data-toggle="tab"><i class="glyphicon glyphicon-flag"></i> <?php _e('Update'); ?> <?php if($settings->newUpdate()) : ?><span class="label label-info"><?php _e('new'); ?></span><?php endif; ?></a></li>
		</ul>

		<form class="" method="post" action="settings.php" id="settings-form">

			<div class="tab-content">

				<!-- - - - - - - - - - - - - - - - -

						General

				- - - - - - - - - - - - - - - - - -->
				<div class="tab-pane col-md-10 fade" id="general-options">
					<?php include_once('page/general-options.php'); ?>
				</div>

				<!-- - - - - - - - - - - - - - - - -

						Denied messages

				- - - - - - - - - - - - - - - - - -->
				<div class="tab-pane col-md-10 fade" id="denied"></div>

				<!-- - - - - - - - - - - - - - - - -

						Emails - Welcome

				- - - - - - - - - - - - - - - - - -->
				<div class="tab-pane col-md-10 fade" id="emails-welcome"></div>

				<!-- - - - - - - - - - - - - - - - -

						Emails - Activate

				- - - - - - - - - - - - - - - - - -->
				<div class="tab-pane col-md-10 fade" id="emails-activate"></div>

				<!-- - - - - - - - - - - - - - - - -

						Emails - Forgot

				- - - - - - - - - - - - - - - - - -->
				<div class="tab-pane col-md-10 fade" id="emails-forgot"></div>

				<!-- - - - - - - - - - - - - - - - -

						Emails - Add User

				- - - - - - - - - - - - - - - - - -->
				<div class="tab-pane col-md-10 fade" id="emails-add-user"></div>

				<!-- - - - - - - - - - - - - - - - -

						Emails - Account update

				- - - - - - - - - - - - - - - - - -->
				<div class="tab-pane col-md-10 fade" id="emails-acct-update"></div>

				<!-- - - - - - - - - - - - - - - - -

						Profiles

				- - - - - - - - - - - - - - - - - -->

				<div class="tab-pane col-md-10 fade" id="user-profiles"></div>

				<!-- - - - - - - - - - - - - - - - -

						Integration

				- - - - - - - - - - - - - - - - - -->
				<div class="tab-pane col-md-10 fade" id="integration"></div>

				<!-- - - - - - - - - - - - - - - - -

						Update

				- - - - - - - - - - - - - - - - - -->
				<div class="tab-pane col-md-10 fade" id="update"></div>

			</div>
			<div class="col-md-12">
				<div class="form-actions">
					<button type="submit" data-loading-text="<?php _e('saving...'); ?>" data-complete-text="<?php _e('Changes saved'); ?>" name="save-settings" class="btn btn-primary" id="save-settings"><?php _e('Save changes'); ?></button>
				</div>
			</div>
		</form>
	  </div>

<?php

include_once('footer.php');
