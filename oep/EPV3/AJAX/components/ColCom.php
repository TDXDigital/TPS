
<!-- COLCOM (Collector, Commercials) -->

<form name="form2" id="form2" method="">
	<div id="352631d" style="width: inherit; padding-bottom: 3; padding-top: 3; border-bottom-style: solid; border-bottom: 1; border-color: #A5A5A5;">
		<table>
			<tr id="head">
				<th>Type</th>
				<th>Playlist</th>
				<th>Spoken</th>
				<th>Time</th>
				<th>Title</th>
				<th>Artist</th>
				<th>Album</th>
				<th>Composer</th>
				<th>CC</th>
				<th>Hit</th>
				<th>Ins</th>
				<th>language</th>
			</tr>
			<tr id="row1">
				<td id="type">
					<select id="snty">
						<option value="53">53, Sponsored Promotion</option>
                        <OPTION value="52">52, Sponsor Indentification</OPTION>
                        <OPTION VALUE="51" Selected>51, Commercial</OPTION>
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
                        <option value="21">21, Pop, Rock and Dance</option>
                        <option value="12">12, Spoken Word Other</option>
                        <option value="12">12P, PSA</option>
                        <OPTION VALUE="11">11, News</option>
					</select>
				</td>
				<td>
					<input type="text" min="0" max="9999" id="plin" style="width: 30px;" name="playlist" />
				</td>
				<td>
					<input type="text" min="0" max="9999" id="spin" style="width: 30px;" name="spoken" />
				</td>
				<td>
					<input type="text" id="tiin" style="width: 60px;" name="tiin" value="<?php echo date("h:i A"); ?>"/>
				</td>
				<td>
					<input type="text" id="title" style="width: 175px;" name="title"/> 
				</td>
				<td>
					<input type="text" id="Artist" style="width: 175px;" name="artist"/> 
				</td>
				<td>
					<input type="text" id="Album" style="width: 175px;" name="album"/> 
				</td>
				<td>
					<input type="text" id="Composer" style="width: auto;" name="composer"/> 
				</td>
				<td>
					<input type="checkbox" id="cc" name="cc"/>
				</td>
				<td>
					<input type="checkbox" id="hit" name="hit"/>
				</td>
				<td>
					<input type="checkbox" id="Ins" name="Ins"/>
				</td>
				<td>
					<select id="lang">
						<option value="English" selected>English</option>
						<option value="French">French</option>
						<option value="Native">Native</option>
						<option value="Other">Other</option>
					</select>
				</td>
			</tr>
			<tr>
				<!--<label for="note">Notes (Not reported on audit)</label>-->
				<td>Notes (Not reported on audit)</td>
				<td colspan="10"><input type="text" style="width: 99%" id="note" name="note" /></td>
				<td><input type="submit" id="submit" value="Submit"/></td>
			</tr>
		</table>
	</div>
</form>
<script>
	$('#form2').submit( function(){
		var title = $("input#title").val();  
        if (title == "") {  
      		//$("label#name_error").show();  
      			$.blockUI({ 
	            message: "<div ></div><h1>Error</h1><p>Title is a required field</p>", 
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
	    			growl(data);
	    		}
	  		 }
  		});
  		return false;
  	});
</script>