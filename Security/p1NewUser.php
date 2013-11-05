<?php
//"select * from adverts left join song on (adverts.AdName = song.title and song.category='51' and song.date between '2012-08-24' and '2012-08-31')"
    session_start();

function gen_trivial_password($len = 6)
{
    $r = '';
    for($i=0; $i<$len; $i++)
        $r .= chr(rand(0, 25) + ord('a'));
    return $r;
}

$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysql_error() . ';  

	username=' . $_SESSION["username"]);
}
else if($con){
	if(!mysql_select_db("CKXU")){header('Location: /login.php');}
	
    }
else{
	echo 'ERROR!';
}

?>

<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="/altstyle.css" />
<title>Statistics</title>
</head>
<html>
<body>
	<div class="topbar">
           User: <?php echo(strtoupper($_SESSION['usr'])); ?>
    </div>
	<div id="header">
		<a href="#"><img src="/images/Ckxu_logo_PNG.png" alt="CKXU" /></a>
	</div>
	<div id="top">
		<h2>New User Account / Member</h2>
	</div>
	<div id="top" class="notice">
		<span>INFORMATION / NOTICE</span>
	</div>
	<div id="content">
		<table width="1000px">
			<tr>
				<th>
					Account Type
				</th>
				<th>
					Access Level
				</th>
				<th>
					Username
				</th>
				<th>
					Auto Generated Password
				</th>
				<th>
					Password
				</th>
				<th>
					Confirm Password
				</th>
				<th>
					Active
				</th>
			</tr><tr>
				<td>
					<select name="AcctType">
						<option value="1">Member</option>
						<option value="2">Employee</option>
						<option value="3">Friend</option>
					</select>
				</td>
				<td>
					<select name="AccLevel">
						<option value="S">Standard</option>
						<option vlaue="E">Elevated</option>
						<option value="N">None</option>
						<option value="A">Admin</option>
					</select>
				</td>
				<td>
					<input name="UserID" type="text" value="<?php
					$SQLQ = "SELECT Auto_increment 
					FROM information_schema.tables 
					WHERE table_name='users'
					AND table_schema = DATABASE();";
					$AUTO = mysql_fetch_array(mysql_query($SQLQ));
					//echo mysql_error();
					echo $AUTO['Auto_increment'];
					?>" />
				</td>
				<td>
					<input type="text" value="<?php $PASS = gen_trivial_password(8);
					echo $PASS ?>" readonly="readonly"/>
				</td>
				<td>
					<input type="password" name="CORE" value="<?php $PASS = gen_trivial_password(8);
					echo $PASS ?>"/>
				</td>
				<td>
					<input type="password" name="ALTERNATE" value="<?php $PASS = gen_trivial_password(8);
					echo $PASS ?>"/>
				</td>
				<td>
					<select>
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div 
</body>
</html>