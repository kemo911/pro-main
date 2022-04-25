/**
 * Created by Lenovo on 1/23/2018.
 */
$(document).ready(function() {

    $( "#start_date, #end_date" ).datepicker();

    load_estimation_report();

    function load_estimation_report(filter_data) {
       $('#estimation_report_dt').DataTable({
           "processing": true,
           "serverSide": true,
           "ajax": {
               url: "/admin/ajax/estimation_report.php",
               data: {
                   filter_data: JSON.stringify(filter_data)
               },
               dataType: "json"
           },
           "pagingType": "numbers",
           columns: [
               {responsivePriority: 1},
               // {responsivePriority: 2},
               // {responsivePriority: 3},
               {responsivePriority: 2},
               {responsivePriority: 3},
               {responsivePriority: 4},
               {responsivePriority: 5},
               {responsivePriority: 6},
               {responsivePriority: 7},
           ],
           "columnDefs": [
               { "searchable": false, "targets": 0 },
               // { "searchable": false, "targets": 2 },
               // { "searchable": false, "targets": 3 },
               { "searchable": false, "targets": 4 },
               // { "searchable": false, "targets": 5 },
               { "searchable": false, "targets": 6 },
           ]
       });
   }

    $('#start_date, #end_date, #estimator, #insurer, #client').change(function () {
        var filter_data = {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            estimator: $('#estimator').find('option:selected').val(),
            client_id: $('#client').find('option:selected').val()
        }

        $('#estimation_report_dt').DataTable().destroy();
        load_estimation_report(filter_data);
    });

});


