<?php 
    // THIS FILE SHOULD BE INCLUDED WITHIN A PAGET THAT HAS ALREADY RUN sec_session_start!
    $base = $_SESSION['BASE_REF'];
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
            
          <a class="navbar-brand" href="<?php echo $base;?>/"><img src="<?php echo $base;?>/images/logo.png" style="height: 20px;" alt="logo"/>TPS Broadcast</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a <?php echo "href=\"".$base; ?>/">Dashboard</a></li>
            <li><a href="<?php echo $base; ?>/station/p1settings.php">Settings</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Host / Program<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">DJ / Hosts</li>
                <li><a href="<?php echo $base; ?>/dj/p1newdj.php">New User</a></li>
                <li><a href="<?php echo $base; ?>/dj/p1updatedj.php">Edit User</a></li>
                <li><a href="<?php echo $base; ?>/dj/p1remove.php">Remove User</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Programs</li>
                <li><a href="<?php echo $base; ?>/program/p1insert.php">New Program</a></li>
                <li><a href="<?php echo $base; ?>/program/p1advupdate.php">Edit Program</a></li>
                <li><a href="<?php echo $base; ?>/station/genres/Genre.php">Genres</a></li>
                  <li class="divider"></li>
                <li class="dropdown-header">Logging</li>
                <li><a href="<?php echo $base; ?>/Episode/EPV2/p1insert.php">New Log (v0.2)</a></li>
                <li><a href="<?php echo $base; ?>/Episode/p1update.php">Edit Logs</a></li>

              </ul>
            </li>
              <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Advertising<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo $base; ?>/Advertisements/p1advins.php">New Commercial</a></li>
                <li><a href="<?php echo $base; ?>/Advertisements/p1update.php">Edit Commercial</a></li>
              </ul>
            </li>
              <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports and Management<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Programming</li>
                <li><a href="<?php echo $base; ?>/Reports/Stats.php?w=12">12 Week Statistics</a></li>
                <li><a href="<?php echo $base; ?>/Episode/EPV3/Audit.php">Programming Audit</a></li>
                <li><a href="<?php echo $base; ?>/Episode/EPV3/Audit.php">CRTC / Government Auditing</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Music Department</li>
                <li><a href="<?php echo $base; ?>/Reports/MissingLogRep.php">Missing Logs</a></li>
                <li><a href="<?php echo $base; ?>/Reports/PlaylistRep.php">Charts</a></li>
                <li><a href="<?php echo $base; ?>/Reports/p1SongSearch.php">Records Search</a></li>
                <li><a href="<?php echo $base; ?>/Playlist/bulkupdate.php">Library Update</a></li>
                <li><a href="<?php echo $base; ?>/Playlist/p1playlistmgr.php">Playlist Management</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Remote Access and Control</li>
                <li><a href="<?php echo $base; ?>/Remote/">Switch Control Suite (SCS)</a></li>
                <li><a href="#">Room Assignments</a></li>
                <li><a href="#">Hardware Management</a></li>
                <li><a href="#">Control Codes</a></li>
                <li><a href="#">Permissions</a></li>
              </ul>
            </li>
            <!--<li><a href="#">Profile</a></li>-->
            <!--<li><a href="#">Help</a></li>-->
            <li><a href="<?php echo $base; ?>logout.php">Logout</a></li>
          </ul>
          <!--<form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Search...">
          </form>-->
        </div>
      </div>
    </div>