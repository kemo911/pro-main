$(document).ready(function() {

    load_report();

    function load_report(filter_data) {
        var cid = $('#callback').val();
        var report_dt = $('#reclamation_report_dt').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json"
            },
            "processing": true,
            "serverSide": true,
            dom: 'Bfrtip',
            buttons: [
                'excel', { extend: 'print', text: 'Imprimer' }
            ],
            "ajax": {
                url: "/admin/ajax/report_reclamation.php",
                data: {
                    filter_data: JSON.stringify(filter_data),
                    callback: cid,
                },
                dataType: "json"
            },
            "pagingType": "numbers"
        });

    }

    $('.dp').datepicker();

    $('#start_date, #end_date').change(function () {
        var filter_data = {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
        };

        $('#reclamation_report_dt').DataTable().destroy();
        load_report(filter_data);
    });

});