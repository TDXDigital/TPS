/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var dotsequence=0;
var opencalls = 0;
var dots_run

/*
 * Updates the dots displayed after a progress bar statement (. .. ...)
 */
function update_dots(){
    if(dotsequence===0){
        $('.dots').html('.');
        dotsequence++;
    }
    else if(dotsequence===1){
        $('.dots').html('..');
        dotsequence++;
    }
    else if(dotsequence===2){
        $('.dots').html('...');
        dotsequence++;
    }
    else{
        $('.dots').html('');
        dotsequence=0;
    }
}

function prep_install(){
    $('.nav').addClass('disabled');
    $('a').parent().addClass('disabled');
    
}

function install_db(){
    $.ajax({
        url:"setup.createdb.php",
        dataType:"json",
        statusCode: {
            404: function() {
              console.log( "Server Not Found" );
            },
            403: function() {
                console.log("Access Denied");
            },
            400: function() {
                console.log("Invalid Session, please retry session");
            }
        },
        beforeSend: function(){
            $("#progress_status").html("Installing Database");
        },
        success: function( data ){
            //$( "#results" ).append( msg );
            //alert(msg);
            $('.progress-bar').css('width', 33.3+'%').attr('aria-valuenow', 25.0); 
            $("#conplete").show();
            $("#completed").append(data.status+": Database Created");
            install_xml();
            return true;
        },
        error: function(data){
            $("#progress_status").html("Could not create Database");
            return false;
        }
    });
}

function install_xml(){
    $.ajax({
        url:"setup.createxml.php",
        dataType:"json",
        async: false,
        cache: false,
        statusCode: {
            404: function() {
              console.log( "Server Not Found" );
              $("#progress_status").html("an Error Occured");
            },
            403: function() {
                console.log("Access Denied");
                $("#progress_status").html("an Error Occured");
            }
        },
        beforeSend: function(){
            $("#progress_status").html("Creating Login Config");
        },
        success: function( data ){
            //$( "#results" ).append( msg );
            //alert(msg);
            $('.progress-bar').css('width', 66.6+'%').attr('aria-valuenow', 50.0); 
            $("#conplete").show();
            $("#completed").append("<br>"+data.status+": Login Config Created");
            create_admin();
        },
        error: function(data){
            $("#progress_status").html("could not create login file");
        }
    });
}

function create_admin(){
    $.ajax({
        url:"setup.postinstall.php",
        dataType:"json",
        async: false,
        cache: false,
        statusCode: {
            404: function() {
              console.log( "Server Not Found" );
            },
            403: function() {
                console.log("Access Denied");
            }
        },
        beforeSend: function(){
            $("#progress_status").html("Administrator Created and Permissions set");
        },
        success: function( data ){
            //$( "#results" ).append( msg );
            //alert(msg);
            $('.progress-bar').css('width', 100.0+'%').attr('aria-valuenow', 75.0); 
            $("#conplete").show();
            $("#completed").append("<br>"+data.status+": Login Config Created");
            perform_updates();
        },
        error: function(data){
            $("#progress_status").html("Could not create permissions and administrator");
        }
    });
}

function perform_updates(){
    $.ajax({
        url:"setup.postinstall.update.php",
        dataType:"json",
        async: false,
        cache: false,
        statusCode: {
            404: function() {
              console.log( "Server Not Found" );
            },
            403: function() {
                console.log("Access Denied");
            }
        },
        beforeSend: function(){
            $("#progress_status").html("Performing Updates to database");
        },
        success: function( data ){
            //$( "#results" ).append( msg );
            //alert(msg);
            $('.progress-bar').css('width', 100.0+'%').attr('aria-valuenow', 100.00); 
            $("#complete").show();
            $("#completed").append("<br>"+data.status+": Updates Completed");
            complete();
        },
        error: function(data){
            $("#progress_status").html("Could not perform updates");
        }
    });
}

function complete(){
    clearInterval(dots_run);
    $('.progress-bar').removeClass("active progress-bar-striped");
    $('.dots').html('...');
    $("#progress_status").html("Complete");
    $('.progress-bar').css('width', 100+'%').attr('aria-valuenow', 100); 
    $("#next").removeAttr('disabled');
}

jQuery(document).ready(function(){
    prep_install();
    dots_run=setInterval(update_dots,750);
    install_db()
    //install_xml();  
    //complete();
    /*if(install_db()){
      install_xml();  
      complete();
    }
    else{
        clearInterval(dots_run);
        complete();
        $('#complete').hide();
        $('.progress-bar').removeClass("active progress-bar-striped");
        $('.progress-bar').addClass("progress-bar-danger");
        $("#progress_status").html("Setup Failed");
    }*/
    //install_xml();
    
    
});
