


// Load and render the data table
$(document).ready(function() {
    var table = $('#data_table').DataTable({
        "processing": true,
        "serverSide": true,
        "select": true,
        "ajax": {
            "url": "/traffic/display", 
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
            { "data": "AdName" },
            { "data": "AdId" },
            { "data": "Category" },
            { "data": "StartDate" },
            { "data": "EndDate" },
            { "data": "Active" },
            { "data": "Friend" },
            { "data": "Playcount" },
        ],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    return '<button type="button" onclick="location.href=\'/traffoc/edit/'+data+'\';" class="btn btn-default btn-xs">' +
                        'Edit ' + '<i class="fa fa-edit" aria-hidden="true"></i></button>';
                },
                "targets": 1
            },
            {
                "render": function ( data, type, row ) {
                    if(data == 1 )
                        return '<i class="fa fa-check-circle-o" style="color: #008000"></i>';
                    else if(data == 0)
                        return '<i class="fa fa-times-circle-o" style="color: #800000"></i>';
                    
                    },
                "targets": 5
            },
            {
                "render": function ( data, type, row ) {
                    if(data == 1 )
                        return '<i class="fa fa-check-circle-o" style="color: #008000"></i>';
                    else if(data == 0)
                        return '<i class="fa fa-times-circle-o" style="color: #800000"></i>';
                    
                    },
                "targets": 6
            },
        ]
    });
    
} );
