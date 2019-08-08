


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
            { "data": "Playcount" },
        ],
        "order": [[ 3, "desc" ]],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    return '<button type="button" onclick="location.href=\'/traffic/edit/'+data+'\';" class="btn btn-default btn-xs">' +
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
        ]
    });
    
} );
