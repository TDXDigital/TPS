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
        msg.replace("\n", "<br />", "g");
        $("#bay").html(msg);
        $("#bay").show();
        $("#error").hide();
        //$('#bay').val().replace("\n", "<br />", "g")
    });
    switch_s.fail(function (jqXHR, textStatus) {
        $("#error").html("Request failed: " + textStatus);
        $("#error").show();
        $("#bay").hide();
    });
}

function Get_Control(){
    var value = $("#control_select").val();
    $(function () {
        $("#dialog-confirm").dialog({
            resizable: false,
            height: 300,
            modal: true,
            buttons: {
                "Confirm": function () {
                    Get_Switch_Poll(value);
                    $(this).dialog("close");
                },
                Cancel: function () {
                    $(this).dialog("close");
                    $("#bay").hide();
                    $("#error").hide();
                }
            }
        });
    });
}
