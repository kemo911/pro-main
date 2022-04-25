<?php include_once('admin.php'); ?>
<?php include_once(dirname(dirname(__FILE__)) . '/classes/add_level.class.php'); ?>
<fieldset>
	<form method="post" class="form " id="level-add-form" action="page/level-create.php">
		<div id="level-message"></div>
		<fieldset>
			<div class="form-group">
				<label class="control-label" for="level"><?php _e('Name'); ?></label>
				<div class="controls">
					<input type="text" class="form-control input-xlarge" id="level" name="level" value="<?php echo $addLevel->getPost('level'); ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label" for="redirect"><?php _e('Redirect'); ?> <a href="#" data-rel="tooltip" tabindex="99" title="<?php _e('When logging in, this user will be redirected to the URL you specify. Leave blank to redirect to the referring page.'); ?>"><i class="glyphicon glyphicon-question-sign"></i></a></label>
				<div class="controls">
					<input id="redirect" class="form-control input-xlarge" name="redirect" type="url" placeholder="eg, http://google.com" value="<?php echo $addLevel->getPost('redirect'); ?>"/>
				</div>
			</div>
		<div class="form-actions">
			<button type="submit" name="add_level" class="btn btn-primary" id="level-add-submit"><?php _e('Create level'); ?></button>
		</div>
		</fieldset>
	</form>
</fieldset>
