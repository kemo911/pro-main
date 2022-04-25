<?php include_once('settings.php'); ?>
<fieldset>
	<legend><?php _e('Welcome email'); ?></legend>
	<div class="form-group">
		<label class="control-label" for="email-welcome-subj"><?php _e('Welcome'); ?> <a href="#" data-rel="tooltip" tabindex="99" title="<?php _e('The email a user receives after registering for your site.'); ?>"><i class="glyphicon glyphicon-question-sign"></i></a></label>
		<div class="controls">
			<label>
				<input type="text" class="form-control input-xlarge" id="email-welcome-subj" name="email-welcome-subj" value="<?php echo $settings->getOption('email-welcome-subj'); ?>">
				<p class="help-block"><?php _e('Subject'); ?></p>
			</label>
			<textarea class="form-control input-xlarge" id="email-welcome-msg" name="email-welcome-msg" rows="10"><?php echo $settings->getOption('email-welcome-msg'); ?></textarea>
			<div class="help-block">
				<p><?php _e('Message body'); ?></p><br>
				<p><strong><?php _e('Shortcodes:'); ?></strong></p>
				<p><?php _e('Site address:'); ?> <code>{{site_address}}</code></p>
				<p><?php _e('Full name:'); ?> <code>{{full_name}}</code></p>
				<p><?php _e('Username:'); ?> <code>{{username}}</code></p>
				<p><?php _e('Email:'); ?> <code>{{email}}</code></p>
				<p><?php _e('Activation link:'); ?> <code>{{activate}}</code></p>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label" for="email-new-user-subj"><?php _e('Notification'); ?> <a href="#" data-rel="tooltip" tabindex="99" title="<?php _e('A user group can receive notifications on new registrations if enabled in Settings > General.'); ?>"><i class="glyphicon glyphicon-question-sign"></i></a></label>
		<div class="controls">
			<label>
				<input type="text" class="form-control input-xlarge" id="email-new-user-subj" name="email-new-user-subj" value="<?php echo $settings->getOption('email-new-user-subj'); ?>">
				<p class="help-block"><?php _e('Subject'); ?></p>
			</label>
			<textarea class="form-control input-xlarge" id="email-new-user-msg" name="email-new-user-msg" rows="10"><?php echo $settings->getOption('email-new-user-msg'); ?></textarea>
			<div class="help-block">
				<p><?php _e('Message body'); ?></p><br>
				<p><strong><?php _e('Shortcodes:'); ?></strong></p>
				<p><?php _e('Site address:'); ?> <code>{{site_address}}</code></p>
				<p><?php _e('Full name:'); ?> <code>{{full_name}}</code></p>
				<p><?php _e('Username:'); ?> <code>{{username}}</code></p>
				<p><?php _e('Email:'); ?> <code>{{email}}</code></p>
			</div>
		</div>
	</div>
</fieldset>