<?php
    include_once "../TPSBIN/functions.php";
    sec_session_start();
?>
<html>
<body>

<form action="upload_playlist.php" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>

</body>
</html>