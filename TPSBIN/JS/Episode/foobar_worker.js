/*var script = document.createElement('script');
script.src = 'http://code.jquery.com/jquery-1.11.0.min.js';
script.type = 'text/javascript';
document.getElementsByTagName('head')[0].appendChild(script);*/

function get_playing() {
    var xmlhttp;

    if (this.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                //document.getElementById("myDiv").innerHTML = xmlhttp.responseText;
                postMessage(xmlhttp.responseText);
            }
            else if(xmlhttp.status == 404) {
            postMessage("error 404");
            //alert('There was an error 400')
            }
            else {
                postMessage("error");
                //return false;
                //postMessage("An Error Occured");

                //alert('something else other than 200 was returned')
            }
        }
    }

    xmlhttp.open("GET", "../../../Episode/EPV3/workers.php?q=np", true);
    xmlhttp.send();
    setTimeout("get_playing()",1125);
    /*var switch_s = $.ajax({
        url: "EPV3/Switch.php?q=V2",
        cache: false
    });
    switch_s.done(function (msg) {
        //$("#switch_status").html(msg);
        postMessage(msg);
    });
    switch_s.fail(function (jqXHR, textStatus) {
        //$("#switch_status").html("Request failed: " + textStatus);
        postMessage("Request failed: " + textStatus);
    });*/
    //setTimeout("check_switch()",500);
}

get_playing();