/**
 * @author j.oliver
 */

function LoadSettings(){
	$("#bay").html("<progress/>");
	// GET XML File
	try{
		//http://think2loud.com/224-reading-xml-with-jquery/
		$.ajax({
			type: "GET",
			url: "/TPSBIN/Control/PS/PollSwitch.php",
			dataType: "xml",
			success: function(xmlraw) {
		 		var xml = xmlraw,
		 		xmlDoc = $.parseXML( xml ),
		    	$xml = $( xmlDoc ),
		    	$error = $xml.find( "error" ),
		    	$data = $xml.find( "rx" );
		    	$("#bay").html("<p>"+$data.text()+"</p>");
		    	$("#error").html("<p>"+$error.text()+"</p>");
			},
			error: function(){
				$("#bay").html("<span>an Error Occured</span>");
			}
		});
	}
	catch (err){
		$("#bay").html("<span>an Error Occured</span>");
	}
}

function Get_Switch_Poll(cmd) {
    $("#bay").html("<progress/>");

         var switch_s = $.ajax({
             url: "../TPSBIN/Control/TCP/TCP_SW.Control.php?q="+cmd,
             cache: false
         });
         switch_s.done(function (msg) {
             $("#bay").html(msg);
         });
         switch_s.fail(function (jqXHR, textStatus) {
             $("#bay").html("Request failed: " + textStatus);
         });
     }
