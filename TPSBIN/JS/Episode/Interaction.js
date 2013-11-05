function ajaxLoad(URL){
$('#domScratch').html($('#domFetch').html());
$.blockUI({ message: $('#domScratch'), overlayCSS: {backgroundColor:'#f5f5f5'} });
$('.blockOverlay').attr('title','Click to Dismiss').click($.unblockUI);
$.ajax({
  	url: URL,
  	success: function(data) {
    	$('#domScratch').html(data);
    	//alert('Load was performed.');
  		}
  	});
//$.blockUI();
return false;
}
function Finalize(){
$('#domScratch').html($('#domFetch').html());
$.blockUI({ message: $('#domScratch'), overlayCSS: {backgroundColor:'#f5f5f5'} });
$('.blockOverlay').attr('title','Click to Dismiss').click($.unblockUI);
$.ajax({
  	url: 'AJAX/components/finalize.php',
  	success: function(data) {
    	$('#domScratch').html(data);
    	$('#FinTime').timespinner();
  		}
  	});
//$.blockUI();
return false;
}
function launchload(){
    $.unblockUI();
    $("#LoadPrompt").dialog({
        buttons: [{
            text: "Load",
            click: function () {
                $('#loadform').submit();
            }

        }, {
            text: "Reset",
            click: function () {
                $('#loadform')[0].reset();
            }
        }, {
            text: "Cancel",
            click: function () {
                $(this).dialog("close");
                $('#loadform')[0].reset();
            }
        }],
        width: 500,
        modal: true
    });
}
function unLoad(URL){
  	
}
function loadlist(){
//check for changes
var resultque = true
  	
//evaluate response
if(resultque == true){
  	$('#list').load('AJAX/components/list.php',
  	setListSpinners());
  	$('#collector').unblock();
  	return false;
}
else{
  	return false;
}
}
function closeSubmit(){
$('#list').load('AJAX/components/list.php',setListSpinners());	
$('#collector').load('AJAX/components/Collector.php',setCollectorSpinners());
UpdateCounts()
//setCollectorSpinners();
	
}
function About(){
$( "#About" ).dialog({
    resizable: false,
    height:140,
    modal: true,
    buttons: {
    "Delete all items": function() {
        $( this ).dialog( "close" );
    },
    Close: function() {
        $( this ).dialog( "close" );
        return false;
    }
    }
});
}
function growl(DisplayText){
$.blockUI({ 
    message: DisplayText, 
    fadeIn: 700, 
    fadeOut: 700, 
    timeout: 4000, 
    showOverlay: false, 
    centerY: false, 
    css: { 
        width: '350px', 
        top: '10px', 
        left: '', 
        right: '10px', 
        border: 'none', 
        padding: '5px', 
        backgroundColor: '#000', 
        '-webkit-border-radius': '10px', 
        '-moz-border-radius': '10px', 
        opacity: .6, 
        color: '#fff' 
    }
    }); 
}