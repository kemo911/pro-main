/**
 * Created by Lenovo on 1/23/2018.
 */
$(document).ready(function() {
    $( "#start_date, #end_date" ).datepicker();
    load_parts_report();
   function load_parts_report(filter_data) {
       var parts_report_dt = $('#parts_report_dt').DataTable({
           "language": {
               "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json"
           },
           "processing": true,
           "serverSide": true,
           "ajax": {
               url: "/admin/ajax/parts_report.php",
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
           ]
       });


       parts_report_dt.on( 'draw', function (e, settings) {
           $('#parts_report_dt tr td:nth-child(6)').each(function () {
               if($(this).text() == '1'){
                   $(this).text('oui');
                }else if($(this).text() == '0'){
                   $(this).text('non');
               }
           });

           $('#parts_report_dt tr td:nth-child(7)').each(function () {
               if($(this).text() == '1'){
                   $(this).text('oui');
               }else if($(this).text() == '0'){
                   $(this).text('non');
               }
           });
       });

   }
    $('#start_date, #end_date, #ordered, #received').change(function () {
        var filter_data = {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            ordered: $('#ordered').find('option:selected').val(),
            received: $('#received').find('option:selected').val()
        }

        $('#parts_report_dt').DataTable().destroy();
        load_parts_report(filter_data);
    });


});


