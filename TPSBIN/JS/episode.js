$(document).on('change paste input', "[name='playlistNum[]']", function(){
 
    if($(this).val().length == 4)
    {
    	var playlistNum = $(this).val();
    	var input = $(this);
    	$.ajax({
            url: "./searchSong/" + playlistNum,
            type: 'POST',
            }).done(function(data) {
            	var albumInfo = JSON.parse(data);
            	input.closest('tr').find("input[name=artist]").val(albumInfo.artist);
            	input.closest('tr').find("input[name=album]").val(albumInfo.album);
            	input.closest('tr').find('input[name=DDLNormal] option[value="'+albumInfo.governmentCategory +'"]')
            	if(albumInfo.CanCon == 1)
            		input.closest('tr').find("input[name=cancon]").prop('checked', true);
            	else
            		input.closest('tr').find("input[name=cancon]").prop('checked', false);
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

 	$('#songTable tbody tr:last').after(

 		'<tr>' +
			'<td>' +
				'<input type="text" class="form-control" name="playlistNum[]" id="playlistNum" placeholder="">' +
			'</td>' +
			'<td>' +
				'<select name="cat" id="DDLNormal" name="DDLNormal" class="form-control" onchange="CHtype()">' +
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

               	'</select>' +
			'</td>' +
			'<td>' +
				'<input type="time" class="form-control" name="time" id="" placeholder="">' +
			'</td>' +
			'<td>' +
				'<input class="form-control" type="text" name="title" placeholder="Title">' + 
			'</td>' +
			'<td>' +
				'<input class="form-control" id="artist" name="artist" type="text" placeholder="Artist">' +
			'</td>' +
			'<td>' +
				'<input class="form-control" id="album" name="album" type="text" placeholder="Album">' +
			'</td>' +
			'<td>' +
				'<input class="form-control" id="album" name="composer" type="text" placeholder="Composer">' +
			'</td>' +
			'<td>' +
				'<input type="checkbox" id="ccin" name="cancon" value="1"/>' +
			'</td>' +
			'<td>' +
				'<input type="checkbox" id="hitin" name="hit" value="1"/>' +
			'</td>' +
			'<td>' +
				'<input type="checkbox" id="insin" name="instrumental" value="1"/>' +	
			'</td>' +
			'<td>' +
				'<select name="type" id="type" class="form-control">' +
		           '<option value="NA"> ---</option>' +
		           '<option value="BACKGROUND">BG</option>' +
		           '<option value="THEME"> TH</option>' +
	       		'</select>' +
			'</td>' +
			'<td>' +
				'<input list="lang" name="lang" required value="English" size="10" maxlength="40" class="form-control"/>' +
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
