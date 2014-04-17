// FILE SPECIFIC TO P1INSERTEP.PHP

function urlencodephp (str) {
	// http://kevin.vanzonneveld.net
	// +   original by: Philip Peterson
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +      input by: AJ
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +      input by: travc
	// +      input by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Lars Fischer
	// +      input by: Ratheous
	// +      reimplemented by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: Joris
	// +      reimplemented by: Brett Zamir (http://brett-zamir.me)
	// %          note 1: This reflects PHP 5.3/6.0+ behavior
	// %        note 2: Please be aware that this function expects to encode into UTF-8 encoded strings, as found on
	// %        note 2: pages served as UTF-8
	// *     example 1: urlencode('Kevin van Zonneveld!');
	// *     returns 1: 'Kevin+van+Zonneveld%21'
	// *     example 2: urlencode('http://kevin.vanzonneveld.net/');
	// *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
	// *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
	// *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
	str = (str + '').toString();
		
	// Tilde should be allowed unescaped in future versions of PHP (as reflected below), but if you want to reflect current
	// PHP behavior, you would need to add ".replace(/~/g, '%7E');" to the following.
return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
}
		
		
function getCallsign(value_name){
	document.getElementById("SM").disabled = true;
	if(value_name==0){
		document.getElementById("callbox").innerHTML="<option>Not Set</option>";
		document.getElementById("airtime").disabled=true;
	   	document.getElementById("airdate").disabled=true;
	   	document.getElementById("brType").disabled=true;
	   	document.getElementById("s2").style="";
	   	document.getElementById("s3").style="";
	   	document.getElementById("s4").style="";
	   	document.getElementById("s5").style="";
	   	document.getElementById("s6").style="";
	   	document.getElementById("s7").style="";
	   	document.getElementById("s1").style="background-color:#CCFFFF";
		return;
	}
	else{
				
		if(window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari (www.w3Schools.com Source)
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5 (Not Supported)
	   		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	   	}
	   	xmlhttp.onreadystatechange=function()
	   		{
	   			if(xmlhttp.readyState==4 && xmlhttp.status==200){
	   				document.getElementById("callbox").innerHTML=xmlhttp.responseText;
	   				document.getElementById("SM").disabled = false;
	   				document.getElementById("airtime").disabled=false;
	   				document.getElementById("airdate").disabled=false;
	   				//document.getElementById("airtime").disabled=false;
	   				document.getElementById("brType").disabled=false;
	   				document.getElementById("s2").style="";
	   				document.getElementById("s3").style="";
	   				document.getElementById("s4").style="";
	   				document.getElementById("s5").style="";
	   				document.getElementById("s6").style="";
	   				document.getElementById("s3").style="background-color:#CCFFFF";
	   				//setTimeout(1000);
	   				document.getElementById("s3").style="";
	   				document.getElementById("s5").style="background-color:#CCFFFF";
	   				document.getElementById("s6").style="background-color:#CCFFFF";
	   						
	   			}
	   			/*else{
	   				//alert(xmlhttp.status+" "+xmlhttp.readyState); //Debug
	   			}*/
	   		}
	   	xmlhttp.open("GET","AJAX/getcallsign.php?n="+urlencodephp(value_name),true);
	   	xmlhttp.send();
	   	document.getElementById("s1").style="";
	   	document.getElementById("s2").style="background-color:#CCFFFF";
	   	//wait(1000);
	   			
	}
}
function RecVer(val)
{
	if(val>0){
		if(window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari (www.w3Schools.com Source)
			xmlhttp=new XMLHttpRequest();
		}
		else
		{// code for IE6, IE5 (Not Supported)
	   		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	   	}
	   	xmlhttp.onreadystatechange=function()
	   		{
	   			if(xmlhttp.readyState==4 && xmlhttp.status==200){
	   				document.getElementById("prdate").value=xmlhttp.responseText;
	   				if(val==2){
	   					oldate = document.getElementById("airdate").value;
	   					document.getElementById("airdate").value="1973-01-01";
	   					document.getElementById("airdate").readonly="readonly";
	   					document.getElementById("s1").style="";
	   					document.getElementById("s2").style="";
	   					document.getElementById("s3").style="";
	   					document.getElementById("s4").style="background-color:#CCFFFF";
	   					document.getElementById("s5").style="";
	   					document.getElementById("s6").style="";
	   				}
	   				else{
	   					document.getElementById("airdate").readonly=false;
	   					if(oldate!=""){
	   						document.getElementById("airdate").value=oldate;
	   					}
	   					document.getElementById("s1").style="";
	   					document.getElementById("s2").style="";
	   					document.getElementById("s3").style="";
	   					document.getElementById("s4").style="background-color:#CCFFFF";
	   					document.getElementById("s5").style="background-color:#CCFFFF";
	   					document.getElementById("s6").style="background-color:#CCFFFF";
	   				}
	   			}
	   		}
	   	xmlhttp.open("GET","AJAX/date.php",true);
	   	xmlhttp.send();
	   			
		document.getElementById("prdate").required = false;
		document.getElementById("prdate").disabled = false;
		//document.getElementById("prdate").value = dateval;
				
	}
	else{
				
		document.getElementById("prdate").value = "";
		document.getElementById("prdate").required = true;
		document.getElementById("prdate").disabled = true;
		document.getElementById("airdate").disabled=false;
		document.getElementById("s1").style="";
	   	document.getElementById("s2").style="";
	   	document.getElementById("s3").style="";
	   	document.getElementById("s4").style="";
	   	document.getElementById("s5").style="background-color:#CCFFFF";
	   	document.getElementById("s6").style="background-color:#CCFFFF";
		if(oldate!=""){
	   		document.getElementById("airdate").value=oldate;
	   	}
	}
}
$(document).ready(
function () {
    // from http://www.javascriptkit.com/javatutors/createelementcheck2.shtml
    var datefield = document.createElement("input")
    datefield.setAttribute("type", "date")
    //if (datefield.type != "date") { //if browser doesn't support input type="date", load files for jQuery UI Date Picker
    //document.write('\n');
    /*document.write('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"><\/script>\n')
    
    document.write('<!-- Latest compiled and minified CSS -->\n');
    document.write('<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">');
    document.write('<!-- Optional theme -->');
    document.write('<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">');
    document.write('<!-- Latest compiled and minified JavaScript -->');
    document.write('<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>');*/
    //}

    if (datefield.type != "date") { //if browser doesn't support input type="date", initialize date picker widget:
        jQuery(function ($) { //on document.ready
            $('#airdate').datepicker(); // for individual date boxes
        })
    }
    $(".chosen-select").chosen();
});