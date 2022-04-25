$(function () {
    var AJAX_URL = '/admin/ajax/ajax_action.php';
    var interval2 = window.setInterval(function () {
        $.post(AJAX_URL, { "action": "get_mold_photo", "mold_id": $('#page-name').data('moldId') }, function (response) {
            $('#photo_view').html(response);
        });
    }, 1000 * 5);

    jQuery('#save-mold').click(function (e) {
        var client_id = $('#clients').val();

        if(client_id == 'undefined' || client_id == ''){
            $('#clients').notify('SVP choisir un client.');
            $("html, body").animate({ scrollTop: 0 }, "slow");
            return false;
        }

        var $apiSignaturePad = $('#signature-pad').signaturePad();
        var signature_img = $apiSignaturePad.getSignatureImage();
        var signature = $apiSignaturePad.getSignatureString();

        $('form#moldForm').submit(function(eventObj) {
            $('<input />').attr('type', 'hidden')
                .attr('name', "signature_img")
                .attr('value', signature_img)
                .appendTo('form#moldForm');
            $('<input />').attr('type', 'hidden')
                .attr('name', "signature")
                .attr('value', signature)
                .appendTo('form#moldForm');
            return true;
        });
    });

    $('label.checkbox-label').click(function () {
        //alert($(this).find('span.custom-checkbox').hasClass('checked'));
        if(!$(this).find('span.custom-checkbox').hasClass('checked')){
            $(this).find('span.custom-checkbox').addClass('checked');
            $(this).next('input[type="hidden"]').attr('value', 1);
        }else {
            $(this).find('span.custom-checkbox').removeClass('checked');
            $(this).next('input[type="hidden"]').attr('value', 0);
        }
    });

});