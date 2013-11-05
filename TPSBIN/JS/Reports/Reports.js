$(document).ready(function () {
    // Handler for .ready() called.
    $.ajax({
        url: "XML_DB_Check.php",
        beforeSend: function () {
            $("#check_db").show();
            $("#servers").hide();
        },
        success: function (data) {
            if ($(data).find("PASS").text() == 1) {
                $("#check_db").hide();
                $("#servers").show(); //.fadeIn(400);
                $("#alert_icon").fadeIn(400);
                $("#alert_icon").addClass('ui-icon ui-icon-check');
            }
            else {
                $("#check_db").hide();
                $("#servers").show(); //.fadeIn(400);
                $("#alert_icon").fadeIn(400);
                $("#alert_icon").addClass('ui-icon ui-icon-error');
                $("#dberror_notify").html($(data).find("ERROR").text());
                $("#db-error-dialog").dialog({
                    modal: true,
                    buttons: {
                        Ok: function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        },
        statusCode: {
            404: function () {
                alert("DB Check Failed, Error 404 Page Not Found Returned");
            }
        }
    });

    // Load Menu
    /*$("ul.subnav").parent().append("<span></span>"); //Only shows drop down trigger when js is enabled (Adds empty span tag after ul.subnav*)
	
	$("ul.topnav li span").click(function() { //When trigger is clicked...
		
		//Following events are applied to the subnav itself (moving subnav up and down)
		$(this).parent().find("ul.subnav").slideDown('fast').show(); //Drop down the subnav on click

		$(this).parent().hover(function() {
		}, function(){	
			$(this).parent().find("ul.subnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
		});

		//Following events are applied to the trigger (Hover events for the trigger)
		}).hover(function() { 
			$(this).addClass("subhover"); //On hover over, add class "subhover"
		}, function(){	//On Hover Out
			$(this).removeClass("subhover"); //On hover out, remove class "subhover"
	});*/
    //$("#menu").menu();
    //$( "#menu" ).menu( "collapseAll", null, true );

});