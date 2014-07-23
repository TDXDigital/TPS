var device_time_ff = [];
var device_time_ss = [];
var device_time_mm = [];
var device_time_hhh = [];
var timer_s1 = [];

function sleep (milliseconds)
{
    var start = new Date().getTime();

    var timer = true;
    while (timer) {
        if ((new Date().getTime() - start)> milliseconds) {
            timer = false;
        }
    }
}
/*
$.ready(function () {
    setInterval(function(){
        Update_Device_Status(target, hid);
    },15000)
});*/

function Query_Device(target,code,hid){
    var result_status = "";
    var storage = $('#'.target).html();
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=" + code,
        dataType: "text",
        beforeSend: function () {
            $('#' + target).html("<progress>processing...</progress>");
            $('.HID-'+hid).attr("disabled",true);
            $('.HID-'+hid).hide();
        },
        success: function (data) {
            if (!$.trim(data)) {
                $('#' + target).html("No Response, Piracom is may not be running or device is off");
            }
            else{
                result_status = data.responseText;
                $('.HID-'+hid).attr("disabled",false);
                $('.HID-'+hid).show();
            }
        },
        error: function (){
            // do nothing
            $('.HID-'+hid).attr("disabled",false);
            $('.HID-'+hid).show();
        },
        timeout:500
    });
    if ($(result_status).find('@0TR')==true){
        $('#' + target).html(" Track Changed"+result_status.substring(5,3));
        $('.HID-'+hid).attr("disabled",false);
        $('.HID-'+hid).show();
    }
    else if($(result_status).find('@0TR')==true){
        $('#' + target).html(" Track Changed"+result_status.substring(5,3));
    }
    else{
        if (typeof result_status==='undefined' || !result_status.trim()) {
            //$('#' + target).html("&nbsp- DENON - ");
            /*$(document).delay(1000).queue(function () {
                Update_Device_Status(target, hid);
            });*/
            //sleep(1500);
            //Update_Device_Status(target, hid);
            var newinterval = setInterval(function () {
                Update_Device_Status(target, hid);
                clearInterval(newinterval);
            }, 1750)
            //$('#' + target).html("No Response, Piracom is may not be running or device is off");
        }
        else{
            $('#' + target).html(result_status);
        }
        //setTimeout( $('#' + target).html("DENON"), 1000);
    }
}

function Update_Progress_Device(hid){
    var current=$("#HID-PR-"+hid).attr("value");
    current+=1;
    progress.attr("value",current);
    if(current>=100){
        current=0;
    }
}

function Check_Device(target,hid){
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=19",
        dataType: "text",
        beforeSend: function () {
            $('#' + target).html("<progress></progress><span>Checking for Device Status</span>");
            $('.HID-' + hid).attr("disabled", true);
            $('.HID-' + hid).hide();
            progress = setInterval(Update_Progress_Device(hid), "100");
        },
        success: function (data) {
            clearInterval(progress);
        },
        timeout:500
    })
}

