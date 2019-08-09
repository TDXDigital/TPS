var epNum = $('#epNum').val();
var songCount = $('#songCountHidden').val();
var playlistCount = $('#playlistCountHidden').val();
var canConCount = $('#canconCountHidden').val();

$(document).on('change paste input', ".playlistNum", function(){

    if($(this).val().length == 4)
    {

    	if($('.songInputField').find("label[for='playlistNum']").text() != 'Playlist ID')
		return false;

    	var playlistNum = $(this).val();
    	var input = $(this);
    	$('#adPart').hide();
		$('#musicPart').show();
		$('.songInputField').find("label[for='playlistNum']").text('Playlist ID');
    	$.ajax({
            url: "/episode/searchSong/" + playlistNum,
            type: 'POST',
            }).done(function(data) {
            	var albumInfo = JSON.parse(data);
    	    	input.closest('.songInputField').find("input[name='artist']").val(albumInfo.artist);
            	input.closest('.songInputField').find("input[name='album']").val(albumInfo.album);
            	if(albumInfo.governmentCategory != undefined)
            		input.closest('.songInputField').find("[name='cat']").val(albumInfo.governmentCategory);
            	if(albumInfo.CanCon == 1)
            		input.closest('.songInputField').find("input[name='cancon']").prop('checked', true);
            	else
            		input.closest('.songInputField').find("input[name='cancon']").prop('checked', false);
            }).fail(function(data){
                alert(JSON.stringfy(data));
            });
    }

});

function setPromoVal()
{
	var inputfield = $('.songInputField');
	inputfield.find("input[name='title']").val(inputfield.find("select[name='showPromo'] option:selected").text());
	// inputfield.find("input[name='artist']").val('CKXU');
	// inputfield.find("input[name='album']").val('Advertisement');
}

function setAdVal()
{
	var inputfield = $('.songInputField');
	inputfield.find("input[name='title']").val(inputfield.find("div[name='commercial'] option:selected").text());
	inputfield.find("input[name='artist']").val('CKXU');
	inputfield.find("input[name='album']").val('Advertisement');
}

