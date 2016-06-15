<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$COUNT = 0;
 foreach ($_FILES['file']['name'] as $filename) {
	     
	 if ($_FILES["file"]["error"][$COUNT] > 0)
	   {
	   echo "Error: " . $_FILES["file"]["error"] . "<br />";
	   }
	 else
	   {
	   echo "Upload: " . $_FILES["file"]["name"][$COUNT] . "<br />";
	   echo "Type: " . $_FILES["file"]["type"][$COUNT] . "<br />";
	   echo "Size: " . ($_FILES["file"]["size"][$COUNT] / 1024 / 1024) . " Mb<br />";
	   echo "Temp file: " . $_FILES["file"]["tmp_name"][$COUNT] . "<br />";
	
	   if (file_exists("S:\Week_". date('W') . "_Yr_" . date('Y') . "/" . $_FILES["file"]["name"][$COUNT]))
	   {
	    	echo $_FILES["file"]["name"][$COUNT] . " already exists. ";
	   }
	   else
	   {
	   		if(!file_exists("S:\Week_". date('W') . "_Yr_" . date('Y') . "/")){
	   			mkdir("S:\Week_". date('W') . "_Yr_" . date('Y') . "/");
			}
	    	if(move_uploaded_file($_FILES["file"]["tmp_name"][$COUNT], "S:\Week_". date('W') . "_Yr_" . date('Y') . "/" . $_FILES["file"]["name"][$COUNT]))
			{
				echo "Stored in: " ."S:\Week_". date('W') . "_Yr_" . date('Y') . "/" . $_FILES["file"]["name"][$COUNT];
			}
			else{
				echo "Directory: " . "S:\Week_". date('W') . "_Yr_" . date('Y') . "/" . $_FILES["file"]["name"][$COUNT]; 
				echo "<br />A Error has occured, the file could not be saved<br />";
				//echo error_get_last();
			}
	   }
	  }
	   ++$COUNT;
	 }
 ?>