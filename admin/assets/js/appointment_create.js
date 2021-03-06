$(document).ready(function() {

    var appointment = {
        date: "",
        user_type: "",
        user_id: "",
        address: "",
        time_block: 5,
        start_time: "",
        end_time: "",
    };

    $('#appointment-day').datepicker({
        dateFormat: "yy-mm-dd",
        minDate: 0,
        maxDate: "+2W",
        onSelect: function(dateText) {
            var appointmentType = $('#appointment-type').val();
            var rid = $('#reclamation_id').val() > 0 ? '&reclamation_id=' + $('#reclamation_id').val() : '';
            window.location.href = "/admin/appointment_create.php?date="+dateText+"&appointment_type="+appointmentType + rid;
        }
    });

    $('.use-block').click(function () {
        var $this = $(this);
        $('#appointment-table').show();
        $('#user_name').html($this.data('userName'));
        $('#address').html($this.data('userAddress'));

        var scheduleId = $this.data('scheduleId');

        $('#schedule-id').val(scheduleId);
        $('#tech-id').val($this.data('userId'));
        // $('#tech-address').val($this.data('userAddress'));

        populateSlots(scheduleId, function (response) {

            if ( $.trim(response) == 'NOT_FOUND' ) {
                $('#appointment-available-time').attr('disabled', true).html('<option>All Time Filled</option>');
                $('#appointment-complete-step').show();
                $('#appointment-common-details').hide();
            } else {
                $('#appointment-available-time').html(response);
            }
            $('#appointment-time-block').show();
            $('#schedule-table').find('tbody tr').removeClass('active');
            $this.closest('tr').addClass('active');
        });
    });

    var populateSlots = function (scheduleId, callableFn) {
        $.post('/admin/ajax/ajax_appointment_create.php', { "schedule_id" : scheduleId }, callableFn);
    };

    $('#appointment-available-time').change(function () {
        var $self = $(this);

        if ( $self.val() > 0 ) {
            $('#appointment-complete-step').hide();
            $('#appointment-common-details').show();
            $('#appointment-details').show();
        } else {
            $('#appointment-complete-step').show();
            $('#appointment-common-details').hide();
            $('#appointment-details').hide();
        }
    });

    $('#client_id').change(function () {
        var $client = $(this);

        if ( $client.val() > 0 ) {
            $.post('/admin/ajax/ajax_get_client.php', { "id": $client.val() }, function (response) {
                response = jQuery.parseJSON(response);
                $('#client-division').show();
                $('#client-name').html( response.name );
                $('#client-email').html( response.email );
                $('#client-company').html( response.company );
                $('#client-telephone').html( response.telephone );
            });
        } else {
            $('#client-division').hide();
        }
    });

    $('#button-create-appointment').click(function () {
        //validation
        var error = [];

        var appointment = {
            type: $('#appointment-type').val(),
            schedule_id: $('#schedule-id').val(),
            appointment_slot_id: $('#appointment-available-time').val(),
            date: $('#appointment-day').val(),
        };

        var appointment_details = {
            client_id: $('#client_id').val(),
            tech_id: $('#tech-id').val(),
            reclamation: $('#reclamation').val(),
            insurer: $('#insurer').val(),
            vin: $('#vin').val(),
            brand: $('#brand').val(),
            model: $('#model').val(),
            year: $('#year').val(),
            inventory: $('#inventory').val(),
            color: $('#color').val(),
            particular_area: $('#pa').val(),
            brake_type: $('#bt').val(),
            millage: $('#millage').val(),
            notes: $('#note').val(),
            checkbox_not_presented: $('input[name="checkbox_not_presented"]').is(':checked') ? 1 : 0,
            checkbox_total_loss: $('input[name="checkbox_total_loss"]').is(':checked') ? 1 : 0,
            checkbox_want_repair_appointment: $('input[name="checkbox_want_repair_appointment"]').is(':checked') ? 1 : 0,
            checkbox_monetary_compensation: $('input[name="checkbox_monetary_compensation"]').is(':checked') ? 1 : 0,
            checkbox_call_back_for_appointment: $('input[name="checkbox_call_back_for_appointment"]').is(':checked') ? 1 : 0,
        };

        var validationFieldMap = {
            type: 'We could not determine your appointment type',
            schedule_id: 'No valid schedule selected',
            appointment_slot_id: 'No valid appointment time block selected',
            date: 'Could not found a valid date',
            client_id: 'Please select a valid client',
            tech_id: 'No valid tech associated with this schedule',
            reclamation: 'Please provide a valid reclamation',
            vin: 'Please provide a valid vin',
        };

        var requireFieldsForAP = ['type', 'schedule_id', 'appointment_slot_id', 'date'];
        var requireFieldsForAD = ['client_id', 'tech_id']; //'reclamation', 'vin'

        $.each(requireFieldsForAP, function (k, v) {
           if ( ! $.trim(appointment[v]) ) {
               error.push( validationFieldMap[v] );
           }
        });

        $.each(requireFieldsForAD, function (k, v) {
           if ( ! $.trim(appointment_details[v]) ) {
               error.push( validationFieldMap[v] );
           }
        });

        $('#message-area').html(error.join('.<br>'));

        if ( error.length === 0 ) {
            $('#message-area').html('');

            $.post('/admin/ajax/ajax_create_appointment.php', { "appointment": appointment, "appointment_details": appointment_details }, function (response) {
                response = jQuery.parseJSON(response);
                if ( ! response.status ) {
                    $('#schedule-lists').html(response.message);
                } else {
                    window.location.href = "/admin/schedule.php";
                }
            });
        }
    });

    var interval2 = window.setInterval(function () {
        var token = $('#token').val();
        $.post("/admin/ajax/appointment_photos.php", {"token": token}, function (response) {
            $('#photo_view').html(response);
        });
    }, 1000 * 5);

    $(document).on('click', '.delete-app-photo', function (event) {

        var alt = $(this).data('alt');

        if ( confirm('Are you sure to delete this iamge?') ) {
            var id = $(this).data('id');
            $.post("/admin/ajax/appointment_photos_delete.php", { "id": id }, function (response) {
                response = $.parseJSON(response);
                if ( response.status ) {
                    $('#photo-div-' + id).remove();
                    // $('.dz-image > img[*alt="'+alt+'"]').closest('.dz-preview').remove();
                    $('.dz-filename span').each(function() {
                        var imgAlt = $(this).text();
                        var splitValue = imgAlt.split('.', 2);
                        var imgFirstPart = splitValue[0];
                        if ( alt.indexOf( imgFirstPart ) !== -1 ) {
                            setTimeout(function () {
                                $('.dz-image > img[alt="'+imgAlt+'"]').closest('.dz-preview').remove();
                            }, 3000);
                        }
                    });
                }
            });
        }

    });

    $('.create-reclamation').click(function () {

        var $self = $(this);
        var error = [];
        $('#message-area').html('');
        var appointment_details = {
            date: $('#reclamation_date').val(),
            client_id: $('#client_id').val(),
            reclamation: $('#reclamation').val(),
            reclamation_id: $('#reclamation_id').val(),
            insurer: $('#insurer').val(),
            vin: $('#vin').val(),
            brand: $('#brand').val(),
            model: $('#model').val(),
            year: $('#year').val(),
            inventory: $('#inventory').val(),
            color: $('#color').val(),
            particular_area: $('#pa').val(),
            brake_type: $('#bt').val(),
            millage: $('#millage').val(),
            call_back: $('#call_back').is(':checked') ? 1 : 0,
            token: $('#token').val(),
        };

        var validationFieldMap = {
            schedule_id: 'No valid schedule selected',
            appointment_slot_id: 'No valid appointment time block selected',
            date: 'Please select a date',
            client_id: 'SVP choisir un client',
            tech_id: 'No valid tech associated with this schedule',
            reclamation: 'Please provide a valid reclamation',
            vin: 'Please provide a valid vin',
        };

        var requireFieldsForAD = ['client_id', 'date']; //'reclamation', 'vin'

        $.each(requireFieldsForAD, function (k, v) {
            if ( ! $.trim(appointment_details[v]) ) {
                error.push( validationFieldMap[v] );
            }
        });

        $.post("/admin/ajax/ajax_check_reclamation.php", {"reclamation": appointment_details.reclamation, "id": $('#reclamation_id').val()}, function (resp) {
            appointment_details['saving_type'] = $self.val();
            resp = $.parseJSON(resp);
            if ( resp.status == 0 ) {
                error.push( resp.message );
                $('#message-area').html(error.join('.<br>'));
            } else {
                if ( error.length === 0 ) {
                    $('#message-area').html('');
                    $.post('/admin/ajax/ajax_create_reclamation.php', appointment_details, function (response) {
                        response = jQuery.parseJSON(response);
                        if ( ! response.status ) {
                            $('#schedule-lists').html(response.message);
                        } else {
                            window.location.href = response.redirect_url;
                        }
                    });
                } else {
                    $('#message-area').html(error.join('.<br>'));
                }
            }
        });

    });

    $('#reclamation_date').datepicker({
        dateFormat: "yy-mm-dd",
        minDate: 0,
    });

});
