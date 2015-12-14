<?php
    include_once "../TPSBIN/functions.php";
    //include_once "../TPSBIN/db_connect.php";
    if(file_exists("../TPSBIN/XML/DBSETTINGS.xml")
            && !key_exists("max_page", $_SESSION)){
        http_response_code(403);
        $refusal = "<h1>403 Forbidden</h1><p>Your request cannot proceed as the"
                . " this server has already been configured.</p>";
        die($refusal);
    }
    if(!isset($_SESSION)){
        sec_session_start();
    }
    $arg1=filter_input(INPUT_GET,'q',FILTER_SANITIZE_STRING);
    $max=filter_input(INPUT_GET,'e',FILTER_SANITIZE_STRING);
    
    $PAGES=[['wel','Welcome','?q=wel'],['lic','EULA','?q=lic'],['db','Database','?q=db'],['auth','Auth','?q=auth'],['settings','Settings','?q=settings'],['review','Review','?q=review'],['install','Install','?q=install'],['done','Complete','?q=complete']];
    $chained= TRUE;
    
    
    if(isset($arg1)){
        $PAGE = $arg1 ?: $PAGES[0][0];
    }
    else{
        $PAGE = "wel";
    }
    $enabled=[];
    if(!isset($_SESSION['max_page'])){
        $_SESSION['max_page']=0;
    }
    

    $chain_break=FALSE;
    $i=0;
    foreach ($PAGES as $entity){
        $e1=TRUE;
        if($chained && $chain_break){
            $e1=FALSE;
        }
        $enabled[$entity[0]]=$e1;
        if(($entity[0]===$PAGE && !($i < $_SESSION['max_page'])) 
                || ($i>=$_SESSION['max_page'])){
            $chain_break=TRUE;
        }
        $i++;
    }
    unset($i);
    $SETUP = TRUE;
    
    if(isset($arg1)){
        if(isset($arg1['old'])){
            header("location: p1advins.php");
        }
        else if(isset($arg1['q'])){
            $PAGE = urldecode($arg1['q']);
        }
    }
    
    //$enabled = ['wel'=>0,'lic'=>0,'db'=>0,'auth'=>0,'settings'=>0,'review'=>0,'done'=>0];
    
    $stage=[];
    filter_input_array(INPUT_GET,$stage);
?>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Setup for TPS">
    <meta name="author" content="James Oliver">
    <link rel="shortcut icon" href="../favicon.ico">
    <script src="../js/jquery/js/jquery-2.1.1.min.js"></script>

    <title>TPS Setup</title>

    <!-- Bootstrap core CSS -->
    <link href="../js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../js/bootstrap/css/bootstrap-select.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../js/bootstrap/css/dashboard.css" rel="stylesheet">
    <!--<link href="js/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  <style id="holderjs-style" type="text/css"></style></head>

  <body>
    <?php include "../TPSBIN/bs_menu.php"?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <?php 
                $_SESSION['PAGES']=$PAGES;
                foreach ($PAGES as $node){
                    print ("<li ");
                    if($PAGE===$node[0]){
                        print "class='active";
                    }
                    if($enabled[$node[0]]!=1){
                        if ($PAGE===$node[0]){print " disabled";}
                        else{print "class=' disabled";}
                        print "'><a href=\"#\">".$node[1]."</a></li>";
                    }
                    else{
                        print "'><a href=\"".$node[2]."\" >".$node[1]."</a></li>";
                    }
                }
            ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" id="content_head">
          <h1 class="page-header">Setup</h1>
            <?php include_once("setup.core.php");?>
          </div>
        </div>
      </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    
    <script src="../js/bootstrap/js/bootstrap.min.js"></script>
    <script src="../js/bootstrap/js/bootstrap-select.js"></script>
    <script type="text/JavaScript" src="../TPSBIN/JS/sha512.js"></script> 
    <script type="text/JavaScript" src="../TPSBIN/JS/forms.js"></script> 
    <!--<script src="../../assets/js/docs.min.js"></script>-->
  

</body></html>
