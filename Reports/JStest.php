<!DOCTYPE html>
<html>
 <head>
 <script type="text/javascript" src="/js/jquery-1.7.2.js"></script>
<script language="javascript">
$(document).ready(function(){
    $('#progress').hide();
    $("#main a.bgdiv").click(function(){
        $("#progress").show("slow");
        $("body").load($(this).attr('href'));
        return false;
    });
});
</script>
 </script>
 </head>

 <body>
 <div id="progress">
  <img src="progress-rotator.gif">
	</div>
	<div id="main">
   <a href="#" class="bgdiv">Click here for MySQL Backup</a>
</div>
 </body>
 </html>