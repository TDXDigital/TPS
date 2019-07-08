/*
 * The MIT License
 *
 * Copyright 2016 J.oliver.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */




// Load and render the data table
$(document).ready(function() {
    var table = $('#data_table').DataTable({
        "processing": true,
        "serverSide": true,
        "select": true,
        "ajax": {
            "url": "/host/display", 
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
            { "data": "djname" },
            { "data": "alias" },
            { "data": "alias" },
            { "data": "active" },
            { "data": "years" },
            { "data": "weight" },
        ],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    return '<button type="button" onclick="location.href=\'/host/edit/'+data+'\';" class="btn btn-default btn-xs">' +
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
                "targets": 3
            },
        ]
    });
    
} );