function setPsaVal()
{	
	var inputfield = $('.songInputField');
	if($('#psaCheck').is(':checked'))
	{
		inputfield.find("input[name='title']").val(inputfield.find("select[name='psaList'] option:selected").text());
	}
	else{
		inputfield.find("input[name='title']").val(inputfield.find("input[name='spokenTitle']").val());
	}

	//calculate minutes and return it
	var startPsaTime = inputfield.find("input[name='time']").val();
	var endPsaTime = new Date(1900,0,1,startPsaTime.split(":")[0],startPsaTime.split(":")[1]);
	var MinuteToAdd = inputfield.find("input[name='minutes']").val();
	$("input[name='spokenTime']").val(parseInt($("input[name='spokenTime']").val()) + parseInt(MinuteToAdd));

	// inputfield.find("input[name='playlistNum']").val('NA');
	endPsaTime.setMinutes(parseInt(endPsaTime.getMinutes()) + parseInt(MinuteToAdd));
	if($('#epType').text() != 'Live')
		inputfield.find("input[name='time']").val(("0" + endPsaTime.getHours()).slice(-2) + ':' + ("0" + endPsaTime.getMinutes()).slice(-2)) ;
	// return startPsaTime + ' ~ ' + ("0" + endPsaTime.getHours()).slice(-2) + ':' + ("0" + endPsaTime.getMinutes()).slice(-2);
	return ("0" + endPsaTime.getHours()).slice(-2) + ':' + ("0" + endPsaTime.getMinutes()).slice(-2);
}
//for insert song button listner 
$(document).on('click', '.insertBtn', function(){

	var rowid = getRowId(); 

 	var inputVal = $(this).closest('.songInputField');
 	var catValue = inputVal.find("[name='cat']").val();
 	if(catValue == null || catValue == '')
 	{
 		$('#catSelection').addClass('has-error has-feedback');
 		return false;
 	}

 	var canConChecked = inputVal.find("input[name='cancon']").is(':checked')?'checked':'';
 	var hitChecked = inputVal.find("input[name='hit']").is(':checked')?'checked':'';
 	var instChecked = inputVal.find("input[name='instrumental']").is(':checked')?'checked':'';
 	var time = inputVal.find("input[name='time']").val();
 	var endTime = time;
 	var selectedTraffic = '';

 	// if it's AD, increase the Ad count
 	if(catValue == 51)
 	{
 		$('#adCount').text(parseInt($('#adCount').text()) + 1);
		setAdVal();
		selectedTraffic = inputVal.find("div[name='commercial'] option:selected");
 	}
 	else if(catValue == 52)
 	{
 		inputVal.find("input[name='title']").val(inputVal.find("select[name='sponsorId'] option:selected").text());
 		selectedTraffic = inputVal.find("select[name='sponsorId'] option:selected");
 	}
 	else if(catValue == 53)
 	{
 		inputVal.find("input[name='title']").val(inputVal.find("select[name='sponsorPromo'] option:selected").text());
 		selectedTraffic = inputVal.find("select[name='sponsorPromo'] option:selected");
 	}
 	// if it's Promo increase PSA count
 	else if(catValue == 45)
 	{
 		$('#psaCount').text(parseInt($('#psaCount').text()) + 1);
 		setPromoVal();
 		selectedTraffic = inputVal.find("select[name='showPromo'] option:selected");
 	}

 	// if it's PSA, increase PSA count
 	else if(catValue == 11 || catValue == 12)
	{
		endTime = setPsaVal(); 	
 		if(inputVal.find("input[name='artist']").val().toUpperCase().indexOf("STATION PSA")>=0||
 			inputVal.find("input[name='title']").val().toUpperCase().indexOf("PSA")>=0)
 			$('#psaCount').text(parseInt($('#psaCount').text()) + 1);
 		selectedTraffic = inputVal.find("select[name='psaList'] option:selected");
 	}
 	else	// when insert song
 	{
 		songCount ++ ;
 		if(inputVal.find("input[name='cancon']").prop('checked'))
 		{
 			canConCount ++;
 		}
 		if(inputVal.find("input[name='playlistNum']").val()!='')
 		{
 			playlistCount ++ ;
 			$('#playlistCount').text(playlistCount);
 		}
 		$('#canconCount').text(parseInt(parseFloat(canConCount)/parseFloat(songCount) * 100));
 		localStorage.setItem("promptLog_songCount" + epNum, songCount);
 		localStorage.setItem("promptLog_playlistCount" + epNum, playlistCount);
 		localStorage.setItem("promptLog_canconCount" + epNum, canConCount);
 	}
 	// if it's live, set time to current time
 	if($('#epType').text() == 'Live')
 	{
 		var now = new Date();
 		var currentTime = ("0" + now.getHours()).slice(-2) + ':' + ("0" + now.getMinutes()).slice(-2);
		inputVal.find("input[name='time']").val(currentTime);
		endTime = inputVal.find("input[name='time']").val();
 	}	
 		

 	//Set End time
 	$("input[name='endTime']").val(endTime);
 	
 	var newTr = '<tr id="' + rowid +'">' +
			'<td>' +
			'<input type="hidden" name="row[]" value="'+ rowid +'">' +
				'<input type="text" readonly class="form-control input-sm playlistNum" name="playlistNum['+ rowid +']" id="playlistNum" placeholder="" value="'+ inputVal.find("input[name='playlistNum']").val()+'">' +
			'</td>' +
			'<td>' +
				'<input type="text" readonly  class="form-control input-sm" name="cat['+ rowid +']" placeholder="" value="'+ inputVal.find("select[name='cat']").val()+'">' +
			'</td>' +
			'<td>' +
				'<input type="time" readonly class="form-control input-sm" name="time['+ rowid +']" placeholder="" value="'+ time +'">' +
			'</td>' +
			'<td>' +
				'<input class="form-control input-sm" type="text" readonly  name="title['+ rowid +']" placeholder="Title" value="'+ inputVal.find("input[name='title']").val()+'">' + 
			'</td>' +
			'<td>' +
				'<input class="form-control input-sm" readonly name="artist['+ rowid +']" id="artist"  type="text" placeholder="Artist" value="'+ inputVal.find("input[name='artist']").val()+'">' +
			'</td>' +
			'<td>' +
				'<input class="form-control input-sm" readonly id="album" name="album['+ rowid +']" type="text" placeholder="Album" value="'+ inputVal.find("input[name='album']").val()+'">' +
			'</td>' +
			'<td>' +
				'<input class="form-control input-sm" readonly id="album" name="composer['+ rowid +']" type="text" placeholder="Composer" value="'+ inputVal.find("input[name='composer']").val()+'">' +
			'</td>' +
			'<td>' +
				'<input type="checkbox" disabled id="ccin" name="cancon['+ rowid +']" ' + canConChecked +' value="1"/>' +
			'</td>' +
			'<td>' +
				'<input type="checkbox" disabled id="hitin" name="hit['+ rowid +']" '+ hitChecked +' value="1"/>' +
			'</td>' +
			'<td>' +
				'<input type="checkbox" disabled id="insin" name="instrumental['+ rowid +']" '+ instChecked +' value="1"/>' +	
			'</td>' +
			'<td>' +
				'<input type="text" readonly readonly class="form-control input-sm" name="type['+ rowid +']" placeholder="" value="'+ inputVal.find("select[name='type']").val()+'">' +
			'</td>' +
			'<td>' +
				'<input type="text" readonly class="form-control input-sm" name="lang['+ rowid +']" placeholder="" value="'+ inputVal.find("input[name='lang']").val()+'">' +
			'</td>' +
			'<td>' +
				'<input type="hidden" class="form-control input-sm" name="note['+ rowid +']" placeholder="" value="">' +
			'</td>' +
			'<td>' +
				'<input type="button" value="Notes" class="btn btn-sm" name="NButton['+ rowid +']" onclick="GetNotes('+ rowid +');" />' +
			'</td>' +
			'<td>' +
				'<button type="button" class="btn btn-sm btn-danger rmvBtn">' +
				      '<span class="glyphicon glyphicon-trash"></span>' +
		  		'</button>' +
			'</td>' +
		'</tr>';
		if(localStorage.getItem("promptLog" + epNum) == null)
			localStorage.setItem("promptLog" + epNum, "");

		localStorage.setItem("promptLog" + epNum, localStorage.getItem("promptLog" + epNum) + newTr);
 	$('#songTable tbody tr:last').after(
 		newTr
  		);
 	checkTrafficBackingMusic(selectedTraffic, rowid);
 	clearInputField();
	// $('.songInputField').find("label[for='playlistNum']").text('Playlist ID');
});

