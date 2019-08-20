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
    var labels = $('#labels').val();
    
    var table = $('#data_table').DataTable({
        "processing": true,
        "serverSide": true,
        "select": true,
        "ajax": {
            "url": "/review/print/display", 
            "data": function(d) {
                    
                    d.filter = {
                        date: $('#startDate').val(),
                        }
                 }
        },
        "columns": [
            { "data": "reviewer" },
            { "data": "album" },
            { "data": "artist" },
            { "data": "ts" },
            { "data": "id" },
        ],
        "columnDefs": [
            {

                "render": function ( data, type, row ) {

                    var disabledAdd = '';
                    var disabledrmv = '';
                    try
                    {
                        if(JSON.parse(labels).filter(item => item == data) == data)
                            disabledAdd = 'disabled';
                        else
                            disabledrmv = 'disabled'; 
                    }
                    catch {

                    }

                    

                    // alert('test');
                    return "<button id='enb"+ data + "' "+ disabledAdd +" type='button' "+
                		"class='btn btn-success addBtn' " + 
                		"onclick='javascript: addLabel(parseInt(" + data +"));'> "+
                		"<i class='fa fa-plus-square'></i> Add</button> "+
                		"<button id='dsb"+ data +"' "+ disabledrmv + " type='button' "+ 
                		"class='btn btn-danger rmvBtn' " +  
                		"onclick='javascript: removeLabel(parseInt("+ data +"));'> "+
                		"<i class='fa fa-minus-square'></i> Remove</button> ";	
                    },
                "orderable": false,
                "targets": 4
            },
        ]
    });
    $('.table_filter').on('change', function() {
        table.draw();
    });
} );
