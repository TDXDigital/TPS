<?php
    if(!$mysqli){
        die("<p>Error: No Database Connection</p>");
    }
?>
<p>Please complete the following form</p>
<h3 class="sub-header">New Traffic Entity</h3>
<form action="traffic.create_ad.php" method="post">
<div class="panel panel-primary">
    <div class="panel-heading">Client Information</div>
        <div class="panel-body">
            <fieldset>
                <label for="client_num">Client<span style="color:red">*</span></label>
                <select id="client_num" name="client" required>
                    <option value="">Select One</option>
                <?php
                    $CLIENTS = $mysqli->query("SELECT ClientNumber,Name FROM clients WHERE ( Status='ACT' OR Status='INT' ) Order By Name ASC");
                    while($client = $CLIENTS->fetch_array()){
                        echo "<option value='".$client['ClientNumber']."'>".$client['Name']."</option>
                        ";
                    }
                ?>
                </select>
            </fieldset>
        </div>
    </div>

<div class="panel panel-info">
    <div class="panel-heading">Traffic Information</div>
        <div class="panel-body">
            <fieldset>
                <label for="ad_name">Advertisement Name<span style="color:red">*</span></label>
                <input type="text" id="ad_name" name="name" required/><br>
                <label for="ad_length">Length (minutes)<span style="color:red">*</span></label>
                <input type="number" id="ad_length" name="length" step="1" required/><br>
                <label for="ad_language">Language</label>
                <input type="text" value="English" required>
                <br>
                <label for="ad_start">Start Date<span style="color:red">*</span></label>
                <input type="date" name="ad_start" value="<?php echo date("Y-m-d");?>" required/><br>
                <label for="ad_end">End Date</label>
                <input type="date" name="ad_end" value="<?php echo date("Y-m-d",strtotime("+1 year"));?>"/>
                <br>
                <label for="ad_friend">Friend Program</label>
                <input type="checkbox" id="ad_friend" name="friend" checked/><br>
                <label for="ad_rate">Invoicing Rate</label>
                <select name="ad_rate">
                    <option value="default">Default</option>
                    <option value="None">None</option>
                </select>
                <br><br>
                <label for="ad_file">File (mp3, wav, flac)</label>
                <input type="file" id="ad_file" name="file" disabled/>
            </fieldset>
        </div>
    </div>
    <div class="well">
        <input type="submit">
    </div>
    </form>
<!--</div>-->
    
