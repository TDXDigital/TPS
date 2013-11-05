 <style>
.ui-combobox {
	position: relative;
	display: inline-block;
}
.ui-combobox-toggle {
	width: 10px;
	position: absolute;
	top: 0;
	bottom: 0;
	margin-left: -1px;
	padding: 0;
	/* support: IE7 */
	*height: 1.7em;
	*top: 0.1em;
}
.ui-combobox-input {
	width: 60px;
	margin: 0;
	#padding: 0.3em;
}
.ui-button-text {
	padding: 0.3em 0.1em;
}
</style>
<script>

(function( $ ) {
	$.widget( "ui.combobox", {
		_create: function() {
		var input,
		that = this,
		wasOpen = false,
		select = this.element.hide(),
		selected = select.children( ":selected" ),
		value = selected.val() ? selected.text() : "",
		wrapper = this.wrapper = $( "<span>" )
		.addClass( "ui-combobox" )
		.insertAfter( select );
		function removeIfInvalid( element ) {
			var value = $( element ).val(),
			matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( value ) + "$", "i" ),
			valid = false;
			select.children( "option" ).each(function() {
				if ( $( this ).text().match( matcher ) ) {
					this.selected = valid = true;
					return false;
				}
			});
			if ( !valid ) {
				// remove invalid value, as it didn't match anything
				// OVERRIDE - Use origional Value if not found!
				$( element )
				.val( "" )
				.attr( "title", value + " didn't match any item" )
				.tooltip( "open" );
				select.val( value );
				setTimeout(function() {
					input.tooltip( "close" ).attr( "title", "" );
				}, 2500 );
				input.data( "ui-autocomplete" ).term = "";
			}
		}
input = $( "<input>" )
.appendTo( wrapper )
.val( value )
.attr( "title", "" )
.addClass( "ui-state-default ui-combobox-input" )
.autocomplete({
delay: 0,
minLength: 0,
source: function( request, response ) {
var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
response( select.children( "option" ).map(function() {
var text = $( this ).text();
if ( this.value && ( !request.term || matcher.test(text) ) )
return {
label: text.replace(
new RegExp(
"(?![^&;]+;)(?!<[^<>]*)(" +
$.ui.autocomplete.escapeRegex(request.term) +
")(?![^<>]*>)(?![^&;]+;)", "gi"
), "<strong>$1</strong>" ),
value: text,
option: this
};
}) );
},
select: function( event, ui ) {
ui.item.option.selected = true;
that._trigger( "selected", event, {
item: ui.item.option
});
},
change: function( event, ui ) {
if ( !ui.item ) {
removeIfInvalid( this );
}
}
})
.addClass( "ui-widget ui-widget-content ui-corner-left" );
input.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
return $( "<li>" )
.append( "<a>" + item.label + "</a>" )
.appendTo( ul );
};
$( "<a>" )
.attr( "tabIndex", -1 )
.attr( "title", "Show All Items" )
.tooltip()
.appendTo( wrapper )
.button({
icons: {
primary: "ui-icon-triangle-1-s"
},
text: false
})
.removeClass( "ui-corner-all" )
.addClass( "ui-corner-right ui-combobox-toggle" )
.mousedown(function() {
wasOpen = input.autocomplete( "widget" ).is( ":visible" );
})
.click(function() {
input.focus();
// close if already visible
if ( wasOpen ) {
return;
}
// pass empty string as value to search for, displaying all results
input.autocomplete( "search", "" );
});
input.tooltip({
tooltipClass: "ui-state-highlight"
});
},
_destroy: function() {
this.wrapper.remove();
this.element.show();
}
});
})( jQuery );
$(function() {
	//$( "#snty" ).combobox();
	//$( "#lang" ).combobox();
	$('#collector').block({
		message: '<h3>Loading...</h3>', 
        css: { border: '3px solid #a00' } 
	});
});
$( "#format" ).buttonset({
	css: {
		padding: "0em 0em"
	}
});
$($('.ui-autocomplete-input')[0]).css('width','175px');
UpdateCounts();
var Tags12 = [
      "Station ID",
      "PSA",
      "Spoken Word Other",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ];
    