function checkTrafficBackingMusic(selectedTraffic, rowId)
{
	var inputfield =  $('.songInputField');
	if(selectedTraffic == '' || selectedTraffic == null || selectedTraffic == undefined)
		return;
	if(inputfield.find("input[name='playlistNum']").val() == '')
		return
	var title = selectedTraffic.data('song');
	var artist = selectedTraffic.data('artist');
	var album = selectedTraffic.data('album');

	if(title != '' && artist!= '' && album!= '' && title != undefined && artist!= undefined && album!= undefined)
	{
		$('#songTable').find("input[name='note["+ rowId +"]']").val('Backing Music Title: ' + title + ', Artist: ' + artist + ', Album: ' + album);
        $('#songTable').find("input[name='NButton["+ rowId +"]']").addClass('btn-info');
	}
}

function getRowId()
{
	var nextRowId = parseInt($('#songTable tbody tr:last').attr('id')) + 1;
	return nextRowId;
}
function clearInputField(){
	var inputfield =  $('.songInputField');
	inputfield.find("input[type='number']").val('');
	inputfield.find("input[type='text']").val('');
	inputfield.find("input[type='checkbox']").prop('checked', false);
	// inputfield.find("select").val('');
	inputfield.find("[name='type']").val('NA');
	 $('#psaCheck').trigger('change');
}

//event for remove button from the song table
$(document).on('click', '.rmvBtn', function(e){
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
});


