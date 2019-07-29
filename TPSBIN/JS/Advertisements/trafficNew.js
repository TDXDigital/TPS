

$( document ).ready(function() {
    $('#Category').trigger('change');
    $('#backingTrack').trigger('change');
    $('#friend').trigger('change');
    $('#psa').trigger('change');
});


$('#backingTrack').change(function(){
    this.checked ? $('#musicChecked').show() : $('#musicChecked').hide();
});


$('#friend').change(function(){
    this.checked ? $('#playCountPart').hide() : $('#playCountPart').show();
});


$('#psa').change(function(){
    if(this.checked)
    {
    	$('#psaChecked').show();
    	$('#clientPart').hide();
    }
    else
    {
    	$('#psaChecked').hide();
    	$('#clientPart').show();
    }
});

$('#Category').change(function(){
	hideAll();
    var cat = $(this).val();
    switch(cat)
    {
    	case "12":
    		$('#psaChckBox').show();
    		$('#psaClientPart').show();
			$('#musicPart').show();
			$('#spokenPart').show();
    		break;

    	case "43":
    		$('#musicPart').show();
    		$('#musicLanguage').show();
    		break;

    	case "44":

    	break;

    	case "45":
    		$('#showPromoPart').show();
    		$('#musicPart').show();
	    	break;

		case "51":

			if($('#psa').is(':checked'))
			{
				$('#psa').prop('checked', false);
				$('#psa').trigger('change');
			}
			$('#psaClientPart').show();
			$('#paidAdPart').show();
			$('#musicPart').show();
    		break;
    	case "52":
    		$('#psaClientPart').show();
    		$('#sponsor').show();
    		$('#musicPart').show();
    	break;

    	case "53":
    		$('#psaClientPart').show();
    		$('#sponsor').show();
    		$('#musicPart').show();
    	break;
    }
});

function hideAll()
{
	$('#psaClientPart').hide();
	$('#showPromoPart').hide();
	$('#paidAdPart').hide();
	$('#sponsor').hide();
	$('#musicPart').hide();
	$('#musicLanguage').hide();
	$('#psaChckBox').hide();
}


$(document).on('change ', "input[name='client']", function(){
	var val = $("input[name='client']").val()
	var clientID = $("#client option").filter(function() {
        return this.value == val;
    }).data('value');
    if(clientID == undefined)
    {
        $('#clientID').val('');
    }
    else
    {
        $.ajax({
            url: "/traffic/searchClient/" + clientID,
            type: 'POST',
            }).done(function(data) {
                var clientInfo = JSON.parse(data);
                $('#clientID').val(clientInfo.ClientNumber);
                $('#company').val(clientInfo.companyName);
                $('#phone').val(clientInfo.PhoneNumber);
                $('#email').val(clientInfo.email);
            }).fail(function(data){
                alert(JSON.stringfy(data));
            });
    }
    

});

$(document).on('click', '#insertShow', function(){

	var date = $('#showDate option:selected').text();
	var start = $('#showTimeStart').val();
	var end = $('#showTimeEnd').val();
	var showNamme = $('#showName').val();

 	$('#showPromoTimeTable tbody tr:last').after(

 		'<tr>' +
			'<td>' +
			'<input type="hidden" name="showDayVal[]" value="'+ date +'">' +
				date +
			'</td>' +
			'<td>' +
			'<input type="hidden" name="showTimeStartVal[]" value="'+ start +'">' +
			'<input type="hidden" name="showTimeEndVal[]" value="'+ end +'">' +
				start + ' ~ ' + end +
			'</td>' +
			'<td>' +
				'<button type="button" class="btn btn-sm btn-danger rmvBtn">' +
				      '<span class="glyphicon glyphicon-trash"></span>' +
		  		'</button>' +
			'</td>' +
		'</tr>'

 		);
});



//event for remove button from the song table
$(document).on('click', '.rmvBtn', function(e){
	$(this).closest ('tr').remove();
});




//Prevent enter key to submit form
$(document).ready(function() {
  $(window).keydown(function(event){
  	var cat = $('#Category').val();
    if(event.keyCode == 13 && cat == 45) {
      event.preventDefault();
      return false;
    }
  });
});

$(document).on('keyup', '#showPromoPart input', function(e){
	if(e.keyCode == 13)
    {
        // $(this).trigger("enterKey");
        $('#insertShow').trigger('click');
    }
});

