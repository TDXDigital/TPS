var left_value, right_value, peak_left_value, peak_right_value, rtime_mode = "1",
    meter_map, tport_map;

function stat_onLoad() {
    stat_init_map();
    stat_reset_meter();
    stat_init_timer()
}

function stat_init_map() {
    map = new Array(41);
    map[0] = -60;
    map[1] = -58;
    map[2] = -56;
    map[3] = -54;
    map[4] = -52;
    map[5] = -50;
    map[6] = -48;
    map[7] = -46;
    map[8] = -44;
    map[9] = -42;
    map[10] = -40;
    map[11] = -38;
    map[12] = -36;
    map[13] = -34;
    map[14] = -32;
    map[15] = -30;
    map[16] = -28;
    map[17] = -26;
    map[18] = -24;
    map[19] = -22;
    map[20] = -20;
    map[21] = -19;
    map[22] = -18;
    map[23] = -17;
    map[24] = -16;
    map[25] = -15;
    map[26] = -14;
    map[27] = -13;
    map[28] = -12;
    map[29] = -11;
    map[30] = -10;
    map[31] = -9;
    map[32] = -8;
    map[33] = -7;
    map[34] = -6;
    map[35] = -5;
    map[36] = -4;
    map[37] = -3;
    map[38] = -2;
    map[39] = -1;
    map[40] = 0;
    meter_map = map;
    map = new Array(22);
    map[0] = "tport_play";
    map[1] = "tport_pause";
    map[2] = "tport_cue";
    map[3] = "tport_stop";
    map[4] = "tport_apause";
    map[5] = "tport_fwd_x2";
    map[6] = "tport_fwd_x5";
    map[7] = "tport_fwd_x10";
    map[8] = "tport_fwd_x20";
    map[9] = "tport_fwd_x50";
    map[10] = "tport_fwd_x100";
    map[11] = "tport_fwd_x200";
    map[12] = "tport_fwd_x1";
    map[13] = "tport_rwd_x2";
    map[14] = "tport_rwd_x5";
    map[15] = "tport_rwd_x10";
    map[16] = "tport_rwd_x20";
    map[17] = "tport_rwd_x50";
    map[18] = "tport_rwd_x100";
    map[19] = "tport_rwd_x200";
    map[20] = "tport_rwd_x1";
    map[21] = "";
    tport_map = map
}

function stat_get_meterMapValue(index) {
    return meter_map[index]
}

function stat_get_tportMapValue(index) {
    return tport_map[index]
}

function stat_reset_meter() {
    left_value = 0;
    right_value = 0;
    peak_left_value = 0;
    peak_right_value = 0
}

function stat_updateMeter() {
    for (var id, idl, idr, i = 1; i <= 41; i++) {
        idl = "ml" + i;
        idr = "mr" + i;
        document.getElementById(idl).style.visibility = left_value < i ? "hidden" : "visible";
        document.getElementById(idr).style.visibility = right_value < i ? "hidden" : "visible"
    }
    document.getElementById("ml44").style.visibility = document.getElementById("ml41").style.visibility;
    if (peak_left_value > 0) {
        id = "ml" + peak_left_value;
        document.getElementById(id).style.visibility = "visible";
        stat_setPeakLeft(peak_left_value)
    }
    document.getElementById("mr44").style.visibility = document.getElementById("mr41").style.visibility;
    if (peak_right_value > 0) {
        id = "mr" + peak_right_value;
        document.getElementById(id).style.visibility = "visible";
        stat_setPeakRight(peak_right_value)
    }
}

function stat_setAudioLevel(left, right, peakleft, peakright) {
    left_value = left;
    right_value = right;
    peak_left_value = peakleft;
    peak_right_value = peakright;
    stat_updateMeter()
}

function stat_setPeakLeft(index) {
    document.getElementById("plv").innerText = stat_get_meterMapValue(index - 1)
}

function stat_setPeakRight(index) {
    document.getElementById("prv").innerText = stat_get_meterMapValue(index - 1)
}

function stat_setTitle(title) {
    document.getElementById("music_title").innerText = title;
    document.getElementById("status_title").title = title
}

function stat_setTrack(track) {
    var tno = "    ";
    if (track) tno = ("000" + track).substr(-4);
    document.getElementById("strack").innerText = tno
}

function stat_setTport(tport) {
    if (tport > 20) return;
    document.getElementById("tport_status").className = stat_get_tportMapValue(tport)
}

function stat_setTime(elapse, remain) {
    document.getElementById("time").innerText = rtime_mode == "1" ? elapse : remain
}

function stat_changeRtimeMode() {
    rtime_mode = parseInt(document.form_rtime.rtim.options[document.form_rtime.rtim.selectedIndex].value)
}

function stat_changeProgramMode(on) {
    var e_prog = document.getElementById("sts_prog");
    if (on == true) e_prog.className = "sts_prog_on";
    else e_prog.className = "sts_prog_off"
}

function stat_changeLockMode(on) {
    var e_lock = document.getElementById("sts_lock");
    if (on == true) e_lock.className = "sts_lock_on";
    else e_lock.className = "sts_lock_off"
}

function stat_ajaxUpdateStatus(cgi, par) {
    new Ajax.Request(cgi, {
        method: "get",
        parameters: "p=" + par + "&t=" + +new Date,
        onSuccess: stat_ajaxUpdateStatHandle,
        onFailure: stat_ajaxFailureHandle,
        onException: stat_ajaxExceptHandle
    })
}
var stat_ajaxUpdateStatHandle = function(transport) {
        var ps;
        if (transport.responseText == undefined) return;
        ps = transport.responseText.evalJSON();
        stat_changeProgramMode(ps.m_prog);
        stat_changeLockMode(ps.m_lock);
        stat_setTport(ps.m_play);
        stat_setTitle(ps.v_playtrack == 0 ? "" : ps.v_playfile);
        stat_setTrack(ps.v_playtrack);
        stat_setTime(ps.v_elapse, ps.v_remain);
        stat_setAudioLevel(ps.v_levelleft, ps.v_levelright, ps.v_peakleft, ps.v_peakright)
    },
    stat_ajaxFailureHandle = function(transport) {},
    stat_ajaxExceptHandle = function(transport, e) {}