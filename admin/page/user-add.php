<?php include_once('admin.php'); ?>
<?php include_once(dirname(dirname(__FILE__)) . '/classes/add_user.class.php'); ?>
<fieldset>
	<form method="post" class="form " action="page/user-add.php" id="user-add-form">
		<div id="message"></div>
		<fieldset>
			<div class="form-group">
				<label class="control-label" for="name"><?php _e('Name'); ?></label>
				<div class="controls">
					<input type="text" class="form-control input-xlarge" id="name" name="name">
				</div>
			</div>

			<div class="form-group" id="usrCheck">
				<label class="control-label" for="username"><?php _e('Username'); ?></label>
				<div class="controls">
					<input type="text" class="form-control input-xlarge" id="username" name="username" maxlength="15">
				</div>
			</div>

			<div class="form-group">
				<label class="control-label" for="email"><?php _e('Email'); ?></label>
				<div class="controls">
					<input type="email" class="form-control input-xlarge" id="email" name="email">
				</div>
			</div>
		<p class="help-block"><?php _e('<b>Note</b>: A random password will be generated and emailed to the user.'); ?></p>
		</fieldset>
		<div class="form-actions">
			<button type="submit" name="add_user" class="btn btn-primary" id="user-add-submit"><?php _e('Add user'); ?></button>
		</div>
	</form>
</fieldset>
