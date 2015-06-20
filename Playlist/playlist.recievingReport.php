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
            unset($message);
        }
        else if($error==="1292"){
            $error_name = "Invalid Date Format";
            $error_message = "The date given in either Release date or Date In was invalid"
                    . "<br>Please use ISO8601 dates only (YYYY-MM-DD)";
            unset($message);
        }
        else if($error==="0"){
            $error_name="General Error";
            $error_message = "A Unknown Error Occured: ".$message;
            unset($message);
        }
        else{
            $error_name="General Error";
            $error_message = "A Unknown Error Occured: ".$message;
            unset($message);
        }
    }
?>
<h3 class="sub-header">Receiving Reports</h3>
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
<form action="javascript:get_records();" method="post">
<div class="panel panel-primary">
    <div class="panel-heading"><span>Report Settings</span>
        <input type="hidden" id="method_hidden" value="any" />
    </div>
    <div class="panel-body">
        <fieldset>
            <div class="col-md-3">
                <div id="ind_group" class="input-group">
                    <label for="reldate" class="input-group-addon">Start Date</label>
                    <input id="startDate" type="text" class="form-control" value="<?php print(date("Y-m-d",  strtotime('-8 days')));?>" name="start_date" tabindex="12"/>
                </div>
            </div>
            <div class="col-md-3">
                <div id="ind_group" class="input-group">
                    <label for="indate" class="input-group-addon">End Date</label>
                    <input id="endDate" type="text" class="form-control" value="<?php print(date("Y-m-d"));?>" name="end_date" tabindex="13"/>
                </div>
            </div>
        </fieldset>
    </div>
</div>
<div class="well">
    <div class="container-fluid">
        <div class="col-md-12">
            <input class="btn btn-primary btn-lg btn-block" type="submit" tabindex="27">
        </div>
    </div>
</div>
</form>    

<div>
    <table class="table table-striped table-hover table-condensed" id="table_print">
    </table>
</div>


<style>
    @media print {
        .navbar{
            display: none;
        }
        .sidebar{
            display: none;
        }
        .main{
            display: none;
        }
    }
</style>

<script type="text/javascript">
    function get_records(){
        alert("called");
        if($("#reldate").val()===""){
           $("#reldate").addClass(" has-error ");
           $("#table_print").html("<div class=\"alert alert-warning\" role=\"alert\">Empty Start Date</div>");
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
</script>
