$(document).ready(function() {

    var AJAX_URL = '/admin/ajax/ajax_action.php';
    var invoiceObject = {};
    var dotInfoHolder = {};
    var dotPartGroup = {
        0: {
            name: '',
            description: '',
            hour: 0,
            price: 0,
        },
        1: {
            name: '',
            description: '',
            hour: 0,
            price: 0,
        },
        2: {
            name: '',
            description: '',
            hour: 0,
            price: 0,
        },
        3: {
            name: '',
            description: '',
            hour: 0,
            price: 0,
        },
        4: {
            name: '',
            description: '',
            hour: 0,
            price: 0,
        },
        5: {
            name: '',
            description: '',
            hour: 0,
            price: 0,
        },
    };
    var dotPartInterval = null;
    var othersInfoHolder = {
        'collapseOne': {
            'stripping_partial': 0,
            'stripping_compact': 0,
            'stripping_standard': 0,
            'stripping_suv': 0,
            'stripping_sky_roof': 0,
            'stripping_dvd_video_acc': 0,
            'stripping_root_support': 0,
            'stripping_back_glass': 0,
            'stripping_price': 0,
            'stripping_tech': null,
            'stripping_note': null
        },
        'collapseTwo': {
            'oversize_dent': 0,
            'other_fees_description': null,
            'other_fees_total_price': 0,
        },
        'collapseThree': {
            'glazier_tech': 0,
            'glazier_price': 0,
            'glazier_note': 0,
        },
        'collapseFour': {
            'work_force_note': 0,
            'work_force_price': 0,
        }
    };
    var damages = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,'stripping','other_fees','glazier','work_force','parts', 'covid'];
    var subs = ['tech','note','price'];
    var otherInvoiceHolder = {
        "stripping": {},
        "other_fees": {},
        "glazier": {},
        "work_force": {},
        "parts": {}
    };

    $('#client_lists_dt').DataTable( {
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json"
        },
        "processing": true,
        "serverSide": true,
        "ajax": "/admin/ajax/list_clients.php",
        "pagingType": "numbers",
        columns: [
            { responsivePriority: 1 },
            { responsivePriority: 2 },
            { responsivePriority: 8 },
            { responsivePriority: 4 },
            { responsivePriority: 5 },
            { responsivePriority: 6 },
            { responsivePriority: 3 },
        ]
    } );
    $('#client_lists_dt_short').DataTable( {
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json"
        },
        "processing": true,
        "serverSide": true,
        "ajax": "/admin/ajax/list_clients_short.php"
    } );
    $('#user_lists_dt').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json"
        }
    });
    $('#user_add_form').parsley({
        successClass: "has-success",
        errorClass: "has-error",
        classHandler: function (el) {
            return el.$element.closest(".form-group");
        },
        errorsContainer: function (el) {
            return el.$element.closest(".form-group");
        },
        errorsWrapper: "<span class='help-block'></span>",
        errorTemplate: "<span></span>"
    });

    if ( $('#clients').length ) {
        $('#clients').select2();
    }
    
    $('#client_search_token').keyup(function () {
        var $self = $(this);

        if ( $self.val().length > 2 ) {
            var $post = $.post( AJAX_URL, {'action': 'list_client_by_search_token', 'token': $self.val() });
            $post.done(function (response) {
                $('#client-lists').find('.list-group').html(response);
            });
            $post.fail(function (response) {
                $('#client-lists').find('.list-group').html(response.text);
            });
        }
    });

    $('#chooseClientModal').on('hidden.bs.modal', function () {
        $('#client_search_token').val('');
        $('#client-lists').find('.list-group').html('');
    });

    $(document).on('click', '#client-lists a.selectClient', function (event) {
        event.preventDefault();

        var $self = $(this);

        $('#clientid').val($self.data('clientId'));
        $('#client_fname').val($self.data('clientFname'));
        $('#client_lname').val($self.data('clientLname'));
        $('#client_email').val($self.data('clientEmail'));
        $('#client_tel1').val($self.data('clientTel'));

        $("#chooseClientModal").modal('toggle');
    });

    $('#date').datepicker();
    $('#time_of_loss').datepicker();

    $('#vehicles').find('a').on('click', function (e) {
        e.preventDefault();
    });

    // Dropzone.autoDiscover = false;
    // if ( $('#div#dropzone').length ) {
    //     $("div#dropzone").dropzone({
    //         paramName: 'file',
    //         url: "/admin/ajax/ajax_file_upload.php",
    //         acceptedFiles: 'image/*',
    //         maxFilesize: 2,
    //         dictDefaultMessage: "Drag your images",
    //     });
    // }

    var response = {};
    var pricing = 0;
    var type = '';
    $('.dot').click(function (e) {
        var $current = $(this);
        var $dotModalPartSection = $('#dotModalPartSection');
        $('#dotPreviewModal').modal('toggle');
        var number = $current.data('dotNumber');

        $('#dotNumber').val(number);
        window.shared.dotNumber = number;
        window.shared.isMounted = true;

        if ( number == 1 || number == 2 || number == 4 ) {
            $('#for_1_2_4').show();
        } else {
            $('#for_1_2_4').hide();
        }

        hideDotGroup();

        if (number === 2) {
            $dotModalPartSection.find('.dot-group-A').show();
            showDotGroup();
            partGroupCalculation('A');
        } else if (number === 6 || number === 7 || number === 11 || number === 12) {
            $dotModalPartSection.find('.dot-group-B').show();
            showDotGroup();
            partGroupCalculation('B');
        } else if (number === 5 || number === 13) {
            $dotModalPartSection.find('.dot-group-C').show();
            showDotGroup();
            partGroupCalculation('C');
        } else {
            $('#part_dot_price').val(0.00);
        }

        type = $current.data('dotType');
        var pricing = 0;
        var name = '';
        $('#dot_number').val(number);
        $.get('/admin/ajax/ajax_get_pricing.php?type='+type+'&number='+number, function (resp) {
            response = resp;
            $('#dot_name').html(resp.name);
        },'json');

        try {

            if ( typeof dotInfoHolder[number] !== 'undefined' ) {
                $('#dot_price').val(dotInfoHolder[number].price);
                $('#dot_desc').val(dotInfoHolder[number].description);
                $('#dot_number').val(number);
                $('#dot_tech').val(dotInfoHolder[number].tech);
            }

        } catch (e) {}
    });

    $('#dotPreviewModal').on('hidden.bs.modal', function () {
        window.shared.isMounted = false;
        response = {};
        pricing = 0;
        type = '';
        $('input[name="dot[extra]"]').attr('checked', false);
        $('input[name="dot[extra]"]').prop('checked', false);
        $('input[name="dot[type]"]:checked').attr('checked', false);
        $('input[name="dot[type]"]:checked').prop('checked', false);
        $('#dot_pricing').val('');
        $('#dot_price').val('');
        $('#dot_desc').val('');
        $('#dot_number').val('');
        $('.dot-label').removeClass('active');
        $('.dot-label-cb').removeClass('active');
        $('#dotModalPartSection').find('.dot-group').hide();

        if (dotPartInterval) {
            clearInterval(dotPartInterval);
        }

        hideDotGroup();
        generateInvoice();
    });

    function showDotGroup() {
        $('.btn-dot-group').show();
    }
    function hideDotGroup() {
        $('.btn-dot-group').hide();
    }

    function partGroupCalculation(group) {
        dotPartInterval = setInterval(function () {
            var total = 0;
            let damageWorkForcePrice = 0;
            var currentDesc = $('#dot_desc').val();
            var $dotModalPartSection = $('#dotModalPartSection');
            if ($dotModalPartSection.find('.dot-group-' + group).length) {
                var selector = $dotModalPartSection.find('.dot-group-' + group);
                selector.find('.part_name').each(function () {
                    var partNumber = $(this).data('partNumber');
                    currentDesc = currentDesc.replace( new RegExp( ', ' + $(this).val(), 'g' ) , '');
                    currentDesc = currentDesc.replace( new RegExp( $(this).val(), 'g' ) , '');
                    if ($(this).is(':checked')) {
                        total += parseInt( $('.part_number_' + partNumber + '.part_price').val() );
                        if ( $('.part_number_' + partNumber + '.part_price').val() > 0 && $('.part_number_' + partNumber + '.part_hour').val() > 0 ) {
                            currentDesc += ( currentDesc ? ', ' : '') + $(this).val();
                            damageWorkForcePrice = parseFloat(damageWorkForcePrice) + parseFloat($('.part_number_' + partNumber + '.part_hour').val()) * 65;
                        }
                    }
                });
                $('#part_dot_price').val(total);
                $('#damagework_force_price').val(damageWorkForcePrice.toFixed(2));
                $('#inv_work_force_price').val(damageWorkForcePrice.toFixed(2));
                calcualteDotPricing();
                $('#dot_desc').val(currentDesc);
            }
        }, 100);
    }

    $('.dot-label').click(function (e) {
        var $self = $(this);
        $('.dot-label').removeClass('active');
        $self.find('input[type="radio"]').prop('checked', true).trigger('click');
    });

    $('.part_name_cb').click(function (e) {
        var $self = $(this);
        $self.find('input[type="checkbox"]').trigger('click');
    });

    $('.part_name').click(function (e) {
        e.stopPropagation();
        var $self = $(this);
        var partNumber = $self.data('partNumber');
        if ($self.is(':checked')) {
            $('.part_number_' + partNumber).attr('disabled', false);
            $('.part_number_' + partNumber).prop('disabled', false);
        } else {
            $('.part_number_' + partNumber).attr('disabled', true);
            $('.part_number_' + partNumber).prop('disabled', true);
        }
    });

    $('input[name="dot[type]"]').click(function (e) {
        e.stopPropagation();
        var $self = $(this);
        var key = $self.data('mmMax');
        var val = $self.val();

        pricing = 0;
        try {
            pricing = response[val][key];
        } catch (error) {
            pricing = 0;
        }

        calcualteDotPricing();
    });

    function calcualteDotDesc($this) {

        if ( ! $this.length ) return '';

        try {
            var min = $this.data('mmMin');
            var max = $this.data('mmMax');
            var val = $this.val();

            return min + '-' + max + 'x ' + + val + 'mm';
        } catch (err) {
            return '';
        }
    }

    function calcualteDotPricing() {

        var prefix = calcualteDotDesc( $('input[name="dot[type]"]:checked') );

        var addToDesc = '';
        var fixedPrice = 0;
        var percentagePrice = 0;

        $('input[name="dot[extra]"]:checked').each(function (index, item) {
            var fm = $(this).data('formula');
            switch ( fm ) {
                case 'addToDesc':
                    if ( addToDesc )
                        addToDesc += ', ' + $(this).val();
                    else
                        addToDesc += $(this).val();
                    break;
                case 'addPricing':
                    fixedPrice = $(this).data('price');

                    if (percentagePrice > 0) {
                        percentagePrice = parseFloat( (fixedPrice + pricing) * 0.25 );
                    }

                    if ( addToDesc )
                        addToDesc += ', ' + $(this).val();
                    else
                        addToDesc += $(this).val();
                    break;
                case 'addPricingPercentage':
                    percentagePrice = fixedPrice === 0 ? parseFloat(pricing * 0.25) : parseFloat( (fixedPrice + pricing) * 0.25 );
                    if ( addToDesc )
                        addToDesc += ', ' + $(this).val();
                    else
                        addToDesc += $(this).val();
                    break;
                case 'addToDescWithPrice':
                    const refId = $(this).data('refId');
                    if (!$(this).val())
                        break;

                    fixedPrice = parseInt($(this).data('price')) * parseInt($(refId).val());

                    if ( addToDesc )
                        addToDesc += ', ' + $(this).val() + ' (' + $(refId).val() +'x)';
                    else
                        addToDesc += $(this).val() + ' (' + $(refId).val() +'x)';
                    break;
            }
        });

        if ( prefix )
            $('#dot_desc').val(prefix + ', ' + addToDesc);
        else
            $('#dot_desc').val(addToDesc);

        $('#dot_pricing').val(parseFloat(fixedPrice + percentagePrice).toFixed(2));

        var dp = parseFloat(pricing) + parseFloat(fixedPrice + percentagePrice);

        $('#dot_price').val(dp.toFixed(2));

        if ( pricing == 0 ) {
            $('#dot_price').val('');
        }

    }

    $('.dot-label-cb').click(function (e) {
        var $self = $(this);
        $self.find('input[name="dot[extra]"]').trigger('click');
    });

    $('input[name="dot[extra]"]').click(function (e) {
        e.stopPropagation();
        calcualteDotPricing();
    });

    $('#bsg_sd').change(function (event) {
        const self = $(this);
        const target = $('.bsg_sd');
        if (self.val() > 0) {
            if (target.find('input[type="checkbox"]').is(':checked')) {
                target.click();
            }
            target.click();
        } else {
            if (target.find('input[type="checkbox"]').is(':checked')) {
                target.click();
            }
        }
    });

    $('#addDotInfo').click(function (event) {

        var dotPrice = $('#dot_price').val();
        var dotDesc = $('#dot_desc').val();
        var dotNumber = $('#dot_number').val();
        dotInfoHolder[dotNumber] = {
            "price": dotPrice,
            "description": dotDesc,
            "tech": $('#dot_tech').val(),
        };
        dotGroupFiller(dotNumber);
        $('#dotPreviewModal').modal('toggle');
    });

    function dotGroupFiller(number) {
        number = parseInt(number);
        var group = '';
        if (number === 2) {
            group = 'A';
        } else if (number === 6 || number === 7 || number === 11 || number === 12) {
            group = 'B';
        } else if (number === 5 || number === 13) {
            group = 'C';
        }

        if (group) {
            var total = 0;
            var selector = jQuery('#dotModalPartSection').find('.dot-group-' + group);
            dotInfoHolder[number]['parts'] = [];
            selector.find('.part_name').each(function () {
                var partNumber = jQuery(this).data('partNumber');
                if (jQuery(this).is(':checked')) {
                    dotPartGroup[partNumber] = {
                        name: jQuery(this).val(),
                        description: jQuery('.part_number_' + partNumber + '.part_description').val(),
                        hour: jQuery('.part_number_' + partNumber + '.part_hour').val(),
                        price: jQuery('.part_number_' + partNumber + '.part_price').val(),
                    };
                    dotInfoHolder[number]['parts'].push(dotPartGroup[partNumber]);
                } else {
                    dotPartGroup[partNumber] = {
                        name: '',
                        description: '',
                        hour: 0,
                        price: 0,
                    };
                }
            });
        }
    }

    //window.setInterval(calcDotGroupGrandTotal, 1000);

    function calcDotGroupGrandTotal() {
        var h = 0,
            p = 0;

        $.each(dotPartGroup, function(i, o) {
            h += o.hour ? parseInt(o.hour) : 0;
            p += o.price ? parseFloat(o.price) : 0;
        });

        var allHoursPrice = parseInt($('#work_force_rate').val()) * h;

        $('#work_force_price').val( parseFloat(allHoursPrice).toFixed(2) );
        $('#inv_parts_price').val( parseFloat(p).toFixed(2) );
    }

    function generateInvoice() {
        $.each(dotInfoHolder, function (k, v) {
            $('[name="invoice[damage'+k+'][tech]"]').val(v.tech);
            $('input[name="invoice[damage'+k+'][price]"]').val(v.price);
            $('input[name="invoice[damage'+k+'][description]"]').val(v.description);
        });

        calculateInvoiceSubTotalPrice();
    }

    $('.invoice_price').blur(function (e) {
        calculateInvoiceSubTotalPrice();
    });
    $('.invoice_price').change(function (e) {
        calculateInvoiceSubTotalPrice();
    });
    $('.invoice_price').keyup(function (e) {
        calculateInvoiceSubTotalPrice();
    });

    $('.invoice_price_calculate').blur(function (e) {
        calculateInvoiceSubTotalPrice();
    });
    $('.invoice_price_calculate').change(function (e) {
        calculateInvoiceSubTotalPrice();
    });
    $('.invoice_price_calculate').keyup(function (e) {
        calculateInvoiceSubTotalPrice();
    });

    function disabledTab( enableType ) {

        enableType = '#' + enableType;
        $('#vehicles a[href='+enableType+']').click();

        $('#vehicles a[href="#car"]').removeAttr('data-toggle');
        $('#vehicles a[href="#truck"]').removeAttr('data-toggle');
        $('#vehicles a[href="#suv"]').removeAttr('data-toggle');

        $('#vehicles a[href='+enableType+']').attr('data-toggle');
    }

    function calculateInvoiceSubTotalPrice() {
        var subTotal = 0;
        $('.invoice_price').each(function() {
            if ( $(this).val() )
                subTotal += parseFloat($(this).val());
        });

        var tps = subTotal * 0.05;
        var tvq = (subTotal + tps) * (9.5/100);

        var fran = $('input[name="invoice[franchise]"]').val();
        var deposit = $('input[name="invoice[deposit]"]').val();

        var total = subTotal + tps + tvq - fran;

        var balance = total - deposit;

        $('input[name="invoice[tps]"]').val(tps.toFixed(2));
        $('input[name="invoice[tvq]"]').val(tvq.toFixed(2));
        $('input[name="invoice[subtotal]"]').val(subTotal.toFixed(2));
        $('input[name="invoice[total]"]').val(total.toFixed(2));
        $('input[name="invoice[deposit]"]').val(deposit);
        $('input[name="invoice[balance]"]').val(balance.toFixed(2));
    }

    $('#scanVIN').click(function () {
        var $this = $('#vin');
        if ( $this.val().trim().length == 17 ) {
            getVinData($this.val().trim());
        } else {
            $('#vin').notify('VIN number is not valid.');
        }
    });
    /*$('#vin').keypress(function(e) {
        if(e.which == 13) {
            var $this = $(this);
            if ( $this.val().trim().length >= 16 ) {
                getVinData($(this).val().trim());
            }
        }
    });*/

    var getVinData = function (vinNumber) {
        $('#vinAreaOverlay').show();
        $.post('/admin/ajax/ajax_vin_data.php', { 'action': 'vin', 'vin_number': vinNumber })
            .done(function (resp) {
                resp = $.parseJSON(resp);
                if ( resp.status ) {
                    var v = resp.results;
                    $('#brand').val(v.brand);
                    $('#model').val(v.model);
                    $('#year').val(v.year);
                    $('#inventory').val(v.inventory);
                    $('#sn').val(v.sn);
                    $('#pa').val(v.pa);
                    $('#bt').val(v.bt);
                    $('#color').val(v.color);
                } else {
                    alert('Wrong VIN!');
                }
                $('#vinAreaOverlay').hide();
            })
            .fail(function (err) {
                $('#vinAreaOverlay').hide();
                console.log(err);
            })
        ;
    };

    var loadInvoice = function() {
        if ( $('#page-name').length && typeof javascriptObject != 'undefined' ) {
            if ( ! $.isEmptyObject(javascriptObject) )
                dotInfoHolder = javascriptObject;
        }

        if ( $('#page-name').length && typeof javascriptObject2 != 'undefined' ) {
            if ( ! $.isEmptyObject(javascriptObject2) ) {
                othersInfoHolder = javascriptObject2;
            }
        }

        //fill dot
        $.each(dotInfoHolder, function (type, obj) {
            if ( $.isEmptyObject(obj) === false ) {
                $.each(obj, function (dot, details) {
                    $('.dot-' + type).css({
                        "background-color": "#ed1c24"
                    });
                });
                //disabledTab(type);
            }
        });

        $.each(othersInfoHolder, function (type, obj) {
            if ( $.isEmptyObject(obj) === false ) {
                $.each(obj, function (k,v) {
                    if ( type == 'collapseOne' ) {
                        if ( k == 'stripping_price' || k == 'stripping_note' || k == 'stripping_tech' ) {
                            $('#' + k).val(v);
                        } else {
                            if ( v == 1 ) {
                                $('#' + k).attr('checked', true);
                            }
                        }
                    } else {
                        $('#' + k).val(v);
                    }
                });
            }
        });

        //fill form data
        //var savedRequest = $.parseJSON(savedRequest);
        $.each(savedRequest, function (key, value) {
            if ( key == 'client_id' ) { $('#clientid').val(value); }
            if ( key == 'f_name' ) { $('#client_fname').val(value); }
            if ( key == 'l_name' ) { $('#client_lname').val(value); }
            if ( key == 'tel' ) { $('#client_tel1').val(value); }
            if ( key == 'email' ) { $('#client_email').val(value); }
            if ( key == 'serial_no' ) { $('#sn').val(value); }
            if ( key == 'particular_area' ) { $('#pa').val(value); }
            if ( key == 'brake_type' ) { $('#bt').val(value); }
            if ( key == 'rental_agreement' ) { $('#invoice_location').val(value); }
            if ( key == 'rental_car' ) {
                if ( value == 1 ) {
                    $('#rental_car').attr('checked', true);
                } else {
                    $('#rental_car').attr('checked', false);
                }
            }
            if ( key == 'javascript_object' ) {  }
            if ( key == 'latest_request' ) {  }
            if ( key == 'damages' ) { $('#invoice_location').text(value); }
            if ( key == 'inv_parts_price' ) { $('#part_price').val(value); $('#inv_parts_price').val(value); }
            $('#'+key).val(value);
        });
    };

    if ( $('#page-name').length && $('#page-name').data('pageName') == 'invoice' && $('#page-name').data('status') =='draft' ) {

        window.setTimeout(function () {
            loadInvoice();
        }, 2000);

        var partCalc = function (dots) {
            //new part section
            try {
                let dotPartGroup = [];
                if (dots) {
                    for (const [i, dot] of Object.entries(dots)) {
                        if (dot) {
                            Object.values(dot.parts).filter( (data) => {
                                if (data.hrs && data.price) {
                                    dotPartGroup.push({
                                        name: data.label,
                                        description: data.desc,
                                        hour: data.hrs,
                                        price: data.price,
                                        damage_section: parseInt(i),
                                    });
                                }
                            });
                        }
                    }
                }
                return dotPartGroup;
            } catch (e) {}
        }

        var autoSaveInvoice = function ( complete ) {

            var error = [];

            var invoiceData = {
                invoice_id: $('#page-name').data('invoiceId'),
                date: $('#date').val(),
                tech: $('#tech').val(),
                client_id: $('#clientid').val(),
                f_name: $('#client_fname').val(),
                l_name: $('#client_lname').val(),
                reclamation: $('#reclamation').val(),
                tel: $('#client_tel1').val(),
                company: $('#client_cie').val(),
                email: $('#client_email').val(),
                insurer: $('#insurer').val(),
                vin: $('#vin').val(),
                brand: $('#brand').val(),
                model: $('#model').val(),
                year: $('#year').val(),
                inventory: $('#inventory').val(),
                serial_no: $('#sn').val(),
                color: $('#color').val(),
                particular_area: $('#pa').val(),
                brake_type: $('#bt').val(),
                millage: $('#millage').val(),
                rental_agreement: $('#invoice_location').val(),
                rental_car: $('#rental_car').is(':checked') ? 1 : 0,
                payment_status: $('#payment_status').val(),
                javascript_object: dotInfoHolder,
                javascript_object2: othersInfoHolder,
                sub_total: $('#sub_total').val(),
                tps: $('#tps').val(),
                tvq: $('#tvq').val(),
                franchise: $('#franchise').val(),
                total: $('#total').val(),
                deposit: $('#deposit').val(),
                balance: $('#balance').val(),
                damages: $('#damages').val(),
                payment_method: $('input[name="payment_method"]:checked').val(),
                number_of_days: $('#number_of_days').val(),
                inv_parts_note: $('#inv_parts_note').val(),
                parts: Object.values(dotPartGroup).filter(function (data) {
                    return data.name && data.price;
                }),
                dots: jQuery('#vueModalDotInfo').val(),
                shared: JSON.stringify(shared),
            };

            invoiceData.parts = partCalc(JSON.parse(invoiceData.dots)) ?? invoiceData.parts;

            if ( complete ) {
                invoiceData['confirm_invoice'] = 1;
            }

            try {
                var $apiSignaturePad = $('#signature-pad').signaturePad();
                if ( $apiSignaturePad.getSignature().length ) {
                    invoiceData['signature_img'] = $apiSignaturePad.getSignatureImage();
                    invoiceData['signature'] = $apiSignaturePad.getSignatureString();
                }
            } catch (e) {}


            $.each(damages, function (k, v) {
                $.each(subs, function (subKey, subValue) {
                    if ( $('#inv_' + v + '_' + subValue).length ) {
                        var currentItemValue = $('#inv_' + v + '_' + subValue).val();
                        invoiceData['inv_' + v + '_' + subValue] = currentItemValue;
                    }
                });
            });

            $.ajax({
                url: '/admin/ajax/ajax_save_invoice.php',
                type: 'post',
                data: invoiceData,
                success: function (resp) {
                    if ( complete ) {
                        window.location.href = '/admin/';
                    }
                    else {
                        $.notify("Auto saving....", "success");
                    }
                },
                error: function (xhr, status, error) {
                    $.notify(error, "error");
                }
            },'json');
        };
        /*var interval = window.setInterval(function () {
            autoSaveInvoice(false);
        }, 1000 * 30);*/
        var interval2 = window.setInterval(function () {
            $.post(AJAX_URL, { "action": "get_invoice_photo", "invoice_id": $('#page-name').data('invoiceId') }, function (response) {
                $('#photo_view').html(response);
            });
        }, 1000 * 5);
    }

    $('#invoice_continue_edit').click(function () {
        $('#invoice_alert').hide();
    });

    var strippingCollections = [];
    $('.stripping').click(function () {
        var $this = $(this);
        var $sp = $('#stripping_price');
        var currentValue = $sp.val() ? parseInt($sp.val()) : 0;

        if ( $this.is(':checked') ) {
            $sp.val( parseInt(currentValue) + parseInt($this.data('price')) );
            strippingCollections.push($this.val());
        } else {
            $sp.val( parseInt(currentValue) - parseInt($this.data('price')) );
            strippingCollections.splice( strippingCollections.indexOf($this.val()), 1 );
        }

        $('#stripping_note').val(strippingCollections.join(' | '));
    });

    $('#stripping_add').click(function () {

        var no_validate = true;

        var $sp = $('#stripping_price');
        var $sn = $('#stripping_note');
        var $st = $('#stripping_tech');

        if ( ( $sp.val() && $sn.val() && $st.val() ) || no_validate ) {
            dotInfoHolder['stripping'] = {
                "price": $sp.val(),
                "description": $sn.val(),
                "tech": $st.val()
            };
            generateInvoice();
            $(this).notify('Ajouté à la facture', {
                position: "right",
                className: 'success',
            });

            $('#collapseOne .panel-body').find('input[type="checkbox"]').each(function() {
                var $self = $(this);
                var key = $self.attr('id');
                othersInfoHolder['collapseOne'][key]=$self.is(':checked') ? 1 : 0;
            });

            othersInfoHolder['collapseOne']['stripping_price']= $('#stripping_price').val();
            othersInfoHolder['collapseOne']['stripping_tech']= $('#stripping_tech').val();
            othersInfoHolder['collapseOne']['stripping_note']= $('#stripping_note').val();
        }
        else {
            $(this).notify('Price, note and tech values are required.', {
                position:"right",
                className: 'error',
            });
        }
    });


    $('#oversize_dent').change(function () {
        var $this = $(this);
        var $ofp = $('#other_fees_price');
        var $oftp = $('#other_fees_total_price');

        if ( $this.val() < 0 )
            $this.val(0);

        var oversize_dent_price = $this.val();
        var other_fees_price = $ofp.val() > 0 ? $ofp.val() : 0;

        $oftp.val( oversize_dent_price * 3 );
    });

    $('#other_fees_price').change(function () {
        var $this = $(this);
        var $ofp = $('#oversize_dent');
        var $oftp = $('#other_fees_total_price');

        if ( $this.val() < 0 )
            $this.val(0);

        var oversize_dent_price = $this.val();
        var other_fees_price = $ofp.val() > 0 ? $ofp.val() : 0;

        $oftp.val( parseInt(oversize_dent_price) + parseInt(other_fees_price) );
    });

    $('#other_fees_add_button').click(function () {

        var no_validate = true;

        var $other_fees_description = $('#other_fees_description');
        var $other_fees_total_price = $('#other_fees_total_price');

        if ( ( $other_fees_description.val().trim().length && $other_fees_total_price.val() > 0 ) || no_validate ) {

            dotInfoHolder['other_fees'] = {
                "price": $other_fees_total_price.val(),
                "description": $other_fees_description.val(),
                "tech": 0
            };
            generateInvoice();

            $(this).notify('Ajouté à la facture.', {
                position:"right",
                className: 'success',
            });

            $.each(othersInfoHolder['collapseTwo'], function (key, val) {
                othersInfoHolder['collapseTwo'][key] = $('#' + key).val();
            });

        } else {
            $(this).notify('Total Price and description is required.', {
                position:"right",
                className: 'error',
            });
        }
    });

    $('#glazier_add').click(function () {

        var no_validate = true;

        var $glazier_tech = $('#glazier_tech');
        var $glazier_note = $('#glazier_note');
        var $glazier_price = $('#glazier_price');

        if ( ($glazier_note.val().trim().length && $glazier_price.val() > 0 && $glazier_tech.val().trim().length) || no_validate ) {

            dotInfoHolder['glazier'] = {
                "price": $glazier_price.val(),
                "description": $glazier_note.val(),
                "tech": $glazier_tech.val(),
            };
            generateInvoice();

            $(this).notify('Ajouté à la facture.', {
                position:"right",
                className: 'success',
            });
            $.each(othersInfoHolder['collapseThree'], function (key, val) {
                othersInfoHolder['collapseThree'][key] = $('#' + key).val();
            });
        } else {
            $(this).notify('Price, tech and description is required.', {
                position:"right",
                className: 'error',
            });
        }
    });

    $('#work_force_add').click(function () {

        var no_validate = true;

        var $work_force_note = $('#work_force_note');
        var $work_force_price = $('#work_force_price');

        if ( ($work_force_note.val().trim().length && $work_force_price.val() > 0) || no_validate ) {

            dotInfoHolder['work_force'] = {
                "price": $work_force_price.val(),
                "description": $work_force_note.val(),
                "tech": 0,
            };
            generateInvoice();

            $(this).notify('Ajouté à la facture.', {
                position:"right",
                className: 'success',
            });
            $.each(othersInfoHolder['collapseFour'], function (key, val) {
                othersInfoHolder['collapseFour'][key] = $('#' + key).val();
            });
        } else {
            $(this).notify('Price and description is required.', {
                position:"right",
                className: 'error',
            });
        }
    });

    $('.part_name').blur(function () {
        var partsDescriptions = [];
        $('.part_name').each(function () {
            var self = $(this);
            if (self.val()) {
                partsDescriptions.push(self.val());
            }
        });
        $('#part_note').val(partsDescriptions.join(' | '));
    });

    $('#parts_add').click(function () {

        var $part_price = $('#part_price');

        if ( $part_price.val() > 0 ) {

            dotInfoHolder['parts'] = {
                "price": $part_price.val(),
                // "description": parseInt($('#total_parts').val()) + ' part' + ( parseInt($('#total_parts').val()) > 1 ? 's' : '' ),
                "description": $('#inv_parts_note').val(),
                "tech": 0,
            };
            generateInvoice();

            $(this).notify('Ajouté à la facture.', {
                position:"right",
                className: 'success',
            });

            //SAVE Parts for Reporting
            var parts = [];
            for ( var p = 1; p <= 10; p++ ) {
                var part = $('#part_' + p).val();
                var part_received_status = $('#part_received_' + p).is(':checked');
                var part_order_status = $('#part_order_' + p).is(':checked');
                var part_price = $('#part_price_' + p).val();

                if ( $.trim(part) && $.trim(part_price) && $.trim(part_price) > 0  ) {
                    parts.push( {
                        'name': part,
                        'price': part_price,
                        'ordered': part_order_status ? 1 : 0,
                        'received': part_received_status ? 1 : 0,
                    } );
                }
            }

            var invoiceId = $('#page-name').data('invoiceId');
            if ( parts.length > 0 ) {
                $.post('/admin/ajax/ajax_save_parts.php', { 'invoice_id': invoiceId, "parts": parts }, function (resp) {

                });
            }
        }
        /*else {
            $(this).notify('Total price is required.', {
                position:"right",
                className: 'error',
            });
        }*/
    });

    $('.part-price').change(function (e) {
        var ttl = 0, total = 0;
        $('.part-price').each(function () {
            var currentValue = $(this).val();
            if ( currentValue && currentValue > 0 ) {
                total += parseFloat(financial(currentValue));
                ttl++;
            }
        });

        $('#part_price').val(total);
        $('#total_parts').val(ttl);
    });

    $('#signature-pad').signaturePad({
        drawOnly: true,
        defaultAction: 'drawIt',
        validateFields: false,
        lineWidth: 0,
        output: null,
        sigNav: null,
        name: null,
        typed: null,
        clear: '#signature-clear',
        typeIt: null,
        drawIt: null,
        typeItDesc: null,
        drawItDesc: null,
        bgColour:'transparent'
    });

    $('#saveInvoice').click(function (e) {
        autoSaveInvoice(true);
    });

    $(document).on('click', '.delete-photo', function (event) {

        var alt = $(this).data('alt');

        if ( confirm('Are you sure to delete this iamge?') ) {
            var id = $(this).data('id');
            var action_name = $(this).data('action') || "delete_invoice_photo";
            $.post(AJAX_URL, { "action": action_name, "id": id }, function (response) {
                response = $.parseJSON(response);
                if ( response.status ) {
                    $('#photo-div-' + $(this).data('id')).remove();
                    // $('.dz-image > img[*alt="'+alt+'"]').closest('.dz-preview').remove();
                    $('.dz-filename span').each(function() {
                        var imgAlt = $(this).text();
                        var splitValue = imgAlt.split('.', 2);
                        var imgFirstPart = splitValue[0];
                        if ( alt.indexOf( imgFirstPart ) !== false ) {
                            setTimeout(function () {
                                $('.dz-image > img[alt="'+imgAlt+'"]').closest('.dz-preview').remove();
                            }, 3000);
                        }
                    });
                }
            });
        }

    });

    $('#reclamation').keyup(function () {
        var v = $(this).val().trim();
        v = v.replace(/[^a-z0-9\-]/gi,'');
        $(this).val(v);
    })
    $('.reclamation-check').blur(function () {
        checkReclamation($(this));
    });

    $('#clients').change(function () {
        var clientId = '', clientFirstName = '', clientLastName = '', clientCIE = '', clientEmail = '', clientTel = '', clientAddress = '';
        var currentClientId = $(this).val();

        if ( currentClientId ) {
            $.each(clients, function (k, v) {
                if ( v.clientid == currentClientId ) {
                    clientId = v.clientid;
                    clientFirstName = v.fname;
                    clientLastName = v.lname;
                    clientCIE = v.cie;
                    clientTel = v.tel1;
                    clientEmail = v.email;
                    clientAddress = v.address;
                }
            });
        }

        $('#clientid').val(clientId);
        $('#client_fname').val(clientFirstName);
        $('#client_lname').val(clientLastName);
        $('#client_email').val(clientEmail);
        $('#client_tel1').val(clientTel);
        $('#client_cie').val(clientCIE);
        $('#client_address').val(clientAddress);
    });

    $('#payment-button').click(function () {
        if ( $(this).data('value') == 'Payment Pending' ) {
            $(this).data('value', 'PAID').removeClass('btn-danger').addClass('btn-success').text('PAID');
            $('#payment_status').val(1);
        } else {$('#payment_status').val(1);
            $(this).data('value', 'Payment Pending').removeClass('btn-success').addClass('btn-danger').text('Payment Pending');
            $('#payment_status').val(0);
        }
    });

    $('#part_note').blur(function () {
        $('#inv_parts_note').val($(this).val());
    });

    $('#inv_parts_note').blur(function () {
        $('#part_note').val($(this).val());
    });

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    function financial(x) {
        return Number.parseFloat(x).toFixed(2);
    }

    function goToByScroll(id){
        // Remove "link" from the ID
        id = id.replace("link", "");
        // Scroll
        $('html,body').animate({
                scrollTop: $("#"+id).offset().top},
            'slow');
    }

    if(getUrlParameter('from') == 'parts_report'){
        $('#partsHead h4 a').trigger('click');
        $('html,body').animate({
                scrollTop: $("#strippingHead").offset().top},
            'slow');
    }

    //lazy fill
    // window.setTimeout(function () {
    //     const inv_glazier_note = $('#inv_glazier_note');
    //     if (!inv_glazier_note.val()) {
    //         inv_glazier_note.val('Uréthane et temps');
    //     }
    // }, 7000);

} );


function checkReclamation($selector) {
    var rec = $selector.val();

    if ( ! $selector.siblings('span').length ) {
        $selector.after('<span class="help-block"></span>');
    } else {
        $selector.siblings('span').html('');
    }

    $.post("/admin/ajax/ajax_check_reclamation.php", {"reclamation": rec}, function (response) {
        response = $.parseJSON(response);
        if ( response.status == 0 ) {
            $selector.siblings('span').html(response.message).css({
                'color': 'red',
                'font-size': '12px',
            });
        }
    });
}
