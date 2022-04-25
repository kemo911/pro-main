$(document).ready(function () {
    $(document).on('click', '.delete-appointment', function () {
        var c = confirm('Caution: If you delete this appointment, you will not able to undo the step.');
        if ( c ) {
            $.post('/admin/ajax/ajax_delete_appointment.php', { "appointment_id": $(this).data('appointmentId') }, function (response) {
                response = jQuery.parseJSON(response);
                if ( response.status ) {
                    window.location.reload();
                }
            });
        }
    });

    $(document).on('click', '.ad-value-update', function (e) {
        var $self = $(this);

        var curValue = $self.is(':checked') ? 1 : 0;

        $.post('/admin/ajax/ajax_inline_value_update.php', { "table": "appointment_details", "field": $self.attr('name'), "value": curValue, "FK": true, "fkValue": $self.data('appointmentId') }, function (response) {
            response = jQuery.parseJSON(response);
        });
    });

    $('#createInvoiceFromAppointment').click(function () {
        var id = $(this).data('appId');
        $.post('/admin/ajax/ajax_create_invoice_from_appointment.php', { "appointment_id" : id }, function (response) {
            response = jQuery.parseJSON(response);
            if ( response.status ) {
                window.location.href = "/admin/main.php?invoice_id=" + response.invoice_id;
            }
        });
    });

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

});
