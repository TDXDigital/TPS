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
            	if(albumInfo.governmentCategory != undefined)
            		input.closest('tr').find("[name='cat["+ rowId +"]']").val(albumInfo.governmentCategory);
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

 	var catValue = $(this).closest('tr').find("[name='cat["+ rowid +"]']").val();
 	var row = $(this).closest('tr');
 	// if it's AD, increase the Ad count
 	if(catValue == 51)
 	{
 		$('#adCount').text(parseInt($('#adCount').text()) + 1);

			row.find("input[name='title["+ rowid +"]']").val(row.find("div[name='commercial["+ rowid +"]'] option:selected").text()).show();
			row.find("input[name='artist["+ rowid +"]']").val('CKXU').show();
			row.find("input[name='album["+ rowid +"]']").val('Advertisement').show();
			row.find("input[name='composer["+ rowid +"]']").show();
			row.find("input[name='cancon["+ rowid +"]']").show();
			row.find("input[name='hit["+ rowid +"]']").show();
			row.find("input[name='instrumental["+ rowid +"]']").show();
			row.find("select[name='type["+ rowid +"]']").show();
			row.find("input[name='lang["+ rowid +"]']").show();


			row.find("div[name='commercial["+ rowid +"]']").hide();

 	}

 	// if it's Promo increase PSA count
 	if(catValue == 45)
 		$('#psaCount').text(parseInt($('#psaCount').text()) + 1);
 	// if it's PSA, increase PSA count
 	if(catValue == 11 || catValue == 12)
 		if($(this).closest('tr').find("input[name='artist["+ rowid +"]']").val().toUpperCase().indexOf("STATION PSA")>=0||
 			$(this).closest('tr').find("input[name='title["+ rowid +"]']").val().toUpperCase().indexOf("PSA")>=0)
 			$('#psaCount').text(parseInt($('#psaCount').text()) + 1);

 	// append table row
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
				'<input type="text" class="form-control input-sm playlistNum" name="playlistNum['+ rowid +']" id="playlistNum" placeholder="">' +
			'</td>' +
			'<td>' +
				'<select name="cat['+ rowid +']" id="DDLNormal" class="form-control input-sm chtype" onchange="CHtype()">' +
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
				'<input type="time" class="form-control input-sm" name="time['+ rowid +']" placeholder="">' +
			'</td>' +
			'<td>' +
				'<input class="form-control input-sm" type="text" name="title['+ rowid +']" placeholder="Title">' + 
				'<div name="commercial['+ rowid +']" style="display: none">'+
					commercialOption +
				'</div>'+
			'</td>' +
			'<td>' +
				'<input class="form-control input-sm" name="artist['+ rowid +']" id="artist"  type="text" placeholder="Artist">' +
			'</td>' +
			'<td>' +
				'<input class="form-control input-sm" id="album" name="album['+ rowid +']" type="text" placeholder="Album">' +
			'</td>' +
			'<td>' +
				'<input class="form-control input-sm" id="album" name="composer['+ rowid +']" type="text" placeholder="Composer">' +
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
				'<select name="type['+ rowid +']" id="type" class="form-control input-sm">' +
		           '<option value="NA"> ---</option>' +
		           '<option value="BACKGROUND">BG</option>' +
		           '<option value="THEME"> TH</option>' +
	       		'</select>' +
			'</td>' +
			'<td>' +
				'<input list="lang" name="lang['+ rowid +']" required value="English" size="10" maxlength="40" class="form-control input-sm"/>' +
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
	//First, enable select option and checkbox to get values
	$(this).closest('tr').find("[type='checkbox']").attr("disabled", false);
	$(this).closest('tr').find('select,option').attr("disabled", false);

	var rowid = $(this).closest('tr').attr('id');
	var catValue = $(this).closest('tr').find("[name='cat["+ rowid +"]']").val();

	// if it's AD, decrease the Ad count
 	if(catValue == 51)
 		$('#adCount').text(parseInt($('#adCount').text()) - 1);
 	// if it's Promo decrease PSA count
 	if(catValue == 45)
 		$('#psaCount').text(parseInt($('#psaCount').text()) - 1);
 	// if it's PSA, decrease PSA count
 	if(catValue == 11 || catValue == 12)
 		if($(this).closest('tr').find("input[name='artist["+ rowid +"]']").val().toUpperCase().indexOf("STATION PSA")>=0||
 			$(this).closest('tr').find("input[name='title["+ rowid +"]']").val().toUpperCase().indexOf("PSA")>=0)
 			$('#psaCount').text(parseInt($('#psaCount').text()) - 1);


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


