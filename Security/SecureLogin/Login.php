<?php
//include_once '../../TPSBIN/db_connect.php';
include_once '../../TPSBIN/functions.php';
 
if(!isset($_SESSION)){
    sec_session_start();
    $DEBUG = FALSE;
    $logged = 'out';
}
else{
    $logged = 'in';
}


/* 
if (login_check($mysqli) == true) {
    $logged = 'in';
} else {
    $logged = 'out';
}*/

// HTML Bootstrap Based on http://bootsnipp.com/snippets/featured/login-amp-signup-forms-in-panel
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Secure Login: Log In</title>
        <link rel="stylesheet" href="../../phpstyle.css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"/>
        <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"/>
        <script type="text/JavaScript" src="../../TPSBIN/JS/sha512.js"></script> 
        <script type="text/JavaScript" src="../../TPSBIN/JS/forms.js"></script> 
    </head>
    <body>
        <form action="process_login.php" method="post" name="login_form">  
        <div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Sign In</div>
                        <!--<div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>-->
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                            <?php
                            if (isset($_GET['error'])) {
                                echo '<p class="error">Error Logging In!</p>';
                            }
                            ?>
                        </div>
                            
                        <form id="loginform" class="form-horizontal" role="form">
                                    
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon">@</span>
                                        <input id="login-username" type="text" class="form-control" required name="email" value="" placeholder="email">                                        
                                    </div>
                                
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                        <input id="password" type="password" class="form-control" required name="password" placeholder="password">
                                    </div>
                                    

                                
                            <div class="input-group">
                                      <div class="checkbox">
                                        <label>
                                          <input id="login-remember" type="checkbox" name="remember" value="1"> Remember me
                                        </label>
                                      </div>
                                    </div>


                                <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="col-sm-12 controls">
                                        <button id="btn-login" onclick="formhash(this.form, this.form.password);" class="btn btn-success" value="Login">Login</button>
                                      <!--<a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>-->

                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12 control">
                                        <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                            If you don't have a login, please <a href="../register.php">register</a>.<br>
                                            If you are done, please <a href="../../TPSBIN/sec_logout.php">log out</a>.<br>
                                            You are currently logged <?php echo $logged ?>.
                                        <!--<a href="#" onClick="$('#loginbox').hide(); $('#signupbox').show()">
                                            Sign Up Here
                                        </a>-->
                                        </div>
                                    </div>
                                </div>    
                            </form>     



                        </div>                     
                    </div>  
        </div>
        </form>
    </div>
    </body>
    <script type="text/javascript">
        $.ajax({
            dataType: "json",
            url: "../listservers.php",
            data: data,
            success: function(data){
                var server_ids = [];
                $.each( data ,function(index,value){
                    server_ids.push("<option value='"+value.server+"'>"+value.name+"</option>");
                });
            }
          });
        </script>
</html>