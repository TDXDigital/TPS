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
                        recommended: $('#filter_recommended').val(),
                        expiry : $('#filter_expiry').val(),
                        missing: $('#filter_missing').val()            
                        }
                    }
        },
        "rowId": 'ShortCode',
        "columns": [
            {
                "orderable":      true,
                "data":         {   
                                    "ShortCode" : "ShortCode"
                                },
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
                "orderable":      true,
                "data":           "endDate",
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
        "render": function (data, type, row) {
            
            var id = data.ShortCode;

            if(data.ShortCode.length == 3){
                id = "0" + data.ShortCode;
            }
            if(data.missing == 1)
            {
                return '<input type="checkbox" name="bulkEditId[]" style="visibility:hidden" value="'+data.refCode+'"/>' + 
                '<a href="#" class="missingAlbum">'+ id.toString() +'</a>';
            }
            else   
                return '<input type="checkbox" name="bulkEditId[]" style="visibility:hidden" value="'+data.refCode+'"/>' + id.toString();
        },
            "targets": 0,
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
			"targets" : 8
	    },
	    { className: "centeredTD", targets: 5}
	],
    });

    enableFilter(playlistTable);
    filterClear(playlistTable);
    rowSelection(playlistTable);
    missingAlbumLink(playlistTable);
    $('#playlist_table thead th').removeClass('centeredTD');
    $('#playlist_table tfoot th').removeClass('centeredTD');

    
} );

function rowSelection(playlistTable)
{
    playlistTable.on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
        var checked = $(':checkbox', $(this).closest('tr')).prop('checked', function(index, attr){
            if(attr){
                $('#checkAll').prop('checked', false);
                return false;
            }
            return true;
        });
    } );    
}


function missingAlbumLink(playlistTable)
{
     playlistTable.on( 'click', '.missingAlbum', function () {
        var id = $(this).closest('tr').attr('id')

         $.ajax({
        url: "./lastProg/"+id,
            type: 'GET',
        }).done(function(data) {
            // var obj = JSON.parse(data);
            alert(data);
            //location.reload();
        }).fail(function(data){
            alert(data.status);
        });
    });    
}
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

$(document).ready(function()
{
    //All Check box for batch edit
    $("#checkAll").click(function() {
        if($(this).is(':checked'))
        {
            $('#playlist_table .selected').click();
            $('#playlist_table tr').click();
            $('#checkAll').prop('checked', true);
        }
        else
        {
            $('#playlist_table .selected').click();
        }
    });   
});











function toggle(source) {
    checkboxes = document.getElementsByName('bulkEditId[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
      checkboxes[i].checked = source.checked;
    }
  }


function batchChange(element, url){
    console.log(element);
    var val = element.val();
    if(!val){
        alert (val);
        return true;
    }
    console.log(val);

    var batch = $.ajax({
        url:"../batch/options/"+val,
        dataType: "json"
    })
    .done(function(data){
        if ( console && console.log ) {
            console.log( data );
        }
        if($.type(data) === "boolean"){
            $("#batchExtension").html("");
            return true;
        }
        if ( console && console.log ) {
            console.log($.type(data));
            console.log(data.inputType);
            console.log(data.values);
        }
        if(data.inputType==="select"){
            $("#batchExtension").html(
                "<select name='value' id='beval' required>"+
                "<option>Select</option></select>");
            $.each(data.values, function(key, value){
                $("#beval")
                    .append($("<option></option>")
                    .attr("value", key)
                    .text(value));
            });
        }
        else if(data.inputType==="attribute"){
            $("#batchExtension").html(
                "Attribute: <select name='value' id='beval' required>"+
                "<option>Select</option></select>"+
                "&nbsp;<span id='beattr'></span>");
            $.each(data.values, function(key, value){
                $("#beval")
                    .append($("<option></option>")
                    .attr("value", key)
                    .attr("class", value.input)
                    .text(value.value));
            });

        }
        changeAttrOptions("#beval option:selected", "#beattr");
        //$("#batchExtension").html("THIS IS A TEST");
    })
    .fail(function(data){
        if ( console && console.log ) {
            console.log( data );
        }
    });

}



