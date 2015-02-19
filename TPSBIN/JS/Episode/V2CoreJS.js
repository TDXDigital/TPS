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
        //opens a new window of size 500x300 (portorate for category listing)
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
			
			// SET Value of spoken minutes to Null as element is hidden
			document.getElementById("spokenc").value="";
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
		else if(document.getElementById("DDLNormal").options[document.getElementById("DDLNormal").selectedIndex].value==43)
		{
			document.getElementById("title001").value="Musical Station ID";
			document.getElementById("artin").readonly="TRUE";
			document.getElementById("albin").readonly="TRUE";
			document.getElementById("hitin").disabled="TRUE";
			document.getElementById("ccin").disabled="TRUE";
			document.getElementById("insin").disabled="TRUE";
		}
		/*else{
			var x=document.getElementById("DDLNormal").selectedIndex;
			var y=document.getElementById("DDLNormal").options;
			alert("Index: " + y[x].index + " is " + y[x].text);
		}*/
	}

	function GetNotes() {
	    var NOTE = prompt("Short Notes Regarding current song (90 char max)");
	    if (NOTE != null && NOTE != '') {
	        document.getElementById('NF1').value = NOTE;
	    }
	    $("NoteField").SlideDown();
	}

	function SpokenWord(){
		alert("Definition: \n Spoken Word \n\n defined as locally produced spoken programming");
	}
	
	function NotSpoken(){
		
	}
	function ClearWarning(){
		$("#warning").html("");
		$("warning").hide();
	}
	function DefineCC(){
		$("#warning").html("<span>Definition: \n Instrumental \n\n defined as music that is performed with no vocals/singers performing in the piece.</span>");
		SetTimeout(ClearWarning(),2000);
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
		//$.blockUI({ message: '<h1><img src="/images/GIF/ajax-loader1.gif" /> Just a moment...</h1>' });
		/*$.blockUI({ message: '<h2><image src="/images/GIF/ajax-loader2.gif"/>Processing</h2>' }); 
        setTimeout(function() { 
            $.unblockUI({ 
                onUnblock: function(){ alert('The server was unable to process your request in a reasonable time. \nPlease resubmit your data'); } 
            }); 
        }, 4000);*/ 
		//if(document.getElementById("spokcon").value<600 && document.getElementById("spokcon").value>0){
		//	$.unblockUI();
			/*$("#inputdiv").hide();
			//$("topbar").show();
			$("#processing").show();*/
		/*}
		else if(!document.getElementById('title001').value.length>0){
			$.unblockUI();*/
			/*$("#inputdiv").hide();
			//$("topbar").show();
			$("#processing").show();*/
		//}
	}

	// -->

    // unblock when ajax activity stops 
     //$(document).ajaxStop($.unblockUI); 


     function test() {
         $.ajax({ url: 'wait.php', cache: false });
     }
     function Display_RDS() {
         var rds = $.ajax({
             url: "EPV3/AJAX/components/RDS_Episode.php",
             cache: false
         });
         rds.done(function (msg) {
             $("#current_song").html(msg);
         });
         rds.fail(function (jqXHR, textStatus) {
             $("#current_song").html("Request failed: " + textStatus);
         });
     }

     function update_playback(target) {

     }

     function transfer_foobar_data() {
         /*if (
         $("#title001").val() == "" &&
         $("#artin").val() == "" &&
         $("albin").val() == "" &&
         $("composer").val == "") {*/
             document.getElementById("title001").value = $("#title003").val();
             document.getElementById("artin").value = $("#artist003").val();
             document.getElementById("albin").value = $("#album003").val();
             document.getElementById("composer").value = $("#composer003").val();
         /*}
         else {
             dialog("Fields not empty, please clear all input");
         }*/
     }
     
     function process_foobar(np_data){
         fb2k_data = JSON.parse(np_data);
         var volume_percentage = eval(100 + (fb2k_data.volume / 35 * 100));
         var playback_percentage = eval(100 - (fb2k_data.playback_time / fb2k_data.length *100));
          var html_info="<div id='notice' class='ui-state-highlight' style='width:1350px;'>" +
            //"</form><form action='p2insert.php' method='post'>" +
            "<div class=\"grid\" style='width:1350px; height:100%;'>" +
            "<div class='col-1-25'><img src=\"../images/foobar2000.png\" width=\"50px;\" alt=\"foobar2000\" title=\"" +
            fb2k_data.version +
            "\"/></div>" +
            "<div class='col-1-8'><span style=\"float:left\">Volume: </span><br><progress value=\"" +
            volume_percentage +
            "\" max=\"100\" id=\"volume\">"+volume_percentage +"&perc;</progress><br><span style=\"float:left\">Playback:</span>";
            if(fb2k_data.status=="playing"){
                html_info += "<span style=\"float:left\" title=\"Playing...\"class='ui-icon ui-icon-play'></span>";
            }
            else if(fb2k_data.status=="paused"){
                html_info += "<span style=\"float:left\" title=\"Paused...\" class='ui-icon ui-icon-pause'></span>";
            }
            else if(fb2k_data.status=="stopped"){
                html_info += "<span style=\"float:left\" title=\"Stopped...\" class='ui-icon ui-icon-stop'></span>";
            }
            else{
                html_info += "<span style=\"float:left\" class='ui-icon ui-icon-alert'></span><span style=\"float:left\">error:"+fb2k_data.status+"</span>";
            }
            html_info += "<br><progress value=\"" +
            playback_percentage +
            "\" max=\"100\" id=\"volume\">"+playback_percentage +"&perc;</progress>"+
            /*"<p>Status:</p>"+
            fb2k_data.status +*/
            "</div>" +
            "<div class='col-1-6'><p>Title</p><input type='text' style=\"width: 95%\" readonly placeholder=\"  Not Provided\" name='title_foobar' id='title003' value='" + fb2k_data.title + "' /></div>" +
            "<div class='col-1-6'><p>Artist</p><input type='text' style=\"width: 95%\" readonly placeholder=\"  Not Provided\" name='artist_foobar' id='artist003' value='";
          if (fb2k_data.track_artist !== undefined && fb2k_data.track_artist!=='?') {
              html_info += fb2k_data.artist;
          }
          else {
              html_info += fb2k_data.album_artist;
          }
          var composer = fb2k_data.composer ? '?' : "";
            html_info += "' /></div>" +
            "<div class='col-1-6'><p>Album</p><input type='text' style=\"width: 95%\" readonly placeholder=\"  Not Provided\" name='album_foobar' id='album003' value='" + fb2k_data.album + "' /></div>" +
            "<div class='col-1-6'><p>Composer</p><input type='text' style=\"width: 95%\" readonly placeholder=\"  Not Provided\" name='composer_foobar' id='composer003' value='" + composer + "' /></div>" +
            "<div class='col-1-6' style=\"vertical-align: bottom;\"><button id=\"transfer_foobar_button\" onclick=\"transfer_foobar_data(); return false;\">Transfer Information</button></div>" +
            "</span></div></div></form>";

          $("#info_player").html(html_info);
          $("#transfer_foobar_button").button({
              icons: {
                  primary: "ui-icon-circle-arrow-n"
              },
              text: true
          });
     }
     
        // EPV3/Switch.php
        // generate web workers to handle updating diapla
     function Display_Switch() {
        if(typeof(Worker) !== "undefined") {
            if(typeof(switch_worker) == "undefined") {
                switch_worker = new Worker("../TPSBIN/JS/Episode/switch_worker.js");
            }
            switch_worker.onmessage = function(event) {
                //document.getElementById("result").innerHTML = event.data;
                $("#switch_status").html(event.data);
            };
        } else {
            // Sorry! No Web Worker support..
             var switch_s = $.ajax({
                 url: "EPV3/Switch.php?q=V2",
                 cache: false
             });
             switch_s.done(function (msg) {
                 $("#switch_status").html(msg);
             });
             switch_s.fail(function (jqXHR, textStatus) {
                 $("#switch_status").html("Request failed: " + textStatus);
             });
        }
     }

    function Stop_Switch_Workjer(){
        switch_worker.terminate();
        switch_worker=undefined;
    }

    function Foobar2000(server) {
        if(typeof(Worker) !== "undefined") {
            if(typeof(foobar_worker) == "undefined") {
                foobar_worker = new Worker("../TPSBIN/JS/Episode/foobar_worker.js");
            }
            foobar_worker.onmessage = function (event) {
                if (event.data == "error") {
                    Foobar2000_stop();
                }
                else {
                    process_foobar(event.data);
                    //$("#info_player").append(event.data);
                }
            };
        } else {
            // Sorry! No Web Worker support..
             var foobar_s = $.ajax({
                 url: "EPV3/workers.php?q=np",
                 cache: false
             });
             foobar_s.done(function (msg) {
                 //$("#info_player").html(msg);
                 //var np_data = JSON.parse(xmlhttp.responseText);
                 //process_foobar(np_data);
                 process_foobar(msg.responseText);
             });
             foobar_s.fail(function (jqXHR, textStatus) {
                 $("#info_player").html("Request failed: " + textStatus);
             });
        }
     }

    function Foobar2000_stop(){
        foobar_worker.terminate();
        foobar_worker=undefined;
    }


     // START CLOCK
     setInterval(function () {
         // Create a newDate() object and extract the seconds of the current time on the visitor's
         var seconds = new Date().getSeconds();
         // Add a leading zero to seconds value
         $("#sec").html((seconds < 10 ? "0" : "") + seconds);
     }, 1000);

     setInterval(function () {
         // Create a newDate() object and extract the minutes of the current time on the visitor's
         var minutes = new Date().getMinutes();
         // Add a leading zero to the minutes value
         $("#min").html((minutes < 10 ? "0" : "") + minutes);
     }, 1000);

     setInterval(function () {
         // Create a newDate() object and extract the hours of the current time on the visitor's
         var hours = new Date().getHours();
         // Add a leading zero to the hours value
         $("#hours").html((hours < 10 ? "0" : "") + hours);
     }, 1000);
     // END CLOCK
     function HideHardware() {

         $.ajax({
             statusCode: {
                 url: "EPV2/AJAX/setpref.php?c=SetHardwareOff",
                 200: function () {
                     $("#HDW_title_open").hide();
                     $("#hdw_prompt").show();
                     $("#hdw").hide();
                 },
                 403: function () {
                     alert("Session Timed Out (Not Authorized)");
                 }
             }
         });
     }
     function ShowHardware() {
        $.ajax({
             statusCode: {
                 url: "EPV2/AJAX/setpref.php?c=SetHardwareOn",
                 200: function () {
                     $("#hdw").show();
                     $("#HDW_title_open").show();
                     $("#hdw_prompt").hide();
                 },
                 403: function () {
                     alert("Session Timed Out (Not Authorized)");
                 }
             }
         });
     }
     function UpdateFinalize() {
         var value = $(this).val();
         $("input[name='time_final_confirm']").val(value);
     }
