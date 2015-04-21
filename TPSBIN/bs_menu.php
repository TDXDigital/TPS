<?php 
    // THIS FILE SHOULD BE INCLUDED WITHIN A PAGET THAT HAS ALREADY RUN sec_session_start!
    if(!isset($_SESSION)){
        // PRINT OUT LOGIN
        echo "Error 401:Unauthorized<br> Please <a href='logout.php'>login</a>";
    }
    if(isset($SETUP)){
        if($SETUP==TRUE){
            goto SETVAR_SETUP;
        }
    }
    $base = $_SESSION['BASE_REF']?:"";
    if(isset($_SESSION['m_logo'])){
        $logo = $_SESSION['m_logo'];
    }
    else if(isset($_SESSION['logo'])){
        $logo = $_SESSION['logo'];
    }
    else{
        $logo = "images/logo.jpg";
    }
    $dbname= $_SESSION['DBNAME']; // NEEDS COMPANY HEAD TO ALLOW SELECTING MULTIPLE CALLSIGNS (This is not right)
    $access=$_SESSION['access'];
    $opened_db=FALSE;
    
    if(!$mysqli){
        $opened_db=TRUE;
        require_once "functions.php";
        require_once "db_connect.php";
    }
    // CONNECT TO DB

    // QUERY "Permissions
    if($stmt = $mysqli->prepare("SELECT * FROM permissions WHERE callsign=? and access=?")){
        
        
        // Bind DBNAME and access // NEEDS TO BIND ON CALLSIGN
        if(isset($_SESSION['CALLSIGN'])){
            $x_call = $_SESSION['CALLSIGN'];//filter_input(INPUT_SESSION,"CALLSIGN");
            $stmt->bind_param("si",$x_call,$access);
        }
        else{
            $stmt->bind_param("si",$dbname,$access);
        }
        //query
        $stmt->execute();
        //

        $perm_arr=$stmt->get_result();
        $permissions=$perm_arr->fetch_array();
        //$stmt->bind_result($permissions[]);// not optimal
        
        //$stmt->fetch();
        $stmt->close();
        //error_log($permissions[0]);
    }
    else{
        if(!$SETUP){
            die('Error 401<br><a href=\'logout.php\'>Authentication Error, please login</a><br><br><sub>GURU: FAILED DB LINK</sub>');
        }
        else{
            SETVAR_SETUP:
            // Genreate Blank Permissions for setup (display menu with no opt.)
            $permissions = ['Station_Settings_View'=>0,'Station_Settings_Edit'=>0,
                            'Member_View'=>0,'Member_Edit'=>0,'Member_Create'=>0,
                            'Program_View'=>0,'Program_Edit'=>0,'Program_Create'=>0,
                            'Genre_View'=>0,'Genre_Edit'=>0,'Genre_Create'=>0,
                            'Playsheet_View'=>0,'Playsheet_Create'=>0,'Playsheet_Edit'=>0,
                            'Advert_View'=>0,'Advert_Edit'=>0,'Advert_Create'=>0,
                            'Audit_View'=>0];
            $base="../";
            $opened_db=FALSE;
            $logo="Setup/opensource_logo.png";
        }
    }

    if($opened_db===TRUE){
        $mysqli->close();
    }
    // Store in reference

    /*
    $menu = array(
        array(0,"<div class=\"navbar navbar-inverse navbar-fixed-top\" role=\"navigation\">"),
        array(0,"<div class=\"container-fluid\">"),
        array(0,"<div class=\"navbar-header\">"),
        array(0,"<button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\".navbar-collapse\">"),
        array(0,"<span class=\"sr-only\">Toggle navigation</span>"),
        array(0,"<span class=\"icon-bar\"></span>"),
        array(0,"<span class=\"icon-bar\"></span>"),
        array(0,"<span class=\"icon-bar\"></span>"),
        array(0,"</button>"),
    )*/
    PRINTMENU:
    ?>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
            <!--Completed Mini Menu-->
          <a class="navbar-brand" href="<?php echo $base."\"><img src=\"$base\\$logo";?>" style="height: 20px;" title="logo"/>TPS Broadcast</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
              <!-- User access to Dashboard is required. can not remove permission-->
            <li><a <?php echo "href=\"".$base."\"" ?> >Dashboard</a></li>
            <?php   
                // determine permission for menu
                $station_permission=max($permissions['Station_Settings_View'],$permissions['Station_Settings_Edit']);
                $members_permission=max($permissions['Member_View'],$permissions['Member_Edit'],$permissions['Member_Create']);
                $program_permission=max($permissions['Program_View'],$permissions['Program_Edit'],$permissions['Program_Create']);
                $genre_permission=max($permissions['Genre_View'],$permissions['Genre_Edit'],$permissions['Genre_Create']);
                //$permissions_permission=max($permissions['Program_View'],$permissions['Program_Edit'],$permissions['Program_Create']); // RODO: Store in DB
                $playsheet_permission=max($permissions['Playsheet_View'],$permissions['Playsheet_Create'],$permissions['Playsheet_Edit']);
                $advertising_permission=max($permissions['Advert_View'],$permissions['Advert_Edit'],$permissions['Advert_Create']);
                //$automation_permission=max($permissions['Audit_View'],$permissions['Audit_Edit'],$permissions['Audit_Create']); // TODO: Store in DB
                $audit_permission=$permissions['Audit_View'];//max($permissions['Audit_View']);
                if(max($station_permission,$members_permission,$program_permission,$genre_permission,$playsheet_permission,$advertising_permission,$audit_permission)==0){
                    echo "<li><div class='alert alert-danger'>"
                    . "<span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\">"
                            . "</span><span> No permissions available  [ $dbname , $access ]</span></div></li>";
                }
                if($station_permission>0){
                    print("<li><a href=\"$base/station/settings.php?old\">Station Settings</a></li>");
                }

                // HOST / PROGRAM
                if(max($members_permission,$program_permission,$playsheet_permission)>0){
                    print("<li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Host / Program<b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">");
                        if($program_permission>0){
                            print("<li class=\"dropdown-header\">Programs</li>
                <li><a href=\"$base/program/p1insert.php\">New Program</a></li>
                <li><a href=\"$base/program/p1advupdate.php\">Edit Program</a></li>
                <li><a href=\"$base/station/genres/Genre.php\">Genres</a></li>
                  <li class=\"divider\"></li>");
                        }
                        if($members_permission>0){
                            print("<li class=\"dropdown-header\">DJ / Hosts</li>
                <li><a href=\"$base/dj/p1newdj.php\">New User</a></li>
                <li><a href=\"$base/dj/p1updatedj.php\">Edit User</a></li>
                <li><a href=\"$base/dj/p1remove.php\">Remove User</a></li>
                <li class=\"divider\"></li>");

                        }
                        if($playsheet_permission>0){
                            print("<li class=\"dropdown-header\">Logging</li>
                <li><a href=\"$base/Episode/EPV2/p1insert.php\">New Playsheet (v0.2)</a></li>
                <li><a href=\"$base/Episode/p1update.php\">Edit Playsheet</a></li>");
                        }
                        print("</ul></li>");
                }

                //  ADVERTISING
                if($advertising_permission>0){
                    print("<li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Advertising<b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">");
                        if($permissions['Advert_Create']>0){
                            print("<li><a href=\"$base/Advertisements/?q=new\">New Commercial</a></li>");
                            print("<li><a style=\"color: #ff6a00\" href=\"$base/Advertisements/?old\">Legacy New Commercial</a></li>");
                        }
                        if($permissions['Advert_Edit']>0){
                            print("<li><a href=\"$base/Advertisements/?q=active\">Edit Commercial</a></li>");
                            print("<li><a style=\"color: #ff6a00\" href=\"$base/Advertisements/p1update.php\">Legacy Edit Commercial</a></li>");

                        }
                        print("</ul></li>");
                }

                //  AUTOMATION (MANAGEMENT)
                if($audit_permission>0){
                    print("<li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Automation<b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">
                <li><a href=\"<?php echo $base/RadioDJ/?q=history\">History</a></li>
                <li><a href=\"$base/RadioDJ/?q=t_songs\">Top Songs</a></li>
                <li><a href=\"$base/RadioDJ/?q=t_albums\">Top Albums</a></li>
                <li><a href=\"$base/RadioDJ/?q=requests\">Requests</a></li>
                <li><a href=\"$base/RadioDJ/?q=tcpc\">Remote Commands</a></li>
              </ul>
            </li>");
                }

                //  MANAGEMENT
                if($audit_permission>0){
                    print("<li class=\"dropdown\">
              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Reports and Management<b class=\"caret\"></b></a>
              <ul class=\"dropdown-menu\">
                <li class=\"dropdown-header\">Programming</li>
                <li><a href=\"$base/Reports/Stats.php?w=12\">12 Week Statistics</a></li>
                <li><a href=\"$base/Episode/EPV3/Audit.php\">Programming Audit</a></li>
                <li><a href=\"$base/station/Audit/\">Reporting Audits</a></li>
                <li class=\"divider\"></li>
                <li class=\"dropdown-header\">Music Department</li>
                <li><a href=\"$base/Reports/MissingLogRep.php\">Missing Logs</a></li>
                <li><a href=\"$base/Reports/PlaylistRep.php\">Charts</a></li>
                <li><a href=\"$base/Reports/p1SongSearch.php\">Records Search</a></li>
                <li><a href=\"$base/Playlist/bulkupdate.php\">Library Update</a></li>
                <li><a href=\"$base/Playlist/p1playlistmgr.php\">Playlist Management</a></li>
                <li class=\"divider\"></li>
                <li class=\"dropdown-header\">Remote Access and Control</li>
                <li><a href=\"$base/Remote/\">Switch Control Suite (SCS)</a></li>
                <li><a href=\"#\">Room Assignments</a></li>
                <li><a href=\"#\">Hardware Management</a></li>
                <li><a href=\"#\">Control Codes</a></li>
                <li><a href=\"#\">Permissions</a></li>
              </ul>
            </li>");
                }

            ?>
              
              
            <!--<li><a href="#">Profile</a></li>-->
            <!--<li><a href="#">Help</a></li>-->
            <li><a href="<?php echo $base; ?>/logout.php">Logout</a></li>
          </ul>
          <!--<form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Search...">
          </form>-->
        </div>
      </div>
    </div>
