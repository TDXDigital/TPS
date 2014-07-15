function Query_Device(target,code,hid){
    var result_status = "";
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=" + code,
        dataType: "text",
        beforeSend: function () {
            $('#' + target).html("<progress>processing...</progress>");
            $('.HID-'+hid).attr("disabled",true);
            $('.HID-'+hid).hide();
        },
        success: function (data) {
            if (data == "") {
                $('#' + target).html("No Response, Piracom is may not be running or device is off");
            }
            else{
                result_status = data.responseText;
                $('.HID-'+hid).attr("disabled",false);
                $('.HID-'+hid).show();
            }
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
        $('#' + target).html(data);
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
                    $('#' + target).html("Device is in standby mode");
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
    });
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
        }
    });

    //$("#".artist_target).value = artist;
    //$('#'+artist_target).value(artist);
    //$('input[name="'+album_target+'"]').val(album);
//    $('input[name="'+track_target+'"]').val(track);
    if(NoData=="TRUE"){
        $("#Alert").html("No Data was returned for one or more Information Items");
        $("#Alert").slideDown("slow", function () {
            $("#Alert").slideUp(600);
        }).delay(3000);
    }
    //$("#".album_target).value = album;
    //$("#".title_target).value = track;

    // ENABLE INTERFACE
    $('.HID-' + hid).attr("disabled", false);
    $('.HID-' + hid).show();
}