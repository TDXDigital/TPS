<br />
<div class="footer">&copy;
<?php 
global $stationName;
$copyYear = 2009; //your company's starting year
$curYear = date('Y'); 
echo $copyYear . (($copyYear != $curYear) ? '-' . $curYear : ''); print "&nbsp;" . $stationName; ?>, All Rights Reserved.&nbsp;&nbsp;</div>
</div>
</body>
</html>