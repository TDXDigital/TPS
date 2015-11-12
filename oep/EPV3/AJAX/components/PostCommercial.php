<?php

function to12hour($hour1){ 
	// 24-hour time to 12-hour time 
	return DATE("g:i a", STRTOTIME($hour1));
}
function to24hour($hour2){
	// 12-hour time to 24-hour time 
	return DATE("H:i", STRTOTIME($hour2));
}

	session_start();
	
	$program = addslashes($_SESSION['program']);
	$callsign = addslashes($_SESSION['callsign']);
	$date = addslashes($_SESSION['date']);
	$pgmtime = addslashes($_SESSION['time']);
	
	$title = addslashes($_POST['title']);
	$album = addslashes($_POST['album']);
	$artist = addslashes($_POST['artist']);
	$composer = addslashes($_POST['composer']);
	$cancon = addslashes($_POST['cc']);
	$hit = addslashes($_POST['hit']);
	$ins = addslashes($_POST['ins']);
	$lang = addslashes($_POST['lang']);
	$timeraw = addslashes($_POST['time']);
	$spoken = addslashes($_POST['spoken']);
	$playlist = addslashes($_POST['playlist']);
	$type = addslashes($_POST['type']);
	$note = addslashes($_POST['note']);
	$adnum = addslashes($_POST['adnum']);
	
	$time = to24hour($timeraw);
	$con = mysql_connect('localhost',$_SESSION['usr'],$_SESSION['rpw']);
	$friends = array();
	if (!$con){
		die("<h2>Error " . mysql_errno() . "</h2><p>Could not establish connection to database. Authentication failed</p>");
	}
	else{
		if(!mysql_select_db("CKXU")){
			die("<h2>Error " . mysql_errno() . "</h2><p>User Access Error. Database refused connection</p>");
		}
		//$query = "INSERT INTO song (callsign,programname,starttime,date,title) VALUES ('".$callsign."','".$program."','".$pgmtime."','".$date."','".$title."')";
		//if($_POST['title']!=""){
	      
	      $indyns = "INSERT INTO song (callsign, programname, date, starttime";
	      $BUFFS = "'" . $callsign . "' , '" . $program . "' , '" . $date . "' , '" . $pgmtime . "'";
	      if ($ins!=""){
	        $indyns.=", instrumental";
	        $BUFFS.=", '".$ins."' ";
	      }
	      if ($time!=""){
	        $indyns.=", time";
	        $BUFFS.=", '" . $time . "' ";
	      }
		  
	      if ($title!=""){
	        	if($type=="51"){
	        		$QRR = mysql_fetch_array(mysql_query("select AdName, Language from adverts where AdId='".$title."' "));
					$BUFFS.=", '" . $QRR['AdName'] . "' ";
	        	}
				else{
					$BUFFS.=", '" . $title . "' ";
				}
	        $indyns.=", title";
	      }
	      if ($album!=""){
	        $indyns.=", album";
	        $BUFFS.=", '" . $album . "' ";
	      }
	      if ($composer){
	        $indyns.=", composer";
	        $BUFFS.=", '" . $composer . "' ";
	      }
	      if ($note!=""){
	        $indyns.=", note";
	        $BUFFS.=", '" . $note . "' ";
	      }
		  if ($spoken!=""){
	        $indyns.=", Spoken";
	        $BUFFS.=", '" . $spoken . "' ";
	      }
	      if ($artist){
	        $indyns.=", artist";
	        $BUFFS.=", '" . $artist . "' ";
	      }
	      if ($cancon!=""){
	        $indyns.=", cancon";
	        $BUFFS.=", '".$cancon."' ";
	      }
	      if ($playlist!=""){
	        $indyns.=", playlistnumber";
	        $BUFFS.=", '" . $playlist . "' ";
	      }
	      if ($type!=""){
	      	if($type=='51'){
	      		if($adnum!=""){
	      								
						// UPDATE Playcount
						$SPupSQL = "select SponsId from program where programname='" . $program . "' and callsign='" . $callsign . "' and SponsId is not null";
						if(!$SPup = mysql_query($SPupSQL)){
							array_push($error, mysql_errno() . "</td><td>" . mysql_error()); 
						}
						//echo mysql_num_rows($SPup);
						if(mysql_num_rows($SPup)==0){
	              			$UPAD = "update adverts set Playcount=Playcount+1 where AdId=\"" . $adnum . "\" ";
							$ADQN = mysql_query("select XREF from adverts where AdId='" . $adnum . "' and XREF IS NOT NULL");
							if(mysql_num_rows($ADQN)!=0){
								$XREF=mysql_fetch_array($ADQN);
								$UPXREF = "update adverts set Playcount=Playcount+1 where AdId=\"" . $XREF['XREF'] . "\" ";
							}
							/*else{
							 	//Not Required to report as many ads do not have XREF
								//array_push($error, mysql_errno() . "</td><td>" . mysql_error());
								array_push($error,"999</td><td> XREF not Defined (ignore for now)"); 
							}*/
								// SET FLAG IF NOT AVAILABLE
	              			$result_Flag = mysql_query("select Playcount from adverts where AdId='" . $adnum . "' and Category='51'");
	              			$FlCheck = mysql_fetch_array($result_Flag);
							//echo $FlCheck['Playcount'];
							$Sel51Flag = $minplaysql51 = "select MIN(Playcount) from adverts where Category='51' and Active='1' and Friend='1' ";
							$Min51Flag = mysql_query($Sel51Flag);
							$flagLevel = mysql_fetch_array($Min51Flag);
							//echo $flagLevel['MIN(Playcount)'];
							if($FlCheck['Playcount']>$flagLevel['MIN(Playcount)']){
								$indyns.=", AdViolationFlag";
	                			$BUFFS.=", '1' ";
							}
							
							if(!mysql_query($UPAD)){
								echo "AD ERROR".mysql_error();
							}
							else{
								if($UPXREF!=""){
									if(!mysql_query($UPXREF)){
										echo $UPXREF;
										echo "XREF ERROR:" . mysql_error();
									}
								}
							}
						}
						
				}
				
	      	}
	        $indyns.=", category";
	        $BUFFS.=", '" . $type . "' ";
	      }
	      /*if ($hit!=""){
	        $indyns.=", hit";
	        $BUFFS.=", '".$hit."' ";
	      }*/
	      $BUFFS.=" )";
	      $indyns.=") values ( ";
	      $DYNAMIC = $indyns . $BUFFS;
		  echo $DYNAMIC;
	      if(!mysql_query($DYNAMIC,$con))
	      {
	      	echo $DYNAMIC;
	        echo 'SQL Error: ';
	        echo mysql_error();
	      }
	      else //This is executed if the song is inserted
	      {
	      			$LASTLINK =  mysql_insert_id($con);
		  			if(!isset($QRR['Language'])){
		  				$LANGIN = $lang;
					}
					else{
						$LANGIN = $QRR['Language'];
					}
	                  $langDef = "insert into language (callsign, programname, date , starttime, songid , languageid ) values ('" . $callsign . "', '". $program ."', '" . $date . "', '". $pgmtime . "', '" . addslashes($LASTLINK) . "', '" . $LANGIN . "')";
	                  if(!mysql_query($langDef,$con))
	                  {
	                      echo 'SQL Error, Language Insertion<br />';
	                      echo mysql_error();
	                  }
	      }
	    //}
		
		if(!mysql_error()){
			//echo "No Errors";
			//echo mysql_error();
			echo "
			<script>
				growl('Ad Sent<br/>".htmlspecialchars($DYNAMIC)."');
				//alert('".$DYNAMIC."');
			</script>
			";
		}
		else{
			echo mysql_error();
		}
	}
?>