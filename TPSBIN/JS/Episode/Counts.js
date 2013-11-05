var Adreq = -99;
var Adfill = -99;
var PSAreq = -99;
var PSAfill = -99;
var CCreq = -99;
var CCfill = -99;
var PLreq = -99;
var PLfill = -99;
var HITlim = -99;
var HIThas = -99;
	
var PASScol = "#00E400";
var FAILcol = "#FF5050";
			
function CheckReqs(){
		
	if(Adreq > Adfill){
		$('#ComTabImg').show();
	}
	else{
		$('#ComTabImg').css("display", "none");
	}
	if(PSAreq > PSAfill){
		$('#PromTabImg').show();
	}
	else{
		$('#PromTabImg').css("display", "none");
	}
		
}
function UpdateCounts(){
	$.ajax({
  	url: "AJAX/components/Counts.php",
  	success: function(data) {
  		if(data == ""){
  			return false;
  		}
  		Adreq = $(data).filter('#adreq').html();
  		Adfill = $(data).filter('#adfill').html();
		PSAreq = $(data).filter('#psareq').html();
		PSAfill = $(data).filter('#psafill').html();
		CCreq = $(data).filter('#ccreq').html();
		CCfill = $(data).filter('#ccfill').html();
		CCpass = $(data).filter('#ccpass').html();
		PLreq = $(data).filter('#plreq').html();
		PLfill = $(data).filter('#plfill').html();
		PLpass = $(data).filter('#plpass').html();
		HITlim = $(data).filter('#hitlim').html();
		HIThas = $(data).filter('#hithas').html();
			
		$('#cc_c').html(CCfill+'/'+CCreq);
		if(CCpass=="1"){
			$('#cc_c').css('color',PASScol);
		}
		else{
			$('#cc_c').css('color',FAILcol);
		}
		$('#pl_c').html(PLfill+'/'+PLreq);
		if(PLpass=="1"){
			$('#pl_c').css('color',PASScol);
		}
		else{
			$('#pl_c').css('color',FAILcol);
		}
		$('#ad_c').html(Adfill+'/'+Adreq);
		if(Adreq==Adfill){
			$('#ad_c').css('color',PASScol);
		}
		else{
			$('#ad_c').css('color',FAILcol);
		}
		$('#psa_c').html(PSAfill+'/'+PSAreq);
		if(PSAreq<=PSAfill){
			$('#psa_c').css('color',PASScol);
		}
		else{
			$('#psa_c').css('color',FAILcol);
		}
		$('#hit_c').html(HIThas+'/'+HITlim);
		if(HITlim<HIThas){
			$('#hit_c').css('color',FAILcol);
		}
		else{
			$('#hit_c').css('color',PASScol);
		}
  		}
  	});
}