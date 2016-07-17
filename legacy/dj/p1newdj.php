<?php
session_start();
//Used to silence annoying warnings so we can load the proper timezone...
date_default_timezone_set('UTC');
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/phpstyle.css" />
<title>DPL Administration</title>
</head>
<html>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center"><img src="<?php print("../../".$_SESSION['logo']);?>" alt="logo"/></td>
      </tr>
      <tr style="background-color:white;">
      <td>
        <table align="left" border="0" height="100" width="1000">
        <tr><td colspan="100%">
        <h2>New DJ</h2>
        </td></tr>
        <tr><th colspan="2" width="40%">
        Name
        </th><th width="35%">
        On-Air Name (optional)
        </th><th width="15%">
        Year Joined
        </th><th width="10%">
        Active
        </th>
        </tr>
             <form name="newdj" action="./p2newdj.php" method="post">
             <tr>
             <td colspan="2">
                 <input name="name" type="text" size="50%" required="true" autofocus="true" />
             </td>
             <td>
                 <input name="alias" type="text" size="50%"/>
             </td>
             <td>
                 <input name="year" type="text" size="10%" value="<?php echo date('Y'); ?>"/>
             </td>
             <td>
                 <input name="active" type="checkbox" checked="checked" size="10%"/>
             </td>

            <td align="left">
                <input type="submit" value="Create" size="10%"/>
                </form>
            </td>
          </tr>
		<tr><td colspan="100%"><hr /></td></tr>
            <tr>
                <form method="get" action="/">
                    <td><input type="submit" value="Dashboard"></td>
                </form>
            </tr>
        </table>

</td></tr>
</table>
</body>
</html>
