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

// Load and render the data tables
$(document).ready(function() {
    var playlistTable = $('#playlist_table').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "/playlist/display-playlist", 
            "data": function(d) {
                    d.filter = {
                        recommended: $('#filter_recommended').val()
                        }
                    }
        },
        "columns": [
            {
                "class":          "details-control",
                "orderable":      true,
                "data":           "ShortCode",
                "defaultContent": "",
            },
            {
                "orderable":      true,
                "data":           "artist",
                "defaultContent": "",
            },
            {
                "orderable":      false,
                "data":           "album",
                "defaultContent": "",
            },
            {
                "orderable":      false,
                "data":           "subgenres",
                "defaultContent": "",
            },
            {
                "orderable":      false,
                "data":           "hometowns",
                "defaultContent": "",
            },
            {
                "orderable":      false,
                "data":           "rating",
                "defaultContent": "",
            },
            {
                "orderable":      true,
                "data":           "addDate",
                "defaultContent": "",
            },
            {
                "orderable":      false,
                "data":           "playlistID",
                "defaultContent": "",
            },
        ],
	"columnDefs": [
        {
        "render": function(data, type, row) {
            if(data.length == 3)
                data = "0" + data;
            return data;
        },
            "targets" : 0
        },
	    {
		"render": function(data, type, row) {
			return cellBulletPoints(data);
		},
			"targets" : 3
	    },
	    {
		"render": function(data, type, row) {
			return cellBulletPoints(data);
		},
			"targets" : 4
	    },
	    {
		"render": function(data, type, row) {
			if (data == 0 || data == null)
			    return '';
			tag = '<img class="star" style="width: 25px;" src="../../images/';
			if (data < 4)
			    tag += 'not_';
			return tag + 'recommended.png" />';
		},
			"targets" : 5
	    },
	    {
		"render": function(data, type, row) {
			return '<a href="./' + data + '"><i class="fa fa-edit"></i></a>';
		},
			"targets" : 7
	    },
	    { className: "centeredTD", targets: 5}
	],
    });

    enableFilter(playlistTable);
    filterClear(playlistTable);
    $('#playlist_table thead th').removeClass('centeredTD');
    $('#playlist_table tfoot th').removeClass('centeredTD');
} );

function cellBulletPoints(data)
{
    td = "";
    if (data != null)
	for (var i = 0; i < data.length; i++)
            if (data[i].length > 0)
	        td += "-" + data[i] + "<br />";
    return td;
}


function filterClear(table)
{
    $( "#clear_filter" ).click(function() {
      $(".table_filter").prop('selectedIndex', 0);
      table.draw();
    });
}

function enableFilter(table)
{
    $('.table_filter').on('change', function() {
        table.draw();
    });
}
