/**
 * Created by Lenovo on 1/23/2018.
 */
$(document).ready(function() {
    $( "#start_date, #end_date" ).datepicker();
    load_invoice_report();
   function load_invoice_report(filter_data) {
       var invoice_report_dt = $('#invoice_report_dt').DataTable({
           "language": {
               "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json"
           },
           "processing": true,
           "serverSide": true,
           "ajax": {
               url: "/admin/ajax/invoice_report.php",
               data: {
                   filter_data: JSON.stringify(filter_data)
               },
               dataType: "json"
           },
           dom: 'Bfrtip',
           buttons: [
               'excel', { extend: 'print', text: 'Imprimer' }
           ],
           "pagingType": "numbers",
           columns: [
               {responsivePriority: 1},
               {responsivePriority: 2},
               {responsivePriority: 3},
               {responsivePriority: 4},
               {responsivePriority: 5},
               {responsivePriority: 6},
               {responsivePriority: 7},
               {responsivePriority: 8},
               {responsivePriority: 9},
               {responsivePriority: 10},
               {responsivePriority: 11},
               {responsivePriority: 12},
           ]
       });

       invoice_report_dt.on( 'preDraw', function (e, settings) {
              // console.log(settings.json);
            if(typeof settings.json != 'undefined' && typeof settings.json.sub_total != 'undefined'){
                $('#sum_sub_total').html(settings.json.sub_total.toFixed(2));
            }

           if(typeof settings.json != 'undefined' && typeof settings.json.tvq != 'undefined'){
               $('#sum_tvq').html(settings.json.tvq.toFixed(2));
           }

           if(typeof settings.json != 'undefined' && typeof settings.json.tps != 'undefined'){
               $('#sum_tps').html(settings.json.tps.toFixed(2));
           }

           if(typeof settings.json != 'undefined' && typeof settings.json.franchise != 'undefined'){
               $('#sum_franchise').html(settings.json.franchise.toFixed(2));
           }

           if(typeof settings.json != 'undefined' && typeof settings.json.total != 'undefined'){
               $('#sum_total').html(settings.json.total.toFixed(2));
           }

           if(typeof settings.json != 'undefined' && typeof settings.json.deposit != 'undefined'){
               $('#sum_deposit').html(settings.json.deposit.toFixed(2));
           }

           if(typeof settings.json != 'undefined' && typeof settings.json.balance != 'undefined'){
               $('#sum_solde').html(settings.json.balance.toFixed(2));
           }
       });

       invoice_report_dt.on( 'draw', function (e, settings) {
           $('#invoice_report_dt tr td:nth-child(10)').each(function () {
               if($(this).text() == '1'){
                   $(this).text('oui');
               }else if($(this).text() == '0'){
                   $(this).text('non');
               }
           });

           $('#invoice_report_dt tr td:nth-child(11)').each(function () {
               if($(this).text() == '1'){
                   $(this).text('oui');
               }else if($(this).text() == '0'){
                   $(this).text('non');
               }
           });
       });

   }
    $('#start_date, #end_date, #tech, #insurer, #client, #solde, #rental_car, #pending_paid').change(function () {
        var filter_data = {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            tech: $('#tech').find('option:selected').val(),
            //insurer: $('#insurer').find('option:selected').val(),
            client_id: $('#client').find('option:selected').val(),
            solde: $('#solde').find('option:selected').val(),
            rental_car: $('#rental_car').find('option:selected').val(),
            pending_paid: $('#pending_paid').find('option:selected').val()
        }

        $('#invoice_report_dt').DataTable().destroy();
        load_invoice_report(filter_data);
    });

});


