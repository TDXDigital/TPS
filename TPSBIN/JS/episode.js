 var rowid = 1;

$(document).on('change paste input', ".playlistNum", function(){
 
    if($(this).val().length == 4)
    {
    	var playlistNum = $(this).val();
    	var input = $(this);
    	$.ajax({
            url: "./searchSong/" + playlistNum,
            type: 'POST',
            }).done(function(data) {
            	var albumInfo = JSON.parse(data);
            	var rowId = input.closest('tr').attr('id');

    	    	input.closest('tr').find("input[name='artist["+ rowId +"]']").val(albumInfo.artist);
            	input.closest('tr').find("input[name='album["+ rowId +"]']").val(albumInfo.album);
            	input.closest('tr').find("[name='cat["+ rowId +"]']").val(albumInfo.governmentCategory) ;
            	if(albumInfo.CanCon == 1)
            		input.closest('tr').find("input[name='cancon["+ rowId +"]']").prop('checked', true);
            	else
            		input.closest('tr').find("input[name='cancon["+ rowId +"]']").prop('checked', false);
            }).fail(function(data){
                alert(JSON.stringfy(data));
            });
    }

});

//for insert song button listner 
$(document).on('click', '.insertBtn', function(){
 	$(this).prop('value', 'Delete');
 	$(this).removeClass('btn-success insertBtn');
 	$(this).addClass('btn-danger rmvBtn');
 	$(this).on('click', removeRow);
 	$(this).closest('tr').find('td').each(function(){
 		// alert($(this).find('input, select').val());
 		$(this).find('input,select,option').attr("readonly", true);
 		$(this).find("[type='checkbox']").attr("disabled", true);
 		$(this).find('select,option').attr("disabled", true);
 	});
 	$('#songTable tbody tr:last').after(

 		'<tr id="' + ++rowid +'">' +
			'<td>' +
			'<input type="hidden" name="row[]" value="'+ rowid +'">' +
				'<input type="text" class="form-control playlistNum" name="playlistNum['+ rowid +']" id="playlistNum" placeholder="">' +
			'</td>' +
			'<td>' +
				'<select name="cat['+ rowid +']" id="DDLNormal" class="form-control" onchange="CHtype()">' +
	               '<option value="53"> 53, Sponsored Promotion</option>' +
	               '<OPTION value="52"> 52, Sponsor Indentification</OPTION>' +
	               '<OPTION VALUE="51"> 51, Commercial</OPTION>' +
	               '<option value="45"> 45, Show Promo</option>' +
	               '<option value="44"> 44, Programmer/Show ID</option>' +
	               '<option value="43"> 43, Musical Station ID</option>' +
	               '<option value="42"> 42, Tech Test</option>' +
	               '<option value="41"> 41, Themes</option>' +
	               	'<option value="36"> 36, Experimental</option>' +
	               	'<option value="35"> 35, NonClassical Religious</option>' +
	               	'<option value="34"> 34, Jazz and Blues</option>' +
	               	'<option value="33"> 33, World/International</option>' +
	               	'<option value="32"> 32, Folk</option>' +
	               	'<option value="31"> 31, Concert</option>' +
	               	'<option value="24"> 24, Easy Listening</option>' +
	               	'<option value="23"> 23, Acoustic</option>' +
	               	'<option value="22"> 22, Country</option>' +
	               	'<option value="21"> 21, Pop, Rock and Dance</option>' +
	               '<option value="12"> 12, PSA/Spoken Word Other</option>' +
	               '<OPTION VALUE="11"> 11, News</option>' +
	               '<OPTION VALUE=""></option>' +

               	'</select>' +
			'</td>' +
			'<td>' +
				'<input type="time" class="form-control" name="time['+ rowid +']" placeholder="">' +
			'</td>' +
			'<td>' +
				'<input class="form-control" type="text" name="title['+ rowid +']" placeholder="Title">' + 
			'</td>' +
			'<td>' +
				'<input class="form-control" name="artist['+ rowid +']" id="artist"  type="text" placeholder="Artist">' +
			'</td>' +
			'<td>' +
				'<input class="form-control" id="album" name="album['+ rowid +']" type="text" placeholder="Album">' +
			'</td>' +
			'<td>' +
				'<input class="form-control" id="album" name="composer['+ rowid +']" type="text" placeholder="Composer">' +
			'</td>' +
			'<td>' +
				'<input type="checkbox" id="ccin" name="cancon['+ rowid +']" value="1"/>' +
			'</td>' +
			'<td>' +
				'<input type="checkbox" id="hitin" name="hit['+ rowid +']" value="1"/>' +
			'</td>' +
			'<td>' +
				'<input type="checkbox" id="insin" name="instrumental['+ rowid +']" value="1"/>' +	
			'</td>' +
			'<td>' +
				'<select name="type['+ rowid +']" id="type" class="form-control">' +
		           '<option value="NA"> ---</option>' +
		           '<option value="BACKGROUND">BG</option>' +
		           '<option value="THEME"> TH</option>' +
	       		'</select>' +
			'</td>' +
			'<td>' +
				'<input list="lang" name="lang['+ rowid +']" required value="English" size="10" maxlength="40" class="form-control"/>' +
	           	'<datalist id="lang">' +
	           		'<option value="English">' +
	           		'<option value="French">' +
	          	'</datalist>' +
			'</td>' +
			'<td>' +
				'<input id="insertSong" type="button" class="btn btn-success insertBtn" value="Insert"/>' +
			'</td>' +
		'</tr>' 
 		);
});



function removeRow(event)
{
	// event.stopImmediatePropagation();
	$(this).closest ('tr').remove();
}



//Prevent enter key to submit form
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});


//Enter key to insert new song
$(document).on('keyup', '#songTable input', function(e){
	if(e.keyCode == 13)
    {
        // $(this).trigger("enterKey");
        $(this).closest('tr').find(".insertBtn").trigger('click');
    }
});


$(document).on('submit','#episodeForm',function(){
	$(this).find("[type='checkbox']").attr("disabled", false);
	$(this).find('select,option').attr("disabled", false);
	var allVals = $('input[type="checkbox"]:checked').map(function () {
    return this.value
}).get();

});