function Update_Device_Status(target,hid){
    var results = "&nbsp;- DENON - ";
    var track = "";
    var temp = "";
    var status = "error";
    // Check Power
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=19",
        timeout: 500,
        dataType: "html",
        cache: false,
        success: function (data) {
            if (!$.trim(data)) {
                $('#' + target).html("<span class='ui-state-error'>Device Power Failure or Communications Error</span>");
            }
            else {
                //alert(data);
                //if (data==='@0PW01\n') {
                if (!data.indexOf('@0PW01')) {
                    $('#' + target).html("<span class='ui-state-error'>Device is in Standby</span>");
                }
                else {
                    // check status, verified device is on
                    $.ajax({
                        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=21",
                        timeout: 500,
                        dataType: "html",
                        cache: false,
                        success: function (data_status) {
                            if (!$.trim(data_status)) {
                                //alert(data_status);
                                $('#' + target).html("<span class='ui-state-error'>Communications Error, failed to retrieve device status</span>");
                                clearInterval(timer_s1[hid]);
                            }
                            else if (!data_status.indexOf('@0STST') || !data_status.indexOf('@0STCU') || !data_status.indexOf('@0STSL') || !data_status.indexOf('@0STPP')) {
                                // ready for playback
                                temp = data_status
                                clearInterval(timer_s1[hid]);
                                //alert(temp);
                                $('#' + target).html("<span class='ui-state-highlight'>Stopped, Cued, or Paused</span>");
                            }
                            else if (!data_status.indexOf('@0STPL')) {
                                // playback or in transit
                                // prep for data
                                var title = "";
                                var artist = "";
                                var album = "";

                                // run query
                                temp = data_status
                                status = "Playing: ";
                                $('#' + target).effect("highlight", {}, 3000);
                                $('#' + target).html("<span class='ui-state-highlight'>Device is in playback</span>");
                                $.ajax({
                                    url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=18",
                                    timeout: 500,
                                    dataType: "html",
                                    cache: false,
                                    success: function (time) {

                                        var time_hhh = parseInt(time.substring(4, 7));
                                        var time_mm = parseInt(time.substring(7, 9));
                                        var time_ss = parseInt(time.substring(9, 11));
                                        var time_ff = parseInt(time.substring(11));

                                        device_time_hhh = time_hhh;
                                        device_time_mm = time_mm;
                                        device_time_ss = time_ss;
                                        device_time_ff = time_ff;

                                        var str_hhh = ((time_hhh < 10 ? "0" : "") + time_hhh);
                                        var str_mm = ((time_mm < 10 ? "0" : "") + time_mm);
                                        var str_ss = ((time_ss < 10 ? "0" : "") + time_ss);
                                        var str_ff = ((time_ff < 10 ? "0" : "") + time_ff);


                                        /*var ss_c = time_ss * 100;
                                        var mm_c = time_mm * 60 * 100;
                                        var hhh_c = time_hhh * 24 * 60 * 100;*/

                                        //var int_time = ss_c + mm_c + hhh_c + ff_c;//parseInt(substr_time);
                                        $('#' + target + '-timer').html(str_hhh + ":" + str_mm + ":" + str_ss + ":" + str_ff)//int_time);
                                        //device_clock[hid] = int_time;
                                        timer_s1[hid] = setInterval(function () {

                                            /*if (device_clock[hid] < 0) {
                                                //alert("time undefined")
                                                $('#' + target + '-timer').html("00:00:00");
                                                clearInterval(timer_s1[hid]);
                                                //alert("cleared timer");
                                            }
                                            else {*/
                                                //alert("time--");
                                                /*device_time_hhh;
                                                device_time_mm;
                                                device_time_ss;*/
                                                device_time_ff -= 11;
                                                if (device_time_ff < 0) {
                                                    device_time_ss--;
                                                    device_time_ff += 100;
                                                }
                                                if (device_time_ss < 0) {
                                                    device_time_mm--;
                                                    device_time_ss += 60;
                                                }
                                                if (device_time_mm < 0) {
                                                    device_time_hhh--;
                                                    device_time_mm += 60;
                                                }
                                                if(device_time_hhh<0){
                                                    //alert("time undefined")
                                                    $('#' + target + '-timer').html("00:00:00");
                                                    clearInterval(timer_s1[hid]);
                                                }
                                                else{
                                                    var str_hhh = ((device_time_hhh < 10 ? "0" : "") + device_time_hhh);
                                                    var str_mm = ((device_time_mm < 10 ? "0" : "") + device_time_mm);
                                                    var str_ss = ((device_time_ss < 10 ? "0" : "") + device_time_ss);
                                                    var str_ff = ((device_time_ff < 10 ? "0" : "") + device_time_ff);
                                                    $('#' + target + '-timer').html(str_hhh + ":" + str_mm + ":" + str_ss + ":" + str_ff)//int_time);
                                                }
                                                /*time_interval = device_clock[hid];
                                                time_interval = time_interval - 11;
                                                device_clock[hid] = time_interval;*/
                                                //$('#' + target + '-timer').html(time_interval);
                                            //}
                                        }, 110);
                                        //alert("timer set...");
                                    }
                                });
                                $.ajax({
                                    url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=14",
                                    timeout: 500,
                                    dataType: "html",
                                    cache: false,
                                    success: function (title) {

                                    }
                                });
                            }
                            else if (!data_status.indexOf('@0STEM')) {
                                // playback or in transit
                                temp = data_status
                                status = "End Of Track: ";
                                $('#' + target+"-timer").effect("pulsate", { times: 10 }, 1000);
                                $('#' + target).html("<span class='ui-state-highlight'>Device is in near end of track</span>");
                                /*break;
                                case "@0STEM":
                                // DRAW ATTENTION
                                $('#' + target).effect("highlight", {}, 3000);
                                status = "EOT:";
                                break;
                                case "@0STFF":
                                status = "Playing: ";
                                break;
                                default:
                                status = "Unknown: ";
                                $('#' + target).html("<span class='ui-state-highlight'>Device is in playback or transit... (E1-"+temp+")</span>");
                                break;
                                }*/
                                //$('#' + target).html("<span class='ui-state-highlight'>Device is in playback or transit... need to query CD (" + temp + ")</span>");
                            }
                            else if (!data_status.indexOf('@0STFF')) {
                                // playback or in transit
                                temp = data_status
                                status = "Fast Forward: ";
                                $('#' + target).html("<span class='ui-state-highlight'>Device is Fast Forwarding (seek)</span>");
                            }
                            else if(!data_status.indexOf('@0STRW')){
                                // playback or in transit
                                temp = data_status
                                status = "Rewind: ";
                                $('#' + target).html("<span class='ui-state-highlight'>Device is Rewinding (seek)</span>");
                            }
                            else if (!data_status.indexOf('@0STED')) {
                                // operating as preset
                                temp = data_status
                                //alert(temp);
                                $('#' + target).html("<span class='ui-state-highlight'>Device is in preset mode</span>");
                            }
                            else if (!data_status.indexOf('@0STER')) {
                                // Operating Error
                                temp = data_status
                                //alert(temp);
                                $('#' + target).html("<div class='ui-state-error'>Device reports operation error, please follow these steps to resolve<br>1. Power Cycle Player (off/on)<br>2. Wait for next check or press refresh<br/>3. If problem continues, be cautious of device functionality.</div>");//&nbsp  [this error has been recorded] (not yet) TODO: post equipment errors to server
                            }
                            else {// got result (unknown)
                                //alert(data_status);
                                device_time_hhh = 0;
                                device_time_mm = 0;
                                device_time_ss = 0;
                                device_time_ff = 0;
                                clearInterval(timer_s1[hid]);
                                $('#' + target + '-timer').html("00:00:00");
                                $('#' + target).html("<span class='ui-state-highlight'>Device is on, unknown result</span>");
                            }

                        }
                    })
                }
            }
        },
        error: function (data) {
            $('#' + target).html("An Error Occured, Please Verify COM is available (PiraCZ Piracom)");
        }
    });

    // Check Status

    /*
    // Check Track
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=17",
        timeout:500,
        beforeSend: function () {
            $('#' + target).html("<progress>processing...</progress>");
            $('.' + hid).attr("disabled", true);
            $('.HID-' + hid).hide();
            //$('.HID-' + hid).css("color", 'yellow');
        },
        dataType: "text",
        success: function (data) {
            if (data == "TIMEOUT" || data == "") {
                $('#' + target).show();
                $('#' + target).html("No Response, Piracom is may not be running or device is off");
                $('.HID-' + hid).attr("disabled", true);
                //setTimeout( $('#' + target).html(results), 1000);
            }
            else if (data.indexOf('@0TR')>-1) {
                //$('#' + target).html("track cued: " + $(data).text());
                //$('#' + target).text(data);
                temp = data.substring(4);
                $('.HID-' + hid).attr("disabled", false);
                $('.HID-' + hid).show();
                $('#' + target).html("Track #"+temp);
            }
            else if (data.indexOf('@0PW')>-1) {
                if (data.indexOf("@0PW00")>-1) {
                    $('#' + target).html("Device is in Standby");
                }
                else if (data.indexOf("@0PW01")>-1) {
                    $('#' + target).html("Device is On");
                }
                else {
                    $('#' + target).html("The device is off or piracom is not operational (Rejected) with:"+data);
                }
            }
            else {
                $('#' + target).slideDown();
                $('#' + target).html(data);
                $('.HID-' + hid).attr("disabled", false);
                $('.HID-' + hid).show();
                //setTimeout( $('#' + target).html("DENON"), 10000);
            }
            temp = data;
        },
        error: function () {
            $('#' + target).html("An Error Occured, Please verify Piracom is running and device master power is on");
        }
    });*/
    /*if ($(results).find('@0TR')){
        results = temp;
    }
    else{
        results = "TRACK ERROR";
    }*/
    //$('#' + target).html(results);
}

