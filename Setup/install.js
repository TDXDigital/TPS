/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var dotsequence=0;
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
              alert( "Server Not Found" );
            },
            403: function() {
                alert("Access Denied");
            }
        },
        succeess: function( msg ){
            //$( "#results" ).append( msg );
            alert(msg);
        }
    });
}

jQuery(document).ready(function(){
    prep_install();
    var dots_run = setInterval(update_dots,750);
});