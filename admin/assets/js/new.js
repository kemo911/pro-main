'use strict';
angular.module('app', ['ngRoute', 'ngAnimate', 'ngFileUpload', 'xeditable']);
angular.module('app')
    .run(['editableOptions', function(editableOptions) {
        editableOptions.theme = 'default'; // bootstrap3 theme. Can be also 'bs2', 'default'
    }])
    .factory('ClientService', function ($http, Upload)
    {
        return {
            import: function (data) {
                return Upload.upload({
                    url: 'ajax/import_clients.php',
                    data: {file: data},
                });
            }
        }
    })
    .factory('EstimationService', function ($http, Upload)
    {
        return {}
    })
    .factory('ReclamationService', function ($http, Upload)
    {
        var POST_URL = '/admin/ajax/ajax_estimations.php';
        return {
            getAll: function (data) {
                return Upload.upload({url: POST_URL, data: data});
            },
            getAppointmentPhotos: function (data) {
                return Upload.upload({url: POST_URL, data: data});
            },
            uploadPhoto: function (reclamation, photo) {
                return Upload.upload({
                    url: POST_URL,
                    data: {
                        action: 'uploadReclamationPhoto',
                        data: reclamation,
                        file: photo
                    }
                });
            },
            deleteReclamationPhoto: function(photo) {
                return Upload.upload({
                    url: POST_URL,
                    data: {
                        action: 'deleteReclamationPhoto',
                        data: photo
                    }
                });
            },
            getRecentlyCreatedReclamation: function (data) {
                return Upload.upload({
                    url: POST_URL,
                    data: {
                        action: 'CheckAndFetchRecentlyCreatedReclamationBySession',
                        data: {},
                    }
                });
            },
            createEstimateAndInvoice: function (invoice, estimation) {
                return Upload.upload({
                    url: POST_URL,
                    data: {
                        action: 'createEstimateAndInvoice',
                        invoice: invoice,
                        estimation: estimation
                    }
                });
            },
            updateEstimateAndInvoice: function (invoice, estimation, invoiceId) {
                invoice.id = invoiceId;
                return Upload.upload({
                    url: POST_URL,
                    data: {
                        action: 'updateEstimateAndInvoice',
                        invoice: invoice,
                        estimation: estimation
                    }
                });
            },
            changeInvoiceType: function(invoice) {
                return Upload.upload({
                    url: POST_URL,
                    data: {
                        action: 'changeInvoiceType',
                        invoice: invoice
                    }
                });
            },
            loadContentById: function (id) {
                return Upload.upload({
                    url: POST_URL,
                    data: {
                        action: 'loadContentById',
                        'id': id,
                    }
                });
            },
        }
    })
    .factory('InvoiceService', function ($http, Upload)
    {
        return {}
    })
    .config(function ($routeProvider, $locationProvider, $httpProvider) {
        $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        $routeProvider.when('/', {
            controller: 'estimationCtrl'
        }).when('/estimation/:id', {
            controller: 'estimationCtrl3'
        });
    })
    .controller("clientCtrl",
        ["$rootScope", "$scope", "$http", '$route', '$location', '$timeout', 'ClientService',
            function clientCtrl($rootScope, $scope, $http, $route, $location, $timeout, ClientService)
            {
                $scope.dataFile = '';
                this.$onInit = function () {
                    $scope.showUploadBox = false;
                };

                $scope.exportClients = function () {

                };

                $scope.importClients = function () {
                    ClientService.import($scope.dataFile).then(function (response) {
                        if ( ! response.data.success) {
                            alert(response.data.message);
                        }
                        else {
                            window.location.reload();
                        }
                    }, function (reason) {
                        alert(reason.data.message);
                    });
                };
            }
        ]
    )
    .controller("estimationCtrl",
        ["$rootScope", "$scope", "$http", "$route", "$routeParams", "$location", "$interval", "$timeout", 'ClientService', 'ReclamationService', 'InvoiceService', 'EstimationService',
            function estimationCtrl($rootScope, $scope, $http, $route, $routeParams, $location, $interval, $timeout, ClientService, ReclamationService, InvoiceService, EstimationService)
            {
                var SUCCESS = 'success';
                var WARNING = 'warning';
                var ERROR   = 'danger';
                var INFO    = 'info';
                $scope.user = {
                    name: 'awesome user'
                };
                /*Estimation object*/
                $scope.showCreateReclamationForm = false;
                $scope.showNewReclamationBlock = false;
                $scope.showCreateInvoiceForm = false;
                $scope.showReclamationChooseWidget = true;
                $scope.estimation = {
                    reclamation: null,
                    type_of_loss:'grêle'
                };

                this.$onInit = function () {
                    $scope.loadAllReclamations();
                    sessionStorage.setItem('reclamation', JSON.stringify($scope.estimation.reclamation));
                    if ( $location.path() && $location.path() !== '/' ) {
                        $scope.pid = $location.path().replace('/estimation/', '');
                        $scope.loadContentById($scope.pid);
                    } else {
                        shared.ready = true;
                        jQuery('#inv_covid_price').val('50.00');
                    }
                };
                $scope.setMessage = function (message, type) {
                    $scope.message = {
                        'type': type,
                        'text': message
                    };
                };
                $scope.isReclamationSetProperlyAlsoNotifyUser = function() {
                    if ( ! $scope.estimation.reclamation) {
                        $scope.setMessage('Choisir ou créer une réclamation en premier.', ERROR);
                        jQuery("html, body").animate({ scrollTop: 0 }, "slow");
                        return false;
                    }
                    return true;
                };

                $scope.showCreateReclamationBlock = function() {
                    $scope.showCreateReclamationForm = true;
                };

                $scope.createNewReclamation = function() {
                    $scope.showNewReclamationBlock = true;
                    $scope.lookup = $interval(function () {
                        ReclamationService.getRecentlyCreatedReclamation().then(function (value) {
                            if (typeof value.data.reclamation !== 'undefined') {
                                $scope.estimation.reclamation = value.data;
                                $scope.selectReclamation(value.data);
                                $interval.cancel($scope.lookup);
                                $scope.showNewReclamationBlock = false;
                                $scope.reclamations.push(value.data);
                            }
                        }, function (reason) {});
                    }, 5000);
                    window.open('/admin/reclamation.php','_blank', 'location=yes,height='+ (window.innerHeight - 100 )+',width='+ (window.innerWidth - 100 )+',scrollbars=yes,status=no');
                };

                $scope.editReclamation = function() {
                    $scope.showCreateReclamationForm = true;
                };

                $scope.loadContentById = function(id) {
                    ReclamationService.loadContentById(id).then(function (value) {
                        var data = value.data;
                        $scope.estimation  = data.estimation;
                        $scope.invoice     = data.invoice;
                        $scope.estimation.reclamation  = data.reclamation;
                        $scope.estimation.reclamation['photos'] = data.reclamationPhotos;
                        $scope.selectReclamation(data.reclamation);
                        $scope.loadInvoice();
                        shared.ready = true;
                        shared.invoiceId = data.invoice.id;
                    }, function ( reason ) {
                        console.log( reason.message );
                        shared.ready = true;
                    });
                };

                $scope.loadAllReclamations = function () {
                    ReclamationService.getAll({'action': 'loadAllReclamations'}).then(function (response) {
                        $scope.reclamations = response.data;
                    }, function (reason) {
                        $scope.reclamations = [];
                    });
                };

                $scope.selectReclamation = function ( reclamation ) {
                    $scope.estimation.reclamation = reclamation;
                    $scope.showReclamationChooseWidget = false;
                    $scope.reclamation_value = reclamation.reclamation;
                    sessionStorage.setItem('reclamation', JSON.stringify($scope.estimation.reclamation));
                    ReclamationService.getAppointmentPhotos({'action': 'getAppointmentPhotos', 'data': reclamation}).then(function (value) {
                        $scope.estimation.reclamation['photos'] = value.data;
                    }, function (reason) {
                        $scope.estimation.reclamation['photos'] = [];
                    });
                };

                $scope.uploadReclamationPhoto = function(picture) {
                    ReclamationService.uploadPhoto($scope.estimation.reclamation, picture).then(function (value) {
                        $scope.estimation.reclamation['photos'] = value.data;
                    }, function (reason) {});
                };

                $scope.deleteReclamationPhoto = function(photo) {
                    ReclamationService.deleteReclamationPhoto(photo).then(function (value) {
                        var index = $scope.estimation.reclamation.photos.indexOf(photo);
                        $scope.estimation.reclamation.photos.splice(index,1);
                    }, function (reason) {
                        alert('Photo not deleted');
                    });
                };

                $scope.editReclamationSelection = function () {
                    $scope.showReclamationChooseWidget = true;
                };

                $scope.ifReclamationSetProperlyThenContinue = function () {
                    if ( $scope.isReclamationSetProperlyAlsoNotifyUser ) {
                       $scope.showCreateReclamationForm = false;
                    }
                };

                $scope.closeReclamation = function () {
                    $scope.showCreateReclamationForm = false;
                };


            // ///////////////////////////////////////
            //    INVOICE
            //////////////////////////////////////////
                $scope.invoice = {
                    id: 0,
                };
                $scope.createInvoice = function () {
                    if ( $scope.isReclamationSetProperlyAlsoNotifyUser() ) {
                        //Reclamation set properly now what?

                        var damages = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,'stripping','other_fees','glazier','work_force','parts','covid'];
                        var subs = ['tech','note','price'];
                        var invoiceData = {
                            rental_agreement: jQuery('#invoice_location').val(),
                            rental_car: jQuery('#rental_car').is(':checked') ? 1 : 0,
                            payment_status: jQuery('#payment_status').val(),
                            sub_total: jQuery('#sub_total').val(),
                            tps: jQuery('#tps').val(),
                            tvq: jQuery('#tvq').val(),
                            franchise: jQuery('.invoice_franchise').val(),
                            total: jQuery('#total').val(),
                            deposit: jQuery('#deposit').val(),
                            balance: jQuery('#balance').val(),
                            damages: jQuery('#damages').val(),
                            payment_method: jQuery('input[name="payment_method"]:checked').val(),
                            number_of_days: jQuery('#number_of_days').val(),
                            confirm_invoice: 1,
                            inv_parts_note: jQuery('#inv_parts_note').val(),
                            parts: Object.values($scope.dotPartGroup).filter(function (data) {
                                return data.name && data.price;
                            }),
                            dots: jQuery('#vueModalDotInfo').val(),
                            shared: JSON.stringify(shared),
                        };
                        invoiceData.parts = $scope.partCalc(JSON.parse(invoiceData.dots)) ?? invoiceData.parts;
                        try {
                            var $apiSignaturePad = jQuery('#signature-pad').signaturePad();
                            if ( $apiSignaturePad.getSignature().length ) {
                                invoiceData['signature_img'] = $apiSignaturePad.getSignatureImage();
                                invoiceData['signature'] = $apiSignaturePad.getSignatureString();
                            }
                        } catch (e) {}

                        jQuery.each(damages, function (k, v) {
                            jQuery.each(subs, function (subKey, subValue) {
                                if ( jQuery('#inv_' + v + '_' + subValue).length ) {
                                    invoiceData['inv_' + v + '_' + subValue] = jQuery('#inv_' + v + '_' + subValue).val();
                                }
                            });
                        });

                        if ($scope.dotInfoHolder) {
                            invoiceData['javascript_object'] = $scope.dotInfoHolder;
                        }

                        ReclamationService.createEstimateAndInvoice(invoiceData, $scope.estimation).then(function (value) {
                            $scope.setMessage(value.data.message, SUCCESS);
                            window.location.href = '/admin/estimation.php#!/estimation/' + value.data.id;
                        }, function (reason) {
                            $scope.setMessage(reason.message, ERROR);
                            jQuery("html, body").animate({ scrollTop: 0 }, "slow");
                        });
                    }
                };

                $scope.updateInvoice = function() {

                    if ( $scope.isReclamationSetProperlyAlsoNotifyUser() ) {
                        //Reclamation set properly now what?

                        var damages = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,'stripping','other_fees','glazier','work_force','parts', 'covid'];
                        var subs = ['tech','note','price'];
                        var invoiceData = {
                            rental_agreement: jQuery('#invoice_location').val(),
                            rental_car: jQuery('#rental_car').is(':checked') ? 1 : 0,
                            number_of_days: jQuery('#number_of_days').val(),
                            payment_status: jQuery('#payment_status').val(),
                            sub_total: jQuery('#sub_total').val(),
                            tps: jQuery('#tps').val(),
                            tvq: jQuery('#tvq').val(),
                            franchise: jQuery('.invoice_franchise').val(),
                            total: jQuery('#total').val(),
                            deposit: jQuery('#deposit').val(),
                            balance: jQuery('#balance').val(),
                            damages: jQuery('#damages').val(),
                            payment_method: jQuery('input[name="payment_method"]:checked').val(),
                            confirm_invoice: 1,
                            inv_parts_note: jQuery('#inv_parts_note').val(),
                            parts: Object.values($scope.dotPartGroup).filter(function (data) {
                                return data.name && data.price;
                            }),
                            dots: jQuery('#vueModalDotInfo').val(),
                            shared: JSON.stringify(shared),
                        };

                        invoiceData.parts = $scope.partCalc(JSON.parse(invoiceData.dots)) ?? invoiceData.parts;

                        if ($scope.dotInfoHolder) {
                            invoiceData['javascript_object'] = $scope.dotInfoHolder;
                        }

                        try {
                            var $apiSignaturePad = jQuery('#signature-pad').signaturePad();
                            if ( $apiSignaturePad.getSignature().length ) {
                                invoiceData['signature_img'] = $apiSignaturePad.getSignatureImage();
                                invoiceData['signature'] = $apiSignaturePad.getSignatureString();
                            }
                        } catch (e) {}

                        jQuery.each(damages, function (k, v) {
                            jQuery.each(subs, function (subKey, subValue) {
                                if ( jQuery('#inv_' + v + '_' + subValue).length ) {
                                    invoiceData['inv_' + v + '_' + subValue] = jQuery('#inv_' + v + '_' + subValue).val();
                                }
                            });
                        });


                        ReclamationService.updateEstimateAndInvoice(invoiceData, $scope.estimation, $scope.invoice.id).then(function (value) {
                            $scope.setMessage(value.data.message + '. Auto refreshing within 5 seconds.', SUCCESS);
                            // window.location.href = '/admin/estimation_report.php';
                            jQuery("html, body").animate({ scrollTop: 0 }, "slow");
                            setTimeout(function () {
                                window.location.reload()
                            }, 5000)
                        }, function (reason) {
                            const msg = reason.message || reason.data.message;
                            $scope.setMessage(msg, ERROR);
                            jQuery("html, body").animate({ scrollTop: 0 }, "slow");
                        });
                    }

                };

                $scope.partCalc = function (dots) {
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

                $scope.changeInvoiceType = function(invoice) {
                    ReclamationService.changeInvoiceType(invoice).then(function (value) {
                        window.location.reload();
                    });
                };

                $scope.loadInvoice = function() {

                    angular.forEach($scope.invoice, function (v,k) {
                        if ( jQuery('#' + k).length ) {
                            jQuery('#' + k).val(v);
                        }

                        if (v) {
                            switch (k) {
                                case 'rental_agreement':
                                    jQuery('#invoice_location').val(v);
                                    break;
                                case 'rental_car':
                                    jQuery('#rental_car').attr('checked', v==="1" );
                                    break;
                                case 'number_of_days':
                                    jQuery('#number_of_days').val(v);
                                    break;
                                case 'franchise':
                                    jQuery('.invoice_franchise').val(v);
                                    break;
                                case 'payment_method':
                                    jQuery('input[name="payment_method"]').each(function () {
                                        if (jQuery(this).val() === v) {
                                            jQuery(this).attr('checked', true);
                                        }
                                    });
                                    break;
                                case 'parts':
                                    if (v.length) {
                                        v.map(r => {
                                            let sl = jQuery('input[value="' + r.name + '"]');
                                            if (sl.length) {
                                                sl.parent().trigger('click');
                                                sl.parents('tr').find('.part_description').val(r.description);
                                                sl.parents('tr').find('.part_hour').val(r.hour);
                                                sl.parents('tr').find('.part_price').val(r.price);
                                            }
                                        });
                                    }
                                    break;
                            }
                        }
                    });

                };

                $scope.dotInfo = {};
                $scope.dotInfoHolder = {};
                $scope.dotPartGroup = {
                    0: {
                        name: '',
                        description: '',
                        hour: '',
                        price: '',
                    },
                    1: {
                        name: '',
                        description: '',
                        hour: '',
                        price: '',
                    },
                    2: {
                        name: '',
                        description: '',
                        hour: '',
                        price: '',
                    },
                    3: {
                        name: '',
                        description: '',
                        hour: '',
                        price: '',
                    },
                    4: {
                        name: '',
                        description: '',
                        hour: '',
                        price: '',
                    },
                    5: {
                        name: '',
                        description: '',
                        hour: '',
                        price: '',
                    },
                };
                $scope.addDotInfo = function () {
                    var dotInfo = {
                        number: parseInt(jQuery('#dot_number').val()),
                        tech:   jQuery('#dot_tech').val(),
                        desc:   jQuery('#dot_desc').val(),
                        price:  jQuery('#dot_price').val(),
                    };
                    $scope.dotInfo['number_' + dotInfo.number] = dotInfo;
                    $scope.dotInfoHolder[dotInfo.number] = {
                        price:  jQuery('#dot_price').val(),
                        description:   jQuery('#dot_desc').val(),
                        tech:   jQuery('#dot_tech').val(),
                    };

                    var group = '';
                    if (dotInfo.number === 2) {
                        group = 'A';
                    } else if (dotInfo.number === 6 || dotInfo.number === 7 || dotInfo.number === 11 || dotInfo.number === 12) {
                        group = 'B';
                    } else if (dotInfo.number === 5 || dotInfo.number === 13) {
                        group = 'C';
                    }

                    if (group) {
                        var total = 0;
                        var selector = jQuery('#dotModalPartSection').find('.dot-group-' + group);
                        selector.find('.part_name').each(function () {
                            var partNumber = jQuery(this).data('partNumber');
                            if (jQuery(this).is(':checked')) {
                                $scope.dotPartGroup[partNumber] = {
                                    name: jQuery(this).val(),
                                    description: jQuery('.part_number_' + partNumber + '.part_description').val(),
                                    hour: jQuery('.part_number_' + partNumber + '.part_hour').val(),
                                    price: jQuery('.part_number_' + partNumber + '.part_price').val(),
                                };
                            } else {
                                $scope.dotPartGroup[partNumber] = {
                                    name: '',
                                    description: '',
                                    hour: '',
                                    price: '',
                                };
                            }
                        });
                    }

                };

            }
        ]
    )
;
