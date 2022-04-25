
$('.nav-tabs a[href="#user-control"]').tab('show');
$('.nav-tabs a[href="#general-options"]').tab('show');

/* Ajax search. */
function searchSuggest(event) {

	$.post($(event.target.form).attr('action'), $(event.target.form).serialize(), function (data) {
		$('#search_suggest_' + ($(event.target.form).attr('action').indexOf('level') > -1 ? 'level' : 'user')).hide().html(data).fadeIn('fast');
	});

}

/* Save settings */
$('#settings-form').submit(function (e) {
	"use strict";

    e.preventDefault();
    $('#save-settings').button('loading');

    var post = $('#settings-form').serialize();
    var action = $("#settings-form").attr('action');

    $("#message").slideUp(350, function () {

        $('#message').hide();

        $.post(action, post, function (data) {
            $('#message').html(data);
            $('#message').slideDown('slow');
            if (data.match('success') !== null) {
                $('#save-settings').button('reset');
            } else {
                $('#save-settings').button('reset');
            }
        });
    });
});

/* Awesome Selct2 jQuery plugin. */
 $(".chzn-select").select2();

/* Some pretty checkbox features. */
function checkboxToggles() {
	$('input.collapsed').click(unhideHidden);
	$('input.uncollapsed').click(hideHidden);

	if ($('input:checked.collapsed')) {
		$('input:checked.collapsed').parent().next().hide().removeClass('hidden');
	}

	if ($('input:checked.uncollapsed')) {
		$('input:checked.uncollapsed').parent().next().hide().addClass('hidden');
	}
}

checkboxToggles();

function unhideHidden() {
	jQuery(':input:not(:checked).collapsed').parent().next().hide().addClass('hidden');

	if ($(this).prop('checked')) {
		$(this).parent().nextAll().hide().removeClass('hidden').fadeIn();
	}
	else {
		$(this).parent().nextAll().each(
		function(){
			if ($(this).filter('.last').length) {
				$(this).fadeOut(function() { $(this).addClass('hidden') });
				return false;
				}
			$(this).fadeOut(function() { $(this).addClass('hidden') });
		});

	}
}

function hideHidden() {
	if ($(this).prop('checked')) {
		$(this).parent().nextAll().each(
		function(){
			if ($(this).filter('.last').length) {
				$(this).fadeOut(function() { $(this).addClass('hidden') });
				return false;
				}
			$(this).fadeOut(function() { $(this).addClass('hidden') });
		});
	}
	else {
		$(this).parent().nextAll().hide().removeClass('hidden').fadeIn();
	}
}

/* Function necessary to retrieve pagination or other GET variables and pass them to the page we're retrieving. */
function getParameters() {
	var searchString = window.location.search.substring(1) , params = searchString.split("&") , hash = '';
		if ( searchString ) {
			for (var i = 0; i < params.length; i++) {
				var val = params[i].split("=");
				if ( i == 0 ) hash += "?";
				hash += unescape(val[0]) + "=" + unescape(val[1]);
				if ( i + 1 < params.length ) { hash += "&"; }
			}
		}
			return hash;
	}

/* Show the first tab by default. */
$('.nav-tabs a:first').tab('show');

$('a[data-toggle="tab"]').on('click', function (e) {
	var divId = $(e.target).attr('href').substr(1);

	if (divId.substr(0, 4) == 'usr-') return false;
	if (divId == 'truck' || divId == 'car' || divId == 'suv') {
	} else {
		$.get( 'page/' + divId + '.php' + getParameters() ).success(function(data){
			$("#"+divId).html(data);
			checkboxToggles();
			$('[data-rel="tooltip"]').tooltip();
			$(".chzn-select").select2();
		});
	}
});


/** Admin add user form validation */
$("#user-add-form").validate({

	/** Admin add user form submit */
	submitHandler: function() {

		$('#user-add-submit').button('loading');

		var post = $('#user-add-form').serialize();
		var action = $('#user-add-form').attr('action');

		$("#message").slideUp(350, function () {

			$('#message').hide();

			$.post(action, post, function (data) {

				$('#message').html(data);
				$('#message').slideDown('slow');

				if (data.match('success') !== null) {
					$('#user-add-form input').val('');
					$('#user-add-submit').button('reset');
				} else {
					$('#user-add-submit').button('reset');
				}
			});
		});
	},
	rules: {
		name: "required",
		username: {
			required: true,
			minlength: 2,
			remote: {
				url: "classes/add_user.class.php",
				type: "post",
				data: { checkusername: "1" }
			}
		},
		email: {
			required: true,
			email: true,
			remote: {
				url: "classes/add_user.class.php",
				type: "post",
				data: { checkemail: "1" }
			}
		}
	},
	messages: {
		name: "Please enter a name.",
		username: {
			required: "Username is required.",
			minlength: $.validator.format("Enter at least {0} characters"),
			remote: $.validator.format("Username has been taken.")
		},
		email: {
			required: "We need an email address too.",
			email: "Please enter a valid email address.",
			remote: $.validator.format("Email address is in use.")
		}
	},
	errorClass: 'has-error',
	validClass: 'has-success',
	errorElement: 'p',
	highlight: function(element, errorClass, validClass) {
		$(element).parent('div').parent('div').removeClass(validClass).addClass(errorClass);
	},
	unhighlight: function(element, errorClass, validClass) {
		$(element).parent('div').parent('div').removeClass(errorClass).addClass(validClass);
	},
});

$('#add_new_user_btn').click(function(e) {

	e.preventDefault();
	$('#add_user').slideToggle();

});

$('#showUsers').blur(function() {
	$.post('classes/functions.php', {'showUsers' : $(this).val()});

	/* Little hack to refresh the page silently... */
	$('a[href="#level-control"]').tab('show');
	$('a[href="#user-control"]').tab('show');
});

$("#level-add-form").validate({

	/** admin add level form */
	submitHandler: function() {

		$('#level-add-submit').button('loading');

		var post = $('#level-add-form').serialize();
		var action = $("#level-add-form").attr('action');

		$("#level-message").slideUp(350, function () {

			$('#level-message').hide();

			$.post(action, post, function (data) {

				$('#level-message').html(data);

				document.getElementById('level-message').innerHTML = data;
				$('#level-message').slideDown('slow');
				if (data.match('success') !== null) {
					$('#level-add-form input').val('');
					$('#level-add-submit').button('reset');
				} else {
					$('#level-add-submit').button('reset');
				}
			});
		});
	},
	rules: {
		level: {
			required: true,
			remote: {
				url: "classes/add_level.class.php",
				type: "post",
				data: { checklevel: "1" }
			}
		}
	},
	messages: {
		level: {
			required: "This needs to be filled out.",
			remote: $.validator.format("Level name already in use.")
		},
	},
	errorClass: 'has-error',
	validClass: 'has-success',
	errorElement: 'p',
	highlight: function(element, errorClass, validClass) {
		$(element).parent('div').parent('div').removeClass(validClass).addClass(errorClass);
	},
	unhighlight: function(element, errorClass, validClass) {
		$(element).parent('div').parent('div').removeClass(errorClass).addClass(validClass);
	},
});
