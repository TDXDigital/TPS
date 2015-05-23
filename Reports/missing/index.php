<?php
    include_once "../../TPSBIN/functions.php";
    include_once "../../TPSBIN/db_connect.php";
    if(!isset($_SESSION)){
        sec_session_start();
    }
    if(isset($_GET)){
        if(isset($_GET['old'])){
            header("location: ../MissingLogRep2.php");
        }
        else if(isset($_GET['q'])){
            $PAGE = urldecode($_GET['q']);
        }
    }
?>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?php echo $_SESSION['BASE_REF'];?>/favicon.ico">

    <title>Traffic Management</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo $_SESSION['BASE_REF'];?>/js/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo $_SESSION['BASE_REF'];?>/js/bootstrap/css/dashboard.css" rel="stylesheet">
    <!--<link href="js/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">-->

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  <style id="holderjs-style" type="text/css"></style></head>

  <body>
    <?php include "../../TPSBIN/bs_menu.php"?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li <?php if($PAGE=="rep"){echo "class='active' ";}?>><a href="?q=rep">Missing Playsheets / Logs</a></li>
            <!--<li <?php if($PAGE=="active"){echo "class='active' ";}?>><a href="?q=active">Active Traffic</a></li>
            <li <?php if($PAGE=="previous"){echo "class='active' ";}?>><a href="?q=previous">Previous Traffic</a></li>-->
          </ul>
            <!--
          <ul class="nav nav-sidebar">
            <li <?php if($PAGE=="overview"){echo "class='active' ";}?>><a href="?q=overview">Traffic Overview</a></li>
            <li <?php if($PAGE=="trrep"){echo "class='active' ";}?>><a href="?q=trrep">Traffic Reports</a></li>
            <li <?php if($PAGE=="cinvoice"){echo "class='active' ";}?>><a href="?q=cinvoice">Invoicing</a></li>
            <li <?php if($PAGE=="qinvoice"){echo "class='active' ";}?>><a href="?q=qinvoice">QuickBooks Invoicing</a></li>
            <li <?php if($PAGE=="clean"){echo "class='active' ";}?>><a href="?q=clean">Traffic Cleanup</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li <?php if($PAGE=="clin"){echo "class='active' ";}?>><a href="?q=clin">Create Client</a></li>
            <li <?php if($PAGE=="pmt"){echo "class='active' ";}?>><a href="?q=pmt">Record Payment</a></li>
            <li <?php if($PAGE=="wro"){echo "class='active' ";}?>><a href="?q=wro">Writeoffs</a></li>
            <li <?php if($PAGE=="ver"){echo "class='active' ";}?>><a href="?q=ver">Verification</a></li>
          </ul>-->
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <?php include_once("mislog.core.php");?>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="<?php echo $_SESSION['BASE_REF'];?>/js/bootstrap/js/bootstrap.min.js"></script>
    <!--<script src="../../assets/js/docs.min.js"></script>-->
  

</body></html>