$(document).on('change', '.chtype', function(e){

	var catNum = $(this).val();
	var row = $(this).closest('tr');
	var rowid = row.attr('id');

	switch(catNum)
	{
		case '51':
			row.find("input[name='playlistNum["+ rowid +"]']").attr("placeholder", "Ad ID");
			row.find("input[name='title["+ rowid +"]']").hide();
			row.find("input[name='artist["+ rowid +"]']").hide();
			row.find("input[name='album["+ rowid +"]']").hide();
			row.find("input[name='composer["+ rowid +"]']").hide();
			row.find("input[name='cancon["+ rowid +"]']").hide();
			row.find("input[name='hit["+ rowid +"]']").hide();
			row.find("input[name='instrumental["+ rowid +"]']").hide();
			row.find("select[name='type["+ rowid +"]']").hide();
			row.find("input[name='lang["+ rowid +"]']").hide();
			row.find("div[name='commercial["+ rowid +"]']").show();
			break;

		case '11':
		case '12':
			row.find("input[name='title["+ rowid +"]']").val('Spoken Word / Talk');
			row.find("input[name='composer["+ rowid +"]']").hide();
			row.find("input[name='cancon["+ rowid +"]']").hide();
			row.find("input[name='hit["+ rowid +"]']").hide();
			row.find("input[name='instrumental["+ rowid +"]']").hide();
			row.find("select[name='type["+ rowid +"]']").hide();
			row.find("input[name='lang["+ rowid +"]']").hide();
			break;
		default:
			row.find("input[name='playlistNum["+ rowid +"]']").attr("placeholder", "");
			row.find("input[name='title["+ rowid +"]']").val('').show();
			row.find("input[name='artist["+ rowid +"]']").show();
			row.find("input[name='album["+ rowid +"]']").show();
			row.find("input[name='composer["+ rowid +"]']").show();
			row.find("input[name='cancon["+ rowid +"]']").show();
			row.find("input[name='hit["+ rowid +"]']").show();
			row.find("input[name='instrumental["+ rowid +"]']").show();
			row.find("select[name='type["+ rowid +"]']").show();
			row.find("input[name='lang["+ rowid +"]']").show();
			row.find("div[name='commercial["+ rowid +"]']").hide();

	}



});

$(document).on('change', '.adch', function(e){
	// alert($(this).val());
	var row = $(this).closest('tr');
	var rowid = row.attr('id');
	row.find("input[name='playlistNum["+ rowid +"]']").val($(this).val());
});

function asd(){

	alert($(this).val());
		if(document.getElementById("DDLNormal").options[document.getElementById("DDLNormal").selectedIndex].value==51){
			$("#inputdiv").hide();
			$("#processing").hide();
			document.getElementById("DDLAdvert").options[document.getElementById("DDLNormal").selectedIndex].selected = true;
			$("#InputAdvert").show();
			document.getElementById("AdNum").value = document.getElementById("ADLis").options[document.getElementById("ADLis").selectedIndex].value;
			document.getElementById("plhead").style.display="inline";
			document.getElementById("spokenc").style.display="none";
			document.getElementById("plbody").style.display="inline";
			document.getElementById("spokcon").style.display="none";
			//document.getElementById("Spokcon").required="true";

			document.getElementById("title001").value="";
			document.getElementById("artin").disabled=false;
			document.getElementById("albin").disabled=false;
			document.getElementById("ccin").disabled=false;
			document.getElementById("hitin").disabled=false;
			document.getElementById("insin").disabled=false;
		}
		else if(document.getElementById("DDLNormal").options[document.getElementById("DDLNormal").selectedIndex].value==12 || document.getElementById("DDLNormal").options[document.getElementById("DDLNormal").selectedIndex].value==11){

			document.getElementById("plhead").style.display="none";
			document.getElementById("spokenc").style.display="inline";
			document.getElementById("plbody").style.display="none";
			document.getElementById("spokcon").style.display="inline";
			//document.getElementById("Spokcon").required="true";

			//document.getElementById("title001").value="";//Spoken Word / News / ID
			document.getElementById("data1").style.display="inline";
			document.getElementById("data1").disabled=false;
			document.getElementById("title001").style.display="none";
			document.getElementById("title001").disabled="true";
			//alert("TRIGGERED");
			//document.getElementById("artin").disabled="true";
			//document.getElementById("albin").disabled="true";
			//document.getElementById("ccin").disabled="true";
			//document.getElementById("hitin").disabled="true";
			//document.getElementById("insin").disabled="true";
		}
		else if(document.getElementById("DDLNormal").options[document.getElementById("DDLNormal").selectedIndex].value!=12 || document.getElementById("DDLNormal").options[document.getElementById("DDLNormal").selectedIndex].value!=11){

			document.getElementById("plhead").style.display="inline";
			document.getElementById("spokenc").style.display="none";
			document.getElementById("plbody").style.display="inline";
			document.getElementById("spokcon").style.display="none";
			//document.getElementById("Spokcon").required="true";

			//document.getElementById("title001").value="";
			document.getElementById("artin").disabled=false;
			document.getElementById("albin").disabled=false;
			document.getElementById("ccin").disabled=false;
			document.getElementById("hitin").disabled=false;
			document.getElementById("insin").disabled=false;

			document.getElementById("data1").style.display="none";
			document.getElementById("data1").disabled="true";
			document.getElementById("title001").style.display="inline";
			document.getElementById("title001").disabled=false;
		}
		else{
			$("#InputAdvert").hide();
			$("#InputSponsor").hide();
		}
	}
