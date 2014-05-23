<?php
    if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
    if(isset($_GET['m'])){
        $message=urldecode($_GET['m']);
    }
?>
<p>Please complete the following form</p>
<h3 class="sub-header">New Traffic Entity</h3>
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
<form action="traffic.verify.php" method="post">
<div class="panel panel-primary">
    <div class="panel-heading">Please Select the type of verification you wish to perform</div>
    <div class="panel-body">
        <fieldset>
            <label for="type">Type<span style="color:red">*</span></label>
            <select id="client_num" name="client" required>
                <option value="">Select One</option>
                <option value="advount">Traffic Counts</option>
            </select>
        </fieldset>
    </div>
</div>

<div class="panel panel-info">
    <div class="panel-heading">Settings</div>
    <div class="panel-body">
        <fieldset>
            <label for="ad_start">Start Date<span style="color:red">*</span></label>
            <input type="date" name="start" value="2014-05-22" required=""><br>
            <label for="ad_end">End Date</label>
            <input type="date" name="end">
            <br><br>
            <label for="ad_friend">Friend Program only</label>
            <input type="checkbox" id="ad_friend" name="friend" checked=""><br>
        </fieldset>
    </div>
</div>
<div class="well">
    <input type="submit">
</div>
</form>
    
