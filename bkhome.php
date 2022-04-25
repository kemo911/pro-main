<?php include_once('header.php'); ?>

<div class="jumbotron">
	<h1><?php _e('Let\'s talk about the future.'); ?></h1>
	<h2><?php _e('Easy user management and a comprehensive admin panel.'); ?></h2>
	<p>
		<a href="http://goo.gl/LEqvlC" target="_TOP" class="btn btn-info btn-lg"><?php _e('Purchase this script'); ?> &raquo;</a>
		<a href="http://eepurl.com/jNgF9" class="btn btn-default btn-lg" target="_blank"><?php _e('Subscribe to updates'); ?></a>
	</p>
	<p class="info-links">
		<a href="http://phplogin.jigowatt.co.uk/install.php#/install-home" target="_blank"><?php _e('Documentation'); ?></a>
		<a href="http://goo.gl/fbBPIr" target="_blank"><?php _e('Support'); ?></a>
	</p>
</div>

<hr>

<div class="features">
	<div class="row">
		<h1><?php _e('Stupendously exciting login and user management.'); ?></h1>
		<p class="intro"><?php _e('Just the right amount of tools to get your job done.'); ?></p>
		<div class="col-md-6">
			<h2><?php _e('Installation'); ?>
				<?php if (file_exists('config.php')) : ?>
				<small><?php _e('Complete'); ?></small>
				<?php else : ?>
				<small><?php _e('Not complete'); ?></small>
				<?php endif; ?>
			</h2>
			<p><?php _e('Get setup in minutes! Enjoy the super easy installation wizard to walk you through the setup process.'); ?></p>
			<?php if (!file_exists('config.php')) : ?>
			<p><?php _e('Start your installation by clicking the button below!'); ?></p>
			<p><a href="install/index.php" class="btn btn-success"><?php _e('Begin Install'); ?></a></p>
			<?php endif; ?>
		</div>

		<div class="col-md-6">
			<h2><?php _e('Reports'); ?></h2>
			<p><?php _e('Keep track of how your site is doing with dynamic graphs to aid you.'); ?></p>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<h2><?php _e('Security'); ?></h2>
			<p><?php _e('Our script has a couple default secure pages to get you started.'); ?></p>
		</div>

		<div class="col-md-6">
			<h2><?php _e('Admin tools'); ?></h2>
			<p><?php _e('Enjoy the luxury of having control over every aspect of your website.'); ?></p>
		</div>
	</div>
</div>

	<br><br><hr>

<div class="demo features">
	<div class="row">
		<h1><?php _e('Demo credentials.'); ?></h1>
		<p class="intro"><?php _e('Whatcha waiting for? Login and check out the site!'); ?></p>

		<div class="col-md-3 col-md-offset-2">
			<h2><?php _e('Administrator'); ?></h2><br>
			<code><?php _e('Username: admin'); ?></code><br>
			<code><?php _e('Password: admin'); ?></code>
		</div>

		<div class="col-md-3">
			<h2><?php _e('Special user'); ?></h2><br>
			<code><?php _e('Username: special'); ?></code><br>
			<code><?php _e('Password: special'); ?></code>
		</div>

		<div class="col-md-3">
			<h2><?php _e('Default privileges'); ?></h2><br>
			<code><?php _e('Username: user'); ?></code><br>
			<code><?php _e('Password: user'); ?></code>
		</div>
	</div>
</div>

<?php

include_once('footer.php');