function Get_Info(track_target,artist_target,album_target,hid){
    var results = "&nbsp;- DENON - ";
    var track = "";
    var album = "";
    var artist = "";
    var NoData = "FALSE";
    var temp = "";
    // HIDE & DISABLE INTERFACE
    //$('#' + target).html("<progress>processing...</progress>");
    $('.' + hid).attr("disabled", true);
    $('.HID-' + hid).hide();

    // GET TITLE (track)
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=14",
        dataType: "text",
        success: function (data) {
            if (data == "TIMEOUT" || data == "") {
                track = $("#" + track_target).value();
                NoData = "TRUE";
            }
            else if (data.indexOf('@0T1') > -1) {
                alert("title:"+data);
                //track = data.substr(4);
                //$('#'+track_target).val(data.substr(4));
                $('input[name="'+track_target+'"]').val(data.substr(4));
            }
            else {
                track = data;
                NoData = "TRUE";
            }
        },
        error: function () {
        }
    });
    // GET ARTIST
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=15",
        dataType: "text",
        success: function (data) {
            if (data == "TIMEOUT" || data == "") {
                artist = $("#"+artist_target).value();
                NoData = "TRUE";
            }
            else if (data.indexOf('@0T2') > -1) {
                alert("artist:"+data);
                //artist = data.substr(4);
                $('input[name="'+artist_target+'"]').val(data.substr(4));
            }
            else {
                artist = data;
                NoData = "TRUE";
            }
        },
        error: function () {
        }
    });
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=16",
        dataType: "text",
        success: function (data) {
            if (data == "TIMEOUT" || data == "") {
                album = $("#"+album_target).value();
                NoData = "TRUE";
            }
            else if (data.indexOf('@0T3') > -1) {
                alert("album:"+data.substr(4));
                //album = data.substr(4);
                $('input[name="'+album_target+'"]').val(data.substr(4));
            }
            else {
                album = data;
                NoData = "TRUE";
            }
        },
        error: function () {
            $("#Alert").html("Device Encountered Error");
            $("#Alert").slideDown("slow", function () {
                $("#Alert").slideUp(600);
            }).delay(3000)
        }
    });

    //$("#".artist_target).value = artist;
    //$('#'+artist_target).value(artist);
    //$('input[name="'+album_target+'"]').val(album);
//    $('input[name="'+track_target+'"]').val(track);
    if(NoData=="TRUE"){
        /*$("#Alert").html("No Data was returned for one or more Information Items");
        $("#Alert").slideDown("slow", function () {
            $("#Alert").slideUp(600);
        }).delay(3000);*/
        alert("error: no response");
    }
    //$("#".album_target).value = album;
    //$("#".title_target).value = track;

    // ENABLE INTERFACE
    $('.HID-' + hid).attr("disabled", false);
    $('.HID-' + hid).show();
}