/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var dotsequence=0;
var opencalls = 0;
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
            $('.progress-bar').css('width', 33.3+'%').attr('aria-valuenow', 33.3); 
            $("#conplete").show();
            $("#completed").append(data.status+": Database Created");
            return true;
        },
        error: function(data){
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
            $('.progress-bar').css('width', 66.6+'%').attr('aria-valuenow', 66.6); 
            $("#conplete").show();
            $("#completed").append("<br>"+data.status+": Login Config Created");
        },
        error: function(data){
            $("#progress_status").html("an Error Occured");
        }
    });
}

function create_admin(){
    $.ajax({
        url:"setup.createadmin.php",
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
            $("#progress_status").html("Creating Login Config");
        },
        success: function( data ){
            //$( "#results" ).append( msg );
            //alert(msg);
            $('.progress-bar').css('width', 66.6+'%').attr('aria-valuenow', 66.6); 
            $("#conplete").show();
            $("#completed").append("<br>"+data.status+": Login Config Created");
        }
    });
}

function complete(){
    $('.progress-bar').removeClass("active progress-bar-striped");
    $('.dots').html('...');
    $("#progress_status").html("Complete");
    $('.progress-bar').css('width', 100+'%').attr('aria-valuenow', 100); 
    $("#next").removeAttr('disabled');
}

jQuery(document).ready(function(){
    prep_install();
    var dots_run=setInterval(update_dots,750);
    if(install_db()){
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
    }
    //install_xml();
    
    
});
