<?php
    if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
    if(isset($_GET['m'])){
        $message=urldecode($_GET['m']);
    }
?>
<h3 class="sub-header">Induction</h3>
<?php
    if(isset($message)){
        echo "<div class=\"panel panel-success\">
    <div class=\"panel-heading\">Message Information</div>
        <div class=\"panel-body\">
            <span>$message</span>
        </div>
    </div>";
    }
?>
<form action="playlist.induct.php" method="post">
<div class="panel panel-primary">
    <div class="panel-heading">Basic Information</div>
    <div class="panel-body">
        <fieldset>
            <div id="art-group" class="input-group">
                <label class="input-group-addon" for="art_field">Artist<span style="color:red">*</span></label>
                <input name="artist" id="art_field" type="text" required list="artists" placeholder="Start typing artist name to retrieve list" autocomplete="on" class="form-control" autofocus="autofocus" onkeyup="catch_enter(event);" tabindex="1">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="get_albums();return true;">Go!</button>
                </span>
            </div>
            <div id="table_display"></div>
            <br>
            <div id="alb_group" class="input-group">
                <label class="input-group-addon" for="alb_field">Album<span style="color:red">*</span></label>
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" tabindex="3" onclick="self_titled()">Self Titled</button>
                </span>
                <input name="artist" id="alb_field" type="text" required list="artists" placeholder="Start typing artist name to retrieve list" autocomplete="on" class="form-control" autofocus="autofocus" tabindex="2">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="get_albums();return true;">Go!</button>
                </span>
            </div>
        </fieldset>
    </div>
</div>
<!--
<div class="panel panel-info">
    <div class="panel-heading">Traffic Information</div>
    <div class="panel-body">
        <fieldset>
            <label for="ad_name">Advertisement Name<span style="color:red">*</span></label>
            <input type="text" id="ad_name" name="name" required/><br>
            <label for="ad_category">CRTC Category<span style="color:red">*</span></label>
            <select id="ad_category" name="category">
                <option value="51" selected>Commercial (51)</option>
                <option value="52">Sponsor Identification (52)</option>
                <option value="53">Sponsored Promotion (53)</option>
                <option value="12">PSA (12)</option>
                <option value="45">Show Promo (45)</option>
            </select><br>
            <label for="ad_length">Length (minutes)<span style="color:red">*</span></label>
            <input type="number" id="ad_length" name="length" step="1" required/><br>
            <label for="ad_language">Language</label>
            <input type="text" value="English" name="language" required>
            <br>
            <label for="ad_start">Start Date<span style="color:red">*</span></label>
            <input type="date" name="start" value="<?php echo date("Y-m-d");?>" required/><br>
            <label for="ad_end">End Date</label>
            <input type="date" name="end" value="<?php echo date("Y-m-d",strtotime("+1 year"));?>"/>
            <br><br>
            <label for="ad_friend">Friend Program</label>
            <input type="checkbox" id="ad_friend" name="friend" checked/><br>
            <label for="ad_rate">Invoicing Rate</label>
            <select id="ad_rate" name="rate">
                <option value="default">Default</option>
                <option value="None">None</option>
            </select>
            <br><br>
            <label for="ad_file">File (mp3, wav, flac)</label>
            <input type="file" id="ad_file" name="file" disabled/>
        </fieldset>
    </div>
</div>
-->
<div class="well">
    <input type="submit">
</div>
</form>
<datalist id="artists"></datalist>
    

<style>
  .ui-autocomplete-loading {
    background: white url("../images/GIF/ajax-loader3.gif") right center no-repeat;
  }
</style>
  
<!--<script type="text/javascript" src="../js/jquery/js/jquery-2.1.1.min.js"></script>-->


<script type="text/javascript">
    $(function(){
        $('#art_field').autocomplete({
            //source: "../MusicLib/DB_Search_Artist.php",
            source: "AJAX/playlist.get_artist.php",
            minLength: 2
        });
        document.querySelector('form').onkeypress = checkEnter;
        //$("#art_field").keyup(catch_enter());
    });
    
    function self_titled(){
        $("#alb_field").val($("#art_field").val());
    }
    
    function catch_enter (event){
        if(event.keyCode === 13){
            get_albums();
            return true;
        }
    }
    
    function get_albums(){
        if($("#art_field").val()===""){
           $("#art_group").addClass(" has-error ");
           $("#table_display").html("<div class=\"alert alert-warning\" role=\"alert\">Empty Artist Field</div>");
           return;
        }
        var input = {'term':$("#art_field").val()};
        $.ajax({
            url: "AJAX/playlist.get_album_table.php",
            data: input,
            type: "GET"
        }).done(function(html_returned){
            $("#table_display").html(html_returned);
        });
    }
    
    function checkEnter(e){
        e = e || event;
        var txtArea = /textarea/i.test((e.target || e.srcElement).tagName);
        return txtArea || (e.keyCode || e.which || e.charCode || 0) !== 13;
    }
    //var query = {"val":$.("#art_field").val()};
    /*
    $.ajax{
        url: "AJAX/playlist.get_artist.php",
        dataType: "json",
        data: query,
        type: "POST"
    }.done({
        
    })*/
</script>