

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

    	case 44:

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

