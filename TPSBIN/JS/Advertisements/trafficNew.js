

$( document ).ready(function() {
	
    $('#Category').trigger('change');
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
    	$.ajax({
            url: "/traffic/searchClient/" + clientID,
            type: 'POST',
            }).done(function(data) {
            	var clientInfo = JSON.parse(data);
            	$('#company').val(clientInfo.ContactName);
            	$('#phone').val(clientInfo.PhoneNumber);
            	$('#email').val(clientInfo.email);
            }).fail(function(data){
                alert(JSON.stringfy(data));
            });
    

});
