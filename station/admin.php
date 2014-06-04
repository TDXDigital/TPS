<?php
    include_once "TPSBIN/functions.php";
    include_once "TPSBIN/db_connect.php";
    if(!isset($_SESSION)){
        sec_session_start();
    }
?>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.ico">

    <title>Dashboard Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="js/bootstrap/css/dashboard.css" rel="stylesheet">
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
    <?php include "TPSBIN/bs_menu.php"?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="#">Overview</a></li>
            <!--<li><a href="#">Reports</a></li>-->
            <!--<li><a href="#">Analytics</a></li>-->
            <li><a href="#">Export</a></li>
          </ul>
          <!--<ul class="nav nav-sidebar">
            <li><a href="">Nav item</a></li>
            <li><a href="">Nav item again</a></li>
            <li><a href="">One more nav</a></li>
            <li><a href="">Another nav item</a></li>
            <li><a href="">More navigation</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li><a href="">Nav item again</a></li>
            <li><a href="">One more nav</a></li>
            <li><a href="">Another nav item</a></li>
          </ul>-->
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Administration Dashboard</h1>
            <!--<h3 class="sub-header">New Admin Interface</h3>-->
              <p>Welcome to the new administration interface! please take a moment to become comfortable with the updated theme, please note that most options have been relocated to the top right menus.</p>
              <p>Statistics and Reports will be presented in this section in future releases and has been loaded with excample information as placeholders</p>
              <div class="alert alert-danger">If you need to force the system to use the old style dashboard enter "?old" after /TPS in the address bar ie."/TPS/?old" </div>
          <div class="row placeholders">
              <h2 class="sub-header">2 Day Live Schedule</h2>
              <p class="text-warning">Completed programs with valid start and end dates during the past 2 days are displayed below</p>
              <script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['timeline']}]}"></script>
                <script type="text/javascript">

                google.setOnLoadCallback(drawChart);
                function drawChart() {

                  var container = document.getElementById('LiveSchedule');
                  var chart = new google.visualization.Timeline(container);
                  var dataTable = new google.visualization.DataTable();
                  dataTable.addColumn({ type: 'string', id: 'Room' });
                  dataTable.addColumn({ type: 'string', id: 'Name' });
                  dataTable.addColumn({ type: 'date', id: 'Start' });
                  dataTable.addColumn({ type: 'date', id: 'End' });
                  dataTable.addRows([
                  <?php
                      date_default_timezone_set('GMT-0700');
                      /*if($mysqli->connect_error()){
                          break;
                      }
                      else{*/
                          $first = TRUE;
                          $sql_episode="SELECT concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(starttime, '%H:%i:%s')) AS start, concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(endtime, '%H:%i:%s')) AS end, programname, date, Type FROM episode WHERE DATE(date)>DATE_ADD(CURDATE(), INTERVAL -2 DAY) and DATE(date)<=CURDATE() and endtime > starttime and Type = 0 order by start";
                          $results = $mysqli->query($sql_episode) or trigger_error($mysqli->error."[$sql_episode]");
                          while($row = $results->fetch_array()){
                              if($first){
                                  $first=FALSE;
                              }
                              else{
                                  echo ",
    ";
                              }
                              //date = y,d,m,h,m,s
                              echo "['Logged','".addslashes(htmlspecialchars($row['programname']))."', new Date("
                              .date("Y,m,d,H,i,s",strtotime($row['start']))."), new Date(".date("Y,m,d,H,i,s",strtotime($row['end'])).")]";
                              //echo "/*".$row['start'].",".$row['end']."*/";
                              //.date('Y',$row['start']).",".date('m',$row['start']).",".date('d',$row['start']).",".date('H',$row['start']).",".date('i',$row['start']).",".date('s',$row['start'])."), new Date("
                              //.date('Y',$row['end']).",".date('m',$row['end']).",".date('d',$row['end']).",".date('H',$row['end']).",".date('i',$row['end']).",".date('s',$row['end']).")]";

                          }
                          //$mysqli->query();
                      //}
                      echo "]);
                      ";
                  ?>
                   /* ,[ 'Scheduled',   'Beginning Google Charts',    new Date(0,0,0,12,30,0), new Date(0,0,0,14,0,0) ],
                    [ 'Scheduled',   'Intermediate Google Charts', new Date(0,0,0,14,30,0), new Date(0,0,0,16,0,0) ],
                    [ 'Scheduled',   'Advanced Google Charts',     new Date(0,0,0,16,30,0), new Date(0,0,0,18,0,0) ],
                    [ '24 Hour', 'Live',       new Date(0,0,0,0,0,0),  new Date(0,0,0,0,0,0) ],
                    [ 'Booth1', 'Live',       new Date(0,0,0,0,0,0),  new Date(0,0,0,24,0,0) ],
                    [ 'Booth2', 'Live',       new Date(0,0,0,0,0,0),  new Date(0,0,0,0,0,0) ]]);*/

                  var options = {
                    timeline: { colorByRowLabel: true }
                  };

                  <?php
                      if(!$first)
                      {
                          echo "chart.draw(dataTable, options);";
                      }
                      else{
                          echo '$(\'#LiveSchedule\').html(\'No Programs Found\');';
                      }
                    ?>
                }

                </script>
                <script type="text/javascript">
                
                    
                google.setOnLoadCallback(drawChart2);
                function drawChart2() {

                  var container = document.getElementById('PreRecord');
                  var chart = new google.visualization.Timeline(container);
                  var dataTable = new google.visualization.DataTable();
                  dataTable.addColumn({ type: 'string', id: 'Room' });
                  dataTable.addColumn({ type: 'string', id: 'Name' });
                  dataTable.addColumn({ type: 'date', id: 'Start' });
                  dataTable.addColumn({ type: 'date', id: 'End' });
                  dataTable.addRows([
                    <?php
                    $first = TRUE;
                    
                    $sql_prerecord="SELECT concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(starttime, '%H:%i:%s')) AS start, concat(DATE_FORMAT(date, '%Y:%m:%d'),\" \",DATE_FORMAT(endtime, '%H:%i:%s')) AS end, programname, date, Type FROM episode WHERE DATE(date)<DATE_ADD(CURDATE(), INTERVAL +30 DAY) and DATE(date)>=CURDATE() and (Type = 1 or prerecorddate is not null) and endtime IS NOT NULL order by start";
                    // should be adjusted to take null into account (IFNULL)
                    $results = $mysqli->query($sql_prerecord) or trigger_error($mysqli->error."[$sql_episode]");
                    while($row = $results->fetch_array()){
                              if($first){
                                  $first=FALSE;
                              }
                              else{
                                  echo ",
    ";
                              }
                              //date = y,d,m,h,m,s
                              echo "['Logged','".addslashes(htmlspecialchars($row['programname']))." (".$row['date'].")', new Date("
                              .date("Y,m,d,H,i,s",strtotime($row['start']))."), new Date(".date("Y,m,d,H,i,s",strtotime($row['end'])).")]";
                              //echo "/*".$row['start'].",".$row['end']."*/";
                              //.date('Y',$row['start']).",".date('m',$row['start']).",".date('d',$row['start']).",".date('H',$row['start']).",".date('i',$row['start']).",".date('s',$row['start'])."), new Date("
                              //.date('Y',$row['end']).",".date('m',$row['end']).",".date('d',$row['end']).",".date('H',$row['end']).",".date('i',$row['end']).",".date('s',$row['end']).")]";

                          }
                          //$mysqli->query();
                      //}
                      echo "]);
                      ";
                  ?>
                   /* ,[ 'Scheduled',   'Beginning Google Charts',    new Date(0,0,0,12,30,0), new Date(0,0,0,14,0,0) ],
                    [ 'Scheduled',   'Intermediate Google Charts', new Date(0,0,0,14,30,0), new Date(0,0,0,16,0,0) ],
                    [ 'Scheduled',   'Advanced Google Charts',     new Date(0,0,0,16,30,0), new Date(0,0,0,18,0,0) ],
                    [ '24 Hour', 'Live',       new Date(0,0,0,0,0,0),  new Date(0,0,0,0,0,0) ],
                    [ 'Booth1', 'Live',       new Date(0,0,0,0,0,0),  new Date(0,0,0,24,0,0) ],
                    [ 'Booth2', 'Live',       new Date(0,0,0,0,0,0),  new Date(0,0,0,0,0,0) ]]);*/

                  var options = {
                    timeline: { colorByRowLabel: true }
                  };

                  <?php
                      if(!$first)
                      {
                          echo "chart.draw(dataTable, options);";
                      }
                      else{
                          echo '$(\'#PreRecord\').html(\'No Pre Records Found\');';
                      }
                    ?>
                }

                </script>
                <div id="LiveSchedule" style="width: 100%; min-height: 150px;"></div>
                <h2 class="sub-header">Upcoming PreRecords (30 Days)</h2>
                <div id="PreRecord" style="width: 100%; height: 100px;"></div>
            <!--<div class="col-xs-6 col-sm-3 placeholder">
              <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="200x200" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzBEOEZEQiI+PC9yZWN0Pjx0ZXh0IHRleHQtYW5jaG9yPSJtaWRkbGUiIHg9IjEwMCIgeT0iMTAwIiBzdHlsZT0iZmlsbDojZmZmO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEzcHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+MjAweDIwMDwvdGV4dD48L3N2Zz4=">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="200x200" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzM5REJBQyI+PC9yZWN0Pjx0ZXh0IHRleHQtYW5jaG9yPSJtaWRkbGUiIHg9IjEwMCIgeT0iMTAwIiBzdHlsZT0iZmlsbDojMUUyOTJDO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEzcHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+MjAweDIwMDwvdGV4dD48L3N2Zz4=">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="200x200" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzBEOEZEQiI+PC9yZWN0Pjx0ZXh0IHRleHQtYW5jaG9yPSJtaWRkbGUiIHg9IjEwMCIgeT0iMTAwIiBzdHlsZT0iZmlsbDojZmZmO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEzcHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+MjAweDIwMDwvdGV4dD48L3N2Zz4=">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="200x200" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iIzM5REJBQyI+PC9yZWN0Pjx0ZXh0IHRleHQtYW5jaG9yPSJtaWRkbGUiIHg9IjEwMCIgeT0iMTAwIiBzdHlsZT0iZmlsbDojMUUyOTJDO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjEzcHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+MjAweDIwMDwvdGV4dD48L3N2Zz4=">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>-->
          </div>

          <h2 class="sub-header">Section title</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Program</th>
                  <th>Playlist</th>
                  <th>CanCon</th>
                  <th>Score</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1,001</td>
                  <td>Lorem</td>
                  <td>ipsum</td>
                  <td>dolor</td>
                  <td>sit</td>
                </tr>
                <tr>
                  <td>1,002</td>
                  <td>amet</td>
                  <td>consectetur</td>
                  <td>adipiscing</td>
                  <td>elit</td>
                </tr>
                <tr>
                  <td>1,003</td>
                  <td>Integer</td>
                  <td>nec</td>
                  <td>odio</td>
                  <td>Praesent</td>
                </tr>
                <tr>
                  <td>1,003</td>
                  <td>libero</td>
                  <td>Sed</td>
                  <td>cursus</td>
                  <td>ante</td>
                </tr>
                <tr>
                  <td>1,004</td>
                  <td>dapibus</td>
                  <td>diam</td>
                  <td>Sed</td>
                  <td>nisi</td>
                </tr>
                <tr>
                  <td>1,005</td>
                  <td>Nulla</td>
                  <td>quis</td>
                  <td>sem</td>
                  <td>at</td>
                </tr>
                <tr>
                  <td>1,006</td>
                  <td>nibh</td>
                  <td>elementum</td>
                  <td>imperdiet</td>
                  <td>Duis</td>
                </tr>
                <tr>
                  <td>1,007</td>
                  <td>sagittis</td>
                  <td>ipsum</td>
                  <td>Praesent</td>
                  <td>mauris</td>
                </tr>
                <tr>
                  <td>1,008</td>
                  <td>Fusce</td>
                  <td>nec</td>
                  <td>tellus</td>
                  <td>sed</td>
                </tr>
                <tr>
                  <td>1,009</td>
                  <td>augue</td>
                  <td>semper</td>
                  <td>porta</td>
                  <td>Mauris</td>
                </tr>
                <tr>
                  <td>1,010</td>
                  <td>massa</td>
                  <td>Vestibulum</td>
                  <td>lacinia</td>
                  <td>arcu</td>
                </tr>
                <tr>
                  <td>1,011</td>
                  <td>eget</td>
                  <td>nulla</td>
                  <td>Class</td>
                  <td>aptent</td>
                </tr>
                <tr>
                  <td>1,012</td>
                  <td>taciti</td>
                  <td>sociosqu</td>
                  <td>ad</td>
                  <td>litora</td>
                </tr>
                <tr>
                  <td>1,013</td>
                  <td>torquent</td>
                  <td>per</td>
                  <td>conubia</td>
                  <td>nostra</td>
                </tr>
                <tr>
                  <td>1,014</td>
                  <td>per</td>
                  <td>inceptos</td>
                  <td>himenaeos</td>
                  <td>Curabitur</td>
                </tr>
                <tr>
                  <td>1,015</td>
                  <td>sodales</td>
                  <td>ligula</td>
                  <td>in</td>
                  <td>libero</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap/js/bootstrap.min.js"></script>
    <!--<script src="../../assets/js/docs.min.js"></script>-->
  

</body></html>
