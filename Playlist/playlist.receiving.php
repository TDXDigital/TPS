<?php
    if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
    if(isset($_GET['m'])){
        $message=urldecode($_GET['m']);
    }
    if(isset($_GET['e'])){
        $error = filter_input(INPUT_GET, 'e', FILTER_SANITIZE_NUMBER_INT);
        
        if($error==="1062"){
            $error_name = "Duplicate entry";
            $error_message = "You cannot insert the same album, artist, and format"
                    . "more than once per day<br>if you really need to record"
                    . "this entry please change the recieving day";
        }
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
    if(isset($error)){
        echo "<div class=\"panel panel-danger\">
    <div class=\"panel-heading\">Error $error: $error_name</div>
        <div class=\"panel-body\">
            <span>$error_message</span>
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
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="SetVariousArtists()">VA Comp</button>
                </span>
                <!-- TODO: Remove 'THE' from the beginning of queries if given -->
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
                    <button class="btn btn-default" type="button" tabindex="2" onclick="self_titled()">Self Titled</button>
                </span>
                <input name="album" id="alb_field" type="text" required placeholder="Start typing album name to retrieve list" autocomplete="on" class="form-control" tabindex="3">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="get_albums();return true;">Go!</button>
                </span>
            </div>
        </fieldset>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div id="gen_group" class="input-group">
                    <select id="genre" name="genre" class="chosen-select" tabindex="4" data-placeholder="Album Genre" style="min-width: 150px;">
                        <option value="null"></option>
                        <?PHP
                        $genres = array(
                            "A"=>"Alternative",
                            "AR"=>"AltRock",
                            "BL-R"=>"Blues Rock",
                            "DA"=>"Dance",
                            "EL"=>"Electronica",
                            "FO-R"=>"Folk Rock",
                            "HH"=>"HipHop",
                            "HR"=>"Hard Rock",
                            "JZ-R"=>"JazzRock",
                            "ML"=>"Metal",
                            "P"=>"Pop",
                            "PR"=>"Punk Rock",
                            "21"=>"CRTC 21 General",
                            "22"=>"CRTC 22 General",
                            "23"=>"CRTC 23 General",
                            "24"=>"CRTC 24 General",
                            "31"=>"CRTC 31 General",
                            "32"=>"CRTC 32 General",
                            "33"=>"CRTC 33 General",
                            "34"=>"CRTC 34 General"
                            );
                        
                        foreach($genres as $key => $value ){
                            print "<option value='".$key."'>".$value."</option>";
                                    
                        }
                        ?>
                    </select>
                    <br>
                </div>
            </div>
            <div class="col-md-3">
                <div id="ind_group" class="input-group">
                    <label for="indate" class="input-group-addon">Date In</label>
                    <input id="indate" type="text" class="form-control" value="<?php print(date("Y-m-d"));?>" name="indate" tabindex="5"/>
                </div>
            </div>
            <div class="col-md-3">
                <div id="ind_group" class="input-group">
                    <label for="label" class="input-group-addon">Label</label>
                    <input id="label" type="text" class="form-control" required="required" list="labels" name="label" tabindex="6" placeholder="Label"/>
                </div>
            </div>
            <div class="col-md-3">
                <div id="format" class="input-group">
                    <label for="label" class="input-group-addon">Format</label>
                    <select class="form-control" name="format" tabindex="7">
                        <option>CD</option>
                        <option>Cassette</option>
                        <option>7"</option>
                        <option>12"</option>
                        <option>Digital</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <input type="checkbox" data-label-prepend="Various Artists" class="style3" name="print" value="1" tabindex="8">
            </div>
            <div class="col-md-2">
                <input type="checkbox" data-label-prepend="Accepted" class="style3" name="accept" checked="checked" value="1" tabindex="9">
            </div>
            <div class="col-md-2">
                <input type="checkbox" data-label-prepend="Print" class="style3" name="print" checked="checked" value="1" tabindex="10">
            </div>
        </div>
    </div>
</div>
    <datalist id="labels">
        
    </datalist>
<div class="panel panel-info">
    <div class="panel-heading">Additional Information</div>
    <div class="panel-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div id="web_group" class="input-group">
                        <label for="website" class="input-group-addon" title="Band Website"><span class="glyphicon glyphicon-globe"></span></label>
                        <input id="website" type="url" class="form-control" disabled="disabled" name="website" tabindex="13" placeholder="www.bandwebsite.com"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="bnc_group" class="input-group">
                        <label for="bandcamp" class="input-group-addon" title="BandCamp URL"><span class="glyphicon glyphicon-tent"></span></label>
                        <input id="bandcamp" type="url" class="form-control"  disabled="disabled" name="bandcamp" tabindex="14" placeholder="Bandcamp.com"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div id="bnc_group" class="input-group">
                        <label for="fb" class="input-group-addon" title="FaceBook URL"><span class="glyphicon glyphicon-user"></span></label>
                        <input id="fb" type="url" class="form-control" disabled="disabled"  name="facebook"tabindex="15" placeholder="Facebook.com"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="bnc_group" class="input-group">
                        <label for="tw" class="input-group-addon" title="Twitter URL"><span class="glyphicon glyphicon-bell"></span></label>
                        <input id="tw" type="url" class="form-control" disabled="disabled"  name="website" tabindex="16" placeholder="Twitter.com"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="well">
    <div class="container-fluid">
        <div class="col-md-8">
            <input class="btn btn-primary btn-lg btn-block" type="submit" tabindex="17">
        </div>
        <div class="col-md-4">
            <input id="print_btn" class="btn btn-default btn-lg btn-block" onclick="window.open('printtest.php', '_blank');" <?php
            if(!isset($_SESSION['PRINTID'])){
            echo "disabled=\"disabled\" ";
            }
            ?> type="button" value="Print Labels" tabindex="18">
        </div>
    </div>
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
        $('#label').autocomplete({
            //source: "../MusicLib/DB_Search_Artist.php",
            source: "AJAX/getlabels.php"
        });
        
        document.querySelector('form').onkeypress = checkEnter;
        $.datepicker.setDefaults({ dateFormat: 'yy-mm-dd' });
        $( "#indate" ).datepicker();
        //update_labels()
        //$("#art_field").keyup(catch_enter());
        $('input[type="checkbox"].style1').checkbox({
            buttonStyleChecked: 'btn-success',
            checkedClass: 'icon-check',
            uncheckedClass: 'icon-check-empty'
        });
        $('input[type="checkbox"].style2').checkbox({
            buttonStyle: 'btn-base',
            buttonStyleChecked: 'btn-success',
            checkedClass: 'icon-check',
            uncheckedClass: 'icon-check-empty'
        });
        $('input[type="checkbox"].style3').checkbox({
            buttonStyle: 'btn-danger',
                buttonStyleChecked: 'btn-success',
            checkedClass: 'icon-check',
            uncheckedClass: 'icon-check-empty'
        });
    });
    
    function self_titled(){
        $("#alb_field").val($("#art_field").val());
    }
    
    function SetVariousArtists(){
        
    }
    
    function catch_enter (event){
        if(event.keyCode === 13){
            get_albums();
            return true;
        }
        else{
            return false;
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
    
    function update_labels(){
        // Get the <datalist> and <input> elements.
        var dataList = document.getElementById('labels');
        var input = document.getElementById('label');
        
        // Create a new XMLHttpRequest.
        var request = new XMLHttpRequest();

        // Handle state changes for the request.
        request.onreadystatechange = function(response) {
            if (request.readyState === 4) {
                if (request.status === 200) {
                // Parse the JSON
                var jsonOptions = JSON.parse(request.responseText);
                

                // Loop over the JSON array.
                jsonOptions.forEach(function(item) {
                    // Create a new <option> element.
                    var option = document.createElement('option');
                    // Set the value using the item in the JSON array.
                    option.value = item;
                    // Add the <option> element to the <datalist>.
                    dataList.appendChild(option);
                });

                // Update the placeholder text.
                input.placeholder = "Lables";
                } else {
                    // An error occured :(
                    input.placeholder = "no response";
                }
            }
        };
        dataList.innerHTML="";
        // Update the placeholder text.
        input.placeholder = "Loading labels...";
            
        
        // Set up and make the request.
        request.open('GET', 'AJAX/getlabels.php?term='+input.value, true);
        request.send();
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

<?php
foreach($_SESSION['PRINTID']as$value){
    echo $value. "<br>";
}
?>