</script>
<form name="form1" id="form1" method="">
	<div id="352631d" style="width: inherit; padding-bottom: 3; padding-top: 3; border-bottom-style: solid; border-bottom: 1; border-color: #A5A5A5;">
		<table>
			<!--<tr id="head">
				<th style="width: 190px">Type</th>
				<th>Playlist</th>
				<th>Spoken</th>
				<th>Time</th>
				<th>Artist</th>
				<th>Title</th>
				<th>Album</th>-->
				<!--<th>Composer</th>
				<th>CC</th>
				<th>Hit</th>
				<th>Ins</th>-->
				<!--<th style="width: 180px;">Options</th>
				<th>language</th>
			</tr>-->
			<tr id="row1">
				<td id="type" style="width: 190px" autocomplete="on">
					<select id="snty" title="CRTC Logging Category" required>
						<option value="53">53, Sponsored Promotion</option>
                        <OPTION value="52">52, Sponsor Indentification</OPTION>
                        <OPTION VALUE="51">51, Commercial</OPTION>
                        <option value="45">45, Show Promo</option>
                        <option value="44">44, Programmer/Show ID</option>
                        <option value="43">43, Produced Stat. ID</option>
                        <option value="42">42, Tech Test</option>
                        <option value="41">41, Themes</option>
                        <option value="36">36, Experimental</option>
                        <option value="35">35, NonClassical Religious</option>
                        <option value="34">34, Jazz and Blues</option>
                        <option value="33">33, World/International</option>
                        <option value="32">32, Folk</option>
                        <option value="31">31, Concert</option>
                        <option value="24">24, Easy Listening</option>
                        <option value="23">23, Acoustic</option>
                        <option value="22">22, Country</option>
                        <option value="21" selected>21, Pop, Rock and Dance</option>
                        <option value="12">12, Spoken Word Other</option>
                        <option value="12">12P, PSA</option>
                        <OPTION VALUE="11">11, News</option>
					</select>
				</td>
				<td>
					<input type="text" min="0" max="9999" id="plin" placeholder="PL#" title="Playlist Number" maxlength="5" style="width: 30px;" name="playlist" />
				</td>
				<td>
					<input type="text" min="0" max="9999" id="spin" placeholder="Min" title="Spoken Minutes" style="width: 30px;" name="spoken"/>
				</td>
				<td>
					<input type="text" id="tiin" required style="width: 70px;" name="tiin" value="<?php echo date("h:i A"); ?>"/>
				</td>
				<td>
					<input type="text" id="Artist" style="width: 175px;" placeholder="Artist" title="Artist" name="artist"/> 
				</td>
				<td>
					<input type="text" id="title" style="width: 175px;" placeholder="Title" title="Title" name="title"/> 
				</td>
				<datalist id="cat12list">
					<option value="Station ID"/>
					<option value="PSA"/>
					<option value="Spoken Word Other"/>
				</datalist>
				<td>
					<input type="text" id="Album" style="width: 175px;" placeholder="Album" title="Album" name="album"/> 
				</td>
				<td id="format" style="width: 180px;">
					<input type="checkbox" id="cc" title="Canadian Content" name="cc"/><label for="cc">CC</label>
					<input type="checkbox" id="hit" title="Top 40 Hit" name="hit"/><label for="hit">Hit</label>
					<input type="checkbox" id="Ins" title="Instrumental" name="Ins"/><label for="Ins">Ins</label>
				</td>
				<td>
					<select id="lang" style="width: 75px">
						<option value="English" selected>English</option>
						<option value="French">French</option>
						<option value="Native">Native</option>
						<option value="Other">Other</option>
						<option value="None">None</option>
					</select>
				</td>
			</tr>
			<tr>
				<!--<label for="note">Notes (Not reported on audit)</label>-->
				<td>
					<input type="text" maxlength="17" id="refcode" title="Scan or Type barcode to retreive album information from records" disabled placeholder="Scan / Type Barcode" style="width: 97%" name="refcode" onfocus="javascript: return false;"/>
				</td>
				<td colspan="2"></td>
				<td>
					<input type="text" title="Year of significance to recording" maxlength="4" id="sgyear" placeholder="Year" style="width: 40px;" name="sgyear"/>
				</td>
				<td>
					<input type="text" id="Composer" placeholder="Composer" style="width: 97%;" name="composer"/>
				</td>
				<td colspan="3"><input type="text" placeholder="Notes" style="width: 99%" id="note" name="note" /></td>
				<td><input type="submit" id="subcol1" value="Submit" disabled/></td>
			</tr>
		</table>
	</div>
	<div id="barcode" style="display: none;">
		<form>
			<span>Scan / Type Bar Code</span>
			<input type="text" maxlength="20" id="scan_barcode" name="bcd"/>
			<input type="button" value="OK" />
			<input type="button" value="Cancel" onclick="javascript: $.unblockUI()" />
		</form> 
	</div>
</form>
<script>
	$('#form1').submit( function(){
		var title = $("input#title").val();
		var type = $("input#snty").val();
		var lang = $("input#lang").val();
		if (type == "") {
			growl("The song Type is required, please select a valid option")
			$('input#type').focus();
			return false
		}  
		if (lang == ""){
			growl("The Language is required, please select a valid option")
			$('input#lang').focus();
			return false
		}
        if (title == "") {  
      		//$("label#name_error").show();  
      			$.blockUI({ 
	            message: "<h1>Error</h1><p>Title is a required field</p>", 
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
      		$("input#title").focus();  
      		return false;  
    	}
  		var DataString = 'type=' + $('#snty').val() + '&title=' + title + '&artist=' + $('#Artist').val() + '&album=' + $('#Album').val() + '&time=' + $('#tiin').val() + '&composer=' + $('#Composer').val() + '&hit=' + $('input[name="hit"]:checked').length + '&cc='
  		+ $('input[name="cc"]:checked').length + '&ins=' + $('input[name="Ins"]:checked').length + '&lang=' + $('#lang').val() + '&note=' + $('#note').val() + '&playlist=' + $('#plin').val() + '&spoken=' + $('#spin').val();
  		//alert(DataString);
  		$.ajax({
	  		url: "AJAX/components/PostSong.php",
	  		type: "POST",
	  		//data: DataString,
	  		data: DataString,
	  		success: function(data) {
	    		//$('#domScratch').html(data);
	    		if(data.length>0){
	    			//growl(data);
	    			//loadlist();
	    			//$('#list').load('AJAX/components/list.php');
  					//setListSpinners();	
  					//$('#collector').load('AJAX/components/Collector.php');
  					closeSubmit();
	    		}
	  		 }
  		});
  		return false;
  	});
</script>