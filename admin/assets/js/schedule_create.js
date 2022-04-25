$(document).ready(function() {

    var schedule = {
        start_date: "",
        end_date: "",
        user_type: "",
        user_id: "",
        address: "",
        time_block: 5,
        start_time: "",
        end_time: "",
    };

    $(".dp").datepicker({
        dateFormat: "yy-mm-dd",
        minDate: 0,
    });

    var dates = jQuery('#schedule-start-date, #schedule-end-date').datepicker("option", "onSelect",
        function(selectedDate){
            var $this = $(this),
                option = $this.hasClass("start_date") ? "minDate" : "maxDate",
                adjust = $this.hasClass("end_date") ? 1 : 0,
                base_date = new Date(selectedDate),
                new_date = new Date();

            //new_date.setDate(base_date.getDate() + (1 * adjust));
            //dates.not(this).datepicker("option", option, new_date);
            dates.not(this).datepicker("option", option, base_date);
            var start_date = $('#schedule-start-date').val();
            var end_date = $('#schedule-end-date').val();

            var date1 = new Date(start_date);
            var date2 = new Date(end_date);
            var timeDiff = Math.abs(date2.getTime() - date1.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
            diffDays += 1;


            $('#duration').html('Schedule will be created for ' + diffDays + ' day' + (diffDays > 1 ? 's' : ''));
        }
    );

    /*$('#schedule-start-date').datepicker({
     dateFormat: "yy-mm-dd",
     minDate: 0,
     maxDate: "+1W",
     onSelect: function(dateText) {
     $('#selected-date').html(dateText);
     $('#view-appointment').attr('href', "/admin/schedule_day.php?date=" + dateText);
     },
     });

     $('#schedule-end-date').datepicker({
     dateFormat: "yy-mm-dd",
     minDate: 0,
     maxDate: "+1W",
     onSelect: function(dateText) {
     $('#selected-date').html(dateText);
     $('#view-appointment').attr('href', "/admin/schedule_day.php?date=" + dateText);
     /!*$.post('/admin/ajax/ajax_get_schedules_for_day.php', {date: dateText}, function (response) {
     $('#schedule-lists').html(response);
     });*!/
     }
     });*/

    $('.user_type').click(function () {
        var $input = $(this).find('input');
        if ( $input.val() === 'estimator' ) {
            $('#estimator').attr('disabled', false);
            $('#tech').attr('disabled', true);
        } else if ( $input.val() === 'tech' ) {
            $('#estimator').attr('disabled', true);
            $('#tech').attr('disabled', false);
        }
        schedule.user_type = $input.val();
        schedule.user_id = $('#' + $input.val()).val();
    });

    $('.shift').click(function () {
        var shiftNumber = $(this).val();

        $('.shift-time').attr('disabled', true);
        $('#from' + shiftNumber).attr('disabled', false);
        $('#to' + shiftNumber).attr('disabled', false);
    });

    $('#from1').change(function () {
        var $current = $(this);
        $('#to1').find('option').each(function () {
            var $self = $(this);
            var timeBloc = parseInt($self.data('timeBlock').replace(':',''));
            var currentValue = parseInt($current.val().replace(':',''));
            if ( timeBloc <= currentValue ) {
                $self.attr('disabled', true);
            } else {
                $self.attr('disabled', false);
            }
        });
        $('#to1').val("");
        schedule.start_time = $current.val();
        schedule.end_time = parseInt($current.val()) + 1;
    });

    $('#from2').change(function () {
        var $current = $(this);

        $('#to2').find('option').each(function () {
            var $self = $(this);
            var timeBloc = parseInt($self.data('timeBlock').replace(':',''));
            var currentValue = parseInt($current.val().replace(':',''));
            if ( timeBloc <= currentValue ) {
                $self.attr('disabled', true);
            } else {
                $self.attr('disabled', false);
            }
        });
        $('#to2').val("");
        schedule.start_time = $current.val();
        schedule.end_time = parseInt($current.val()) + 1;
    });


    $('#create-schedule-button').click(function () {

        $('#message').html('');

        schedule['start_date'] = $('#schedule-start-date').val();
        schedule['end_date'] = $('#schedule-end-date').val();
        schedule.time_block = $('#time_block').val();
        schedule.address = $('#address').val();

        if ( $('#estimator').is(':disabled') !== true ) {
            schedule.user_id = $('#estimator').val();
        }
        if ( $('#tech').is(':disabled') !== true ) {
            schedule.user_id = $('#tech').val();
        }

        if ( $('#from2').is(':disabled') !== true ) {
            schedule.start_time = $('#from2').val();
            schedule.end_time = $('#to2').val();
        }
        if ( $('#from1').is(':disabled') !== true ) {
            schedule.start_time = $('#from1').val();
            schedule.end_time = $('#to1').val();
        }

        var message = [];

        $.each(schedule, function (k, v) {
            if ( !v ) {
                message.push(k);
            }
        });

        if ( message.length > 0 ) {
            $('#message').html('<h4 style="color: red;">Please enter value for ' + message.join(', ') + '</h4>');
        } else {
            $.post('/admin/ajax/schedule_create.php', schedule, function (response) {
                response = jQuery.parseJSON(response)
                if ( response.status ) {
                    $.post('/admin/ajax/ajax_get_schedules_for_day.php', {start_date: schedule.start_date, end_date: schedule.end_date}, function (response) {
                        $('#schedule-lists').html(response);
                        $('#message').html('<h4 style="color: red;">'+response.message+'</h4>');
                    });
                } else {
                    $('#message').html('<h4 style="color: red;">'+response.message+'</h4>');
                }
            });
        }

    });

    $(document).on('click', '.delete-schedule', function () {
        var c = confirm('If you delete this block, all associated appointment will be deleted as well.');
        if ( c ) {
            $.post('/admin/ajax/ajax_delete_schedule.php', { "schedule_id": $(this).data('scheduleId') }, function (response) {
                response = jQuery.parseJSON(response);
                if ( response.status ) {
                    window.location.reload();
                }
            });
        }
    });

});