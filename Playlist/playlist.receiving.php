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
            <div class="input-group">
                <label class="input-group-addon" for="artist">Artist<span style="color:red">*</span></label>
                <input name="artist" id="art_field" type="text" list="artists" placeholder="Start typing artist name to retrieve list" autocomplete="on" class="form-control" autofocus="autofocus">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button">Go!</button>
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
<script type="text/javascript">
    $(document).ready({
        
    })
    
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