//event for remove button from the song table
$(document).on('click', '.editBtn', function(e){
	//First, enable select option and checkbox to get values
	$('#songTable').find("[type='checkbox']").attr("disabled", false);
	$('#songTable').find('select,option').attr("disabled", false);
	$('#songTable').find('input').attr("readonly", false);
	$("input[name='spokenTime']").attr("readonly", false);
	$(this).find('span').text(' Confirm');
	$(this).removeClass('editBtn');
	$(this).addClass('confirmBtn');
});

$(document).on('click', '.confirmBtn', function(e){
	$('#songTable').find("[type='checkbox']").attr("disabled", true);
	$('#songTable').find('select,option').attr("disabled", true);
	$('#songTable').find('input').attr("readonly", true);
	$("input[name='spokenTime']").attr("readonly", true);
	$(this).find('span').text(' Edit');
	$(this).removeClass('confirmBtn');
	$(this).addClass('editBtn');
});


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
$(document).on('keyup', '.songInputField input', function(e){
	if(e.keyCode == 13)
    {
        // $(this).trigger("enterKey");
        $(this).closest('.songInputField').find(".insertBtn").trigger('click');
    }
});


// enable all checkbox to get values
$(document).on('submit','#episodeForm',function(){
	localStorage.setItem("promptLog" + epNum, "");
	localStorage.setItem("promptLog_songCount" + epNum, 0);
	localStorage.setItem("promptLog_playlistCount" + epNum, 0);
	localStorage.setItem("promptLog_canconCount" + epNum, 0);
	
	$(this).find("[type='checkbox']").attr("disabled", false);
	$(this).find('select,option').attr("disabled", false);
	var allVals = $('input[type="checkbox"]:checked').map(function () {
    return this.value
}).get();

});

function hideAll()
{
	$('#adPart').hide();
	$('#psaPart').hide();
	$('#musicPart').hide();
	$('#showPromoPart').hide();
	$('#sponsorIDPart').hide();
	$('#sponsorPromoPart').hide();
}

$(document).on('change', '.chtype', function(e){
	$('#catSelection').removeClass('has-error has-feedback');
	hideAll();
	var catNum = $(this).val();
	var row = $(this).closest('tr');
	var rowid = row.attr('id');

	switch(catNum)
	{
		case '45':
			clearInputField();
			$('.songInputField').find("label[for='playlistNum']").text('Ad ID');
			$('#showPromoPart').show();
			break;
		case '51':
			clearInputField();
			$('.songInputField').find("[name='cat']").val('51');
			$('#adPart').show();
			$('.songInputField').find("label[for='playlistNum']").text('Ad ID');
			break;
		case '52':
			clearInputField();
			$('.songInputField').find("[name='cat']").val('52');
			$('#sponsorIDPart').show();
			$('.songInputField').find("label[for='playlistNum']").text('Ad ID');
			break;
		case '53':
			clearInputField();
			$('.songInputField').find("[name='cat']").val('53');
			$('#sponsorPromoPart').show();
			$('.songInputField').find("label[for='playlistNum']").text('Ad ID');
			break;

		case '12':

		case '11':
			clearInputField();
			$('.songInputField').find("label[for='playlistNum']").text('Ad ID');
			$('#psaPart').show();
			break;
		default:
			$('#musicPart').show();
			$('.songInputField').find("label[for='playlistNum']").text('Playlist ID');

	}



});


$('#psaCheck').change(function(){
    this.checked ? $('#psaInput').show() : $('#psaInput').hide();
    this.checked ? $('#spokenInput').hide() : $('#spokenInput').show();
});


$(document).on('change', '.adch', function(e){
	$('.songInputField').find("input[name='playlistNum']").val($(this).val());
});

function popitup(url) {
    //opens a new window of size 500x300 (portorate for category listing)
	newwindow=window.open(url,'name','height=500,width=300');
	if (window.focus) {newwindow.focus()}
		return false;
}

function GetNotes(rowId) {
    var NOTE = prompt("Short Notes Regarding current song (90 char max)", $('#songTable').find("input[name='note["+ rowId +"]']").val());
    if (NOTE != null && NOTE != '') {
        $('#songTable').find("input[name='note["+ rowId +"]']").val(NOTE);
        $('#songTable').find("input[name='NButton["+ rowId +"]']").addClass('btn-info');
    }

    // $("NoteField").slideDown();
}







// Drawing Clock functions //

var canvas = document.getElementById("canvas");
var ctx = canvas.getContext("2d");
var radius = canvas.height / 2;
ctx.translate(radius, radius);
radius = radius * 0.90
setInterval(drawClock, 1000);

function drawClock() {
  drawFace(ctx, radius);
  drawNumbers(ctx, radius);
  drawTime(ctx, radius);
}

function drawFace(ctx, radius) {
  var grad;
  ctx.beginPath();
  ctx.arc(0, 0, radius, 0, 2*Math.PI);
  ctx.fillStyle = 'white';
  ctx.fill();
  grad = ctx.createRadialGradient(0,0,radius*0.95, 0,0,radius*1.05);
  grad.addColorStop(0, '#333');
  grad.addColorStop(0.5, 'white');
  grad.addColorStop(1, '#333');
  ctx.strokeStyle = grad;
  ctx.lineWidth = radius*0.1;
  ctx.stroke();
  ctx.beginPath();
  ctx.arc(0, 0, radius*0.1, 0, 2*Math.PI);
  ctx.fillStyle = '#333';
  ctx.fill();
}

function drawNumbers(ctx, radius) {
  var ang;
  var num;
  ctx.font = radius*0.15 + "px arial";
  ctx.textBaseline="middle";
  ctx.textAlign="center";
  for(num = 1; num < 13; num++){
    ang = num * Math.PI / 6;
    ctx.rotate(ang);
    ctx.translate(0, -radius*0.85);
    ctx.rotate(-ang);
    ctx.fillText(num.toString(), 0, 0);
    ctx.rotate(ang);
    ctx.translate(0, radius*0.85);
    ctx.rotate(-ang);
  }
}

function drawTime(ctx, radius){
    var now = new Date();
    var hour = now.getHours();
    var minute = now.getMinutes();
    var second = now.getSeconds();
    //hour
    hour=hour%12;
    hour=(hour*Math.PI/6)+
    (minute*Math.PI/(6*60))+
    (second*Math.PI/(360*60));
    drawHand(ctx, hour, radius*0.5, radius*0.07);
    //minute
    minute=(minute*Math.PI/30)+(second*Math.PI/(30*60));
    drawHand(ctx, minute, radius*0.8, radius*0.07);
    // second
    second=(second*Math.PI/30);
    drawHand(ctx, second, radius*0.9, radius*0.02);
}

function drawHand(ctx, pos, length, width) {
    ctx.beginPath();
    ctx.lineWidth = width;
    ctx.lineCap = "round";
    ctx.moveTo(0,0);
    ctx.rotate(pos);
    ctx.lineTo(0, -length);
    ctx.stroke();
    ctx.rotate(-pos);
}

// Drawing Clock functions done //



    //back link
    $(document).ready(function(){
    $('a.back').click(function(){
        parent.history.back();
        return false;
    });
});



// use localstorage for prompt log - song insertion
// incase of refresh page, keep the form data
$(document).ready(function(){
   	if(localStorage.getItem("promptLog" + epNum) != null)
   	{
   		$('#songTable tbody tr:last').after(
 			localStorage.getItem("promptLog" + epNum)
  		);	
   	}
   	if(localStorage.getItem("promptLog_songCount" + epNum) != null && localStorage.getItem("promptLog_songCount" + epNum) != '0')
   		songCount = localStorage.getItem("promptLog_songCount" + epNum);
   	if(localStorage.getItem("promptLog_playlistCount" + epNum) != null && localStorage.getItem("promptLog_playlistCount" + epNum) != '0')
   	{
   		playlistCount = localStorage.getItem("promptLog_playlistCount" + epNum);
   	}
   	if(localStorage.getItem("promptLog_canconCount" + epNum) != null && localStorage.getItem("promptLog_canconCount" + epNum) != '0')
   	{
   		canConCount = localStorage.getItem("promptLog_canconCount" + epNum);
   	}
   	$('#playlistCount').text(playlistCount);
   	if(songCount != 0)
		$('#canconCount').text(parseInt(parseFloat(canConCount)/parseFloat(songCount) * 100));
});