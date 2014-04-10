<?php
    include_once "../TPSBIN/functions.php";
    sec_session_start();
?>
<html>
    <head>
        <script src="../js/jquery/js/jquery-2.0.3.min.js" ></script>
        <script src="../js/jquery/js/jquery-ui-1.10.0.custom.min.js" ></script>
        <link href="../js/jquery/js/css/trontastic/jquery-ui-1.10.0.custom.min.css"/>
        <meta title="Upload Playlist"/>
    </head>
<body>
    <script>
        function submitted(){
            $("#processing").show();
        }
    </script>
<form action="upload_playlist.php" method="post" onsubmit="submitted();"
enctype="multipart/form-data">
    <fieldset title="XLS / XLSX Library Update">
        <label for="file">Filename:</label>
        <input type="file" name="file" id="file"><br>
        <fieldset title="Range">
            <label for="start">Start Row</label>
            <input type="number" name="START" step="1" min="1" id="start"><br>
            <label for="end">End Row (blank for all)</label>
            <input type="number" name="END" step="1" min="1" id="end"><br>
        </fieldset>
        <input type="submit" name="submit" value="Submit">
    </fieldset>
</form>
    <div id="processing" style="display: none;"><progress></progress>Working...</div>

</body>
</html>