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
                result_status = data;
                $('.HID-'+hid).attr("disabled",false);
                $('.HID-'+hid).show();
            }
        }
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
        beforeSend: function () {
            $('#' + target).html("<progress></progress><span>Checking for Device Status</span>");
            $('.HID-' + hid).attr("disabled", true);
            $('.HID-' + hid).hide();
            progress = setInterval(Update_Progress_Device(hid), "100");
        },
        success: function (data) {
            clearInterval(progress);
        }
    })
}

function Update_Device_Status(target,hid){
    var results = "&nbsp;- DENON - ";
    var temp = "";
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=17",
        beforeSend: function () {
            $('#' + target).html("<progress>processing...</progress>");
            $('.'+hid).attr("disabled",true);
            $('.HID-'+hid).hide();
            $('.HID-'+hid).css("color",'yellow');
        },
        dataType: "text",
        success: function (data) {
            if (data == "TIMEOUT" || data == "") {
                $('#' + target).show();
                $('#' + target).html("No Response, Piracom is may not be running or device is off");
                $('.HID-'+hid).attr("disabled",true);
                //setTimeout( $('#' + target).html(results), 1000);
            }
            else if ($(data).find('@0TR')) {
                $('#' + target).html("track cued: " + data);
                $('.HID-'+hid).attr("disabled",false);
                $('.HID-'+hid).show();
            }
            else {
                $('#' + target).slideDown();
                $('#' + target).html(data);
                $('.HID-'+hid).attr("disabled",false);
                $('.HID-'+hid).show();
                //setTimeout( $('#' + target).html("DENON"), 10000);
            }
            temp = data;
        },
        error: function () {

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