

// Load and render the data table
$(document).ready(function() {
    var table = $('#data_table').DataTable({
        "processing": true,
        "serverSide": true,
        "select": true,
        "ajax": {
            "url": "/episode/display", 
            "data": function(d) {
                    
                    d.filter = {
                        status: $('#filter_status').val(), 
                        date: $('#filter_date').val(),
                        genre: $('#filter_genre').val(),
                        locale: $('#filter_location').val(),
                        format: $('#filter_format').val(),
                        missing_info: $('#missing_info').val(),
                        tag: $('#filter_tag').val()
                        }
                 }
        },
        "columns": [
            { "data": "EpNum" },
            { "data": "callsign" },
            { "data": "programname" },
            { "data": "date" },
            { "data": "prerecorddate" },
            { "data": "starttime" },
            { "data": "description" },
            { "data": "EpNum" },
        ],
        "order": [[ 3, "desc" ]],
        "columnDefs": [

            {
                "render": function ( data, type, row ) {
                    return '<button type="button" onclick="location.href=\'/episode/log/'+data+'\';" class="btn btn-default btn-xs">' +
                        'View ' + '<i class="fa fa-edit" aria-hidden="true"></i></button>';
                },
                "targets": 7
            },
        ]
    });
    
} );
