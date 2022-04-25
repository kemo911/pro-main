<?php include_once('admin.php'); ?>

<fieldset>
	<legend>Modifier les niveaux</legend>

	<div class="row">

		<div class="col-md-8 pull-right">
			<form method="post" id="search-levels-form" class="pull-right form-inline" action="classes/add_level.class.php">
			<div class="form-group">
			  <button id="create_new_level_btn" class="btn btn-default">Créer un nouveau niveau</button>
				<div class="input-group">
				  <span class="input-group-addon">
				    <label for="levelSearch"><a href="#" data-rel="tooltip-bottom" title="<?php _e('Search by Name, Level, ID, or Redirect URL.'); ?>"><i class="glyphicon glyphicon-search"></i></a></label>
				  </span>
				  <input class="form-control" type="text" placeholder="<?php _e('Level search'); ?>" onkeyup="searchSuggest(event);" name="searchLevels" id="searchLevels">
				</div>
				  <input type="number" class="form-control input-mini" min="0" id="showLevels" name="showLevels" placeholder="<?php _e('Show'); ?>" value="<?php echo !empty($_SESSION['jigowatt']['levels_page_limit']) ? $_SESSION['jigowatt']['levels_page_limit'] : 10; ?>">
			  </div>
			</form>
		</div>

		<div class="col-md-4">
			<div class="img-thumbnail" style="display:none; width:100%;" id="search_suggest_level"></div>
		</div>

	</div>

	<div id="create_level" style="display:none;">
		<?php include_once('level-create.php'); ?>
	</div>

	<?php user_levels(); ?>
</fieldset>

<script type="text/javascript">
$('#create_new_level_btn').click(function(e) {

	e.preventDefault();
	$('#create_level').slideToggle();

});

$('#showLevels').blur(function() {
	$.post('classes/functions.php', {'showLevels' : $(this).val()});

	/* Little hack to refresh the page silently... */
	$('a[href="#user-control"]').tab('show');
	$('a[href="#level-control"]').tab('show');
});

</script>
