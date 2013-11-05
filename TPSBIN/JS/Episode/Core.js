var editchanges = false;
var LOAD_OK = 'FALSE';
	function setCollectorSpinners(){
		$('#plin').spinner({
	  		incremental: true,
	  		max: 999,
	  		min: 0,
	  		width: 3,
	  	});
	  	$('#spin').spinner({
	  		incremental: true,
	  		step: .1,
	  		max: 480,
	  		min: 0,
	  		width: 3
	  	});
	  	$('#sgyear').spinner({
	  		incremental: true,
	  		max: 9999,
	  		min: -9999,
	  		width: 4
	  	});
	  	$('#sgyear').spinner( "option", "disabled", true );
	  	//if (!Modernizr.inputtype.time){
		  	
		    $(function() {
		    	//$( "#tiin" ).attr('type','text');
			    $( "#tiin" ).timespinner();
			 
		        $( "#culture" ).change(function() {
		            var current = $( "#spinner" ).timespinner( "value" );
		            Globalize.culture( $(this).val() );
		            $( "#spinner" ).timespinner( "value", current );
		        });
			});
		//}
	}
	function setListSpinners(){
		$('input[name="Playlist[]"]').spinner({
	  		incremental: true,
	  		max: 999,
	  		min: 0,
	  		width: 3,
	  	});
	  	$('input[name="Spoken[]"]').spinner({
	  		incremental: true,
	  		max: 480,
	  		min: 0,
	  		width: 3,
	  	});
	  	$('input[name="times[]"]').timespinner();
	}
	
	function setSpinners(){
		setCollectorSpinners();
		setListSpinners();
		$('#collector').unblock();
	}
	function CritUpdate(){
  		$.ajax({
  		url: "AJAX/components/CritInfo.php",
  		beforeSend: function() {
  			$('#error').slideUp();
  		},
  		success: function(data) {
  			if(data == ""){
  				$('#error').slideUp();
  			}
  			else{
  				$('#error').slideDown();
  			}
    		$('#error').html(data);
  		 }
  		});
  	}
  $(document).ready(function(){
  	//#########################################
  	//CheckLoad();
  	/*if(hasset=='FALSE'){
  		$.blockUI({
			message: $('#Login')
		});
  	}*/
  	CheckDbOnLoad();
  	$.widget( "ui.timespinner", $.ui.spinner, {
        options: {
            // seconds
            step: 60 * 1000,
            // hours
            page: 60
        },
 
        _parse: function( value ) {
            if ( typeof value === "string" ) {
                // already a timestamp
                if ( Number( value ) == value ) {
                    return Number( value );
                }
                return +Globalize.parseDate( value );
            }
            return value;
        },
 
        _format: function( value ) {
            return Globalize.format( new Date(value), "t" );
        }
        
    });
  	
  	$(window).scroll(function(){
  		//$('#foot').offset({ top: offset.top, left: offset.left})
  	});
  	$.ajaxSetup({
  		type: "POST"
  	})
  	$('#collector').ajaxStop(function (){
	  	setCollectorSpinners();
	  	$('#subcol1').removeAttr('disabled');
	  	$('#collector').unblock();
	  	UpdateCounts();
	});
	$('#list').ajaxStop(function (){
	  	setListSpinners();
	  	$('#list').unblock();
	  	$('#collector').unblock();
	  	//CheckReqs();
	});
	$('#list').ajaxStart(function (){
		$('#list').block({
			message: '<h1>Processing</h1>', 
            css: { border: '3px solid #a00' } 
		});
	});
  	
	
  	$('#stats').load('AJAX/showStats.php',
  	function() {
  		var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ]; 
		var dayNames= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
		$(function() {
			$( "#tabs" ).tabs({
				beforeLoad: function( event, ui ) {
				ui.panel.html("Loading Tab...");;
				ui.jqXHR.error(function() {
					ui.panel.html(
						"Error Loading Tab..." );
				});
				}
			});
			$("#ui-tabs-1").css({width: "inherit"});
		});
		//$('#Date').html(monthNames[new Date.getDay()] + " " + new Date.getDate() + ' ' + monthNames[new Date.getMonth()] + ' ' + new Date.getFullYear());
  	});
  	$('#system').load('AJAX/sysCheck.php');
  	$('#collector').load('AJAX/components/Collector.php',
  	function(){
	  	setCollectorSpinners();
	  	// AUTOCOMPLETE
	  	$('#title').autocomplete({
	      source: "AJAX/components/LibSearchTitle.php",
	      minLength: 2
	      /*select: function( event, ui ) {
	        log( ui.item ?
	          "Selected: " + ui.item.value + " aka " + ui.item.id :
	          "Nothing selected, input was " + this.value );
	      }*/
	    });
	    $('#Artist').autocomplete({
	      source: "AJAX/components/LibSearchArtist.php",
	      minLength: 2
	      /*select: function( event, ui ) {
	        log( ui.item ?
	          "Selected: " + ui.item.value + " aka " + ui.item.id :
	          "Nothing selected, input was " + this.value );
	      }*/
	    });
	    $('#Album').autocomplete({
	      source: "AJAX/components/LibSearchAlbum.php",
	      minLength: 2
	      /*select: function( event, ui ) {
	        log( ui.item ?
	          "Selected: " + ui.item.value + " aka " + ui.item.id :
	          "Nothing selected, input was " + this.value );
	      }*/
	    });
	    
	    // REMOVES DISABLE (Prevents submission during load) weird, I know!
	  	$('#subcol1').removeAttr('disabled');
	  	$('#collector').unblock();
	  }
	);
	
	$.ajax({
  		url: "AJAX/components/CritInfo.php",
  		success: function(data) {
  			if(data == ""){
  				return false;
  			}
    		$('#error').append(data);
    		$('#error').show();
  		 }
  		});
	
  	$('#info').load('AJAX/information.php',
  	 $(function() {
  	 	 $( "#accd" ).accordion({
			collapsible: true
			});
		})
	);
	
		
  	//$('#list').load('AJAX/listing.php', function(){
  	$('#list').load('AJAX/components/list.php', function(){
    //$.unblockUI();
    if(LOAD_OK == 'TRUE'){
        $.unblockUI();
    }
    $('#collector').unblock();
  		var height_t = $("#list").height() + $('#info').height() + $('#tiin').height() + $('#system').height() + $('#status').height() + $('#jMenu').height() +$('#space').height();
  		if(height_t>$(window).height()){
  			$("#content").css({
    			height: height_t
			});
		}
		setListSpinners();
  		});
	
    //#########################################
  	if(disable_prompt == true){
  		$('#newLoad').slideUp();
  	}
  	
  	setInterval( function() {
		// Create a newDate() object and extract the seconds of the current time on the visitor's
		var seconds = new Date().getSeconds();
		// Add a leading zero to seconds value
		$("#sec").html(( seconds < 10 ? "0" : "" ) + seconds);
		},1000);
		
	setInterval( function() {
		// Create a newDate() object and extract the minutes of the current time on the visitor's
		var minutes = new Date().getMinutes();
		// Add a leading zero to the minutes value
		$("#min").html(( minutes < 10 ? "0" : "" ) + minutes);
	    },1000);
		
	setInterval( function() {
		// Create a newDate() object and extract the hours of the current time on the visitor's
		var hours = new Date().getHours();
		// Add a leading zero to the hours value
		$("#hours").html(( hours < 10 ? "0" : "" ) + hours);
	    }, 1000);
  	
  	//Verify Exit
  	$(window).on('beforeunload', function() {
  		/*$( "#question" ).dialog({
	      resizable: false,
	      height:140,
	      modal: true,
	      buttons: {
	        "Delete all items": function() {
	          $( this ).dialog( "close" );
	        },
	        Cancel: function() {
	          $( this ).dialog( "close" );
	          return false;
	        }
	      }
	    });*/
	   if(ExitPrompt == 1){
	    return "Warning! Your log is currently not finalized. \nIt is recommended that you use the finalize option\nLog Options -> Finalize\n\nExiting now will automatically finalize your log for this time";
	   }
  	});

     
    $("#foot").hover(function(){$('#foot').fadeOut(100);$('#foot').fadeIn(500);});
    $("span.fade").hover(function(){$('#foot').fadeOut(100);$('#foot').fadeIn(500);});
    //$(document).unblockUI();
    // LOAD VARIABLES
    if(argm!=''){
        $('#loadMessage').text(argm);
        $('#loadMessage').addClass('ui-state-error');
        //alert(argm);
    }

    //LOAD - Create Datepicker for date field
    $( "#datepicker" ).datepicker();
  	
  	var dispCom = false;
    // simple jMenu plugin called
    //$("#jMenu").jMenu();
 	/*$('#ComTab').active(function(){
 		$('#ads').hide();
 	});*/
    // more complex jMenu plugin called
    $("#jMenu").jMenu({
      ulWidth : 'auto',
      effects : {
        effectSpeedOpen : 75,
        effectTypeOpen : 'slide',
        effectOpen : 'linear'
      },
      animatedText : true,
      paddingLeft: 1,
      ulWidth: '130px'
    });
  });
 