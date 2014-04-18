function Query_Device(target,code,hid){
    var result_status = "";
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.php?HID=" + hid + "&CMD=" + code,
        beforeSend: function () {
            $('#' + target).html("<processing></processing>");
        },
        success: function (data) {
            /*if (data == "") {
            $('#' + target).show();
            }
            else{
            $('#'+target).slideDown();
            }*/
            result_status = data;
        }
    });
    if ($(result_status).find('@0TR')){
        $('#' + target).html(" Track Changed"+result_status.substring(5,3));
    }
    else if($(result_status).find('@0TR')){
        $('#' + target).html(" Track Changed"+result_status.substring(5,3));
    }
    else{
        $('#' + target).html(data);
    }
}

function Update_Device_Status(target,hid){
    var results = "&nbsp;- DENON - ";
    var temp = "";
    $.ajax({
        url: "EPV3/AJAX/components/hardware_query.JSON.php?HID=" + hid + "&CMD=17",
        success: function (data) {
            /*if (data == "") {
            $('#' + target).show();
            }
            else{
            $('#'+target).slideDown();
            }*/
            temp = data;
        }
    });
    /*if ($(results).find('@0TR')){
        results = temp;
    }
    else{
        results = "TRACK ERROR";
    }*/
    $('#' + target).html(results);
}