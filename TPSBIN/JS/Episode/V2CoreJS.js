function load(){
	 	//$.blockUI(null); 
	 	document.getElementById("DDLNormal").value = 21;// Allow for deafult option
	 }
	 
	//http://www.coryarthus.com/ (CODE SOURCE)
	function setSelectedIndex(s, v) {
	    for ( var i = 0; i < s.options.length; i++ ) {
	        if ( s.options[i].value == v ) {
	            s.options[i].selected = true;
	            return;
	        }
	    }
	}

	function popitup(url) {
		newwindow=window.open(url,'name','height=500,width=300');
		if (window.focus) {newwindow.focus()}
		return false;
	}
	function ADCH(){
		document.getElementById("AdNum").value = document.getElementById("ADLis").options[document.getElementById("ADLis").selectedIndex].value;
		//this.form.elements["AdNum"].value = "some";
		//alert("changed");
		
	}
	function CHAVF(){
		//document.getElementById("ADLis").options[document.getElementById("adbox1").selectedIndex].selected = true;
		setSelectedIndex(document.getElementById("ADLis"),document.getElementById("friends").options[document.getElementById("friends").selectedIndex].value)
		document.getElementById("DDLAdvert").options[2].selected = true;
		document.getElementById("AdNum").value = document.getElementById("friends").options[document.getElementById("friends").selectedIndex].value;
		//ADCH();
		$("#inputdiv").hide();
		$("#processing").hide();
		$("#InputAdvert").show();
	}
	
	function CHtype(){
		
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
			//$("#inputdiv").hide();
			//document.getElementById("")
			/*$("p1head").hide();
			$("spokenc").show();
			$("#processing").hide();
			$("#InputAdvert").hide();*/
			//alert("Please Enter Spoken Time!");
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
			//$("#inputdiv").hide();
			//document.getElementById("")
			/*$("p1head").hide();
			$("spokenc").show();
			$("#processing").hide();
			$("#InputAdvert").hide();*/
			//alert("Please Enter Spoken Time!");
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
		
		/*else{
			var x=document.getElementById("DDLNormal").selectedIndex;
			var y=document.getElementById("DDLNormal").options;
			alert("Index: " + y[x].index + " is " + y[x].text);
		}*/
	}
	function UnCHtype(){
		if(document.getElementById("DDLAdvert").options[document.getElementById("DDLAdvert").selectedIndex].value!=51){
			document.getElementById("DDLNormal").options[document.getElementById("DDLAdvert").selectedIndex].selected = true;
			$("#inputdiv").show();
			$("#processing").hide();
			$("#InputAdvert").hide();
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
		if(document.getElementById("DDLNormal").options[document.getElementById("DDLNormal").selectedIndex].value==12 || document.getElementById("DDLNormal").options[document.getElementById("DDLNormal").selectedIndex].value==11){
			//$("#inputdiv").hide();
			//document.getElementById("")
			/*$("p1head").hide();
			$("spokenc").show();
			$("#processing").hide();
			$("#InputAdvert").hide();*/
			//alert("Please Enter Spoken Time!");
			document.getElementById("plhead").style.display="none";
			document.getElementById("spokenc").style.display="inline";
			document.getElementById("plbody").style.display="none";
			document.getElementById("spokcon").style.display="inline";
			//document.getElementById("Spokcon").required="true";
			
			document.getElementById("title001").value="Spoken Word / News";
			document.getElementById("artin").disabled="true";
			document.getElementById("albin").disabled="true";
			document.getElementById("ccin").disabled="true";
			document.getElementById("hitin").disabled="true";
			document.getElementById("insin").disabled="true";
		}	
		/*else{
			var x=document.getElementById("DDLNormal").selectedIndex;
			var y=document.getElementById("DDLNormal").options;
			alert("Index: " + y[x].index + " is " + y[x].text);
		}*/
	}
	
	function GetNotes(){
			var NOTE = prompt("Short Notes Regarding current song (90 char max)");
			if(NOTE!=null&&NOTE!=''){
				document.getElementById('NF1').value=NOTE;
			}
	}
	
	function SpokenWord(){
		alert("Definition: \n Spoken Word \n\n defined as locally produced spoken programming");
	}
	
	function NotSpoken(){
		
	}
	
	function DefineCC(){
		alert("Definition: \n Instrumental \n\n defined as music that is performed with no vocals/singers performing in the piece.");
	}
	
	function DefineHit(){
		alert("Definition: \n Instrumental \n\n defined as music that is performed with no vocals/singers performing in the piece.");
	}
	
	function DefineIns(){
		alert("Definition: \n Instrumental \n\n defined as music that is performed with no vocals/singers performing in the piece.");
	}
	
	function fetchplaylist(){
		
	}
	
	function formsubmit(){
		$.blockUI({ message: '<h1><img src="/images/GIF/ajax-loader1.gif" /> Just a moment...</h1>' });
		/*$.blockUI({ message: '<h2><image src="/images/GIF/ajax-loader2.gif"/>Processing</h2>' }); 
        setTimeout(function() { 
            $.unblockUI({ 
                onUnblock: function(){ alert('The server was unable to process your request in a reasonable time. \nPlease resubmit your data'); } 
            }); 
        }, 4000);*/ 
		if(document.getElementById("spokcon").value<600 && document.getElementById("spokcon").value>0){
			$.unblockUI();
			/*$("#inputdiv").hide();
			//$("topbar").show();
			$("#processing").show();*/
		}
		else if(document.getElementById('title001').value.length>0){
			$.unblockUI();
			/*$("#inputdiv").hide();
			//$("topbar").show();
			$("#processing").show();*/
		}
	}

	// -->