var pitch = null,
    volume = null,
    scrn_disp, source_value = -1,
    track_last_time = 0,
    pitch_value = 1e4,
    volume_value = 1e4,
    pitch_last_value = -1e4,
    volume_last_value = -1e4,
    pitch_set_last_value = -1e4,
    volume_set_last_value = -1e4,
    timer_counter = 0,
    pitch_delay_counter = 0,
    volume_delay_counter = 0,
    pitch_enable_ref = true,
    volume_enable_ref = true;

function ctrl_onChangeSelect(name) {
    switch (name) {
        case "source":
            ctrl_changeSource();
            break;
        case "rtim":
            stat_changeRtimeMode();
            break;
        case "pit_int":
        case "pit_small":
            ctrl_onChangePitchValue();
            break;
        case "vol_int":
        case "vol_small":
            ctrl_onChangeVolumeValue();
            break
    }
}

function ctrl_onStandbyStatus(status) {
    status > 0 && comm_redirectPoffScreen()
}

function ctrl_onLoad() {
    comm_addSelectEvent(ctrl_onChangeSelect);
    comm_registStandbyFunction(ctrl_onStandbyStatus);
    comm_startGetStandbyStatus();
    ctrl_init_slider();
    ctrl_init_changeScreen();
    ctrl_ajaxUpdateUI();
    ctrl_init_timer()
}

function ctrl_init_slider() {
    if (comm_isMobileScreen() > 0) return;
    pitch = new Control.Slider("pitch_point", "pitch", {
        minimum: 0,
        maximum: 320,
        sliderValue: 160,
        range: $R(0, 320),
        onChange: function(value) {
            var v = (Math.floor(value) - 160) / 10;
            $("pitch_value").innerHTML = v;
            pitch_last_value = value;
            pitch_enable_ref = false
        },
        onSlide: function(value) {
            var v = (Math.floor(value) - 160) / 10;
            $("pitch_value").innerHTML = v;
            if (pitch_last_value != value) {
                pitch_last_value = value;
                pitch_enable_ref = false
            }
        }
    });
    volume = new Control.Slider("volume_point", "volume", {
        minimum: 0,
        maximum: 701,
        sliderValue: 351,
        range: $R(0, 701),
        onChange: function(value) {
            var v = (Math.floor(value) - 601) / 10;
            if (v == -60.1) {
                $("volume_value").innerHTML = "Mute";
                $("volume_label").innerHTML = ""
            } else {
                $("volume_value").innerHTML = v;
                $("volume_label").innerHTML = "dB"
            }
            volume_last_value = value;
            volume_enable_ref = false
        },
        onSlide: function(value) {
            var v = (Math.floor(value) - 601) / 10;
            if (v == -60.1) {
                $("volume_value").innerHTML = "Mute";
                $("volume_label").innerHTML = ""
            } else {
                $("volume_value").innerHTML = v;
                $("volume_label").innerHTML = "dB"
            } if (volume_last_value != value) {
                volume_last_value = value;
                volume_enable_ref = false
            }
        }
    })
}

function ctrl_init_timer() {
    setInterval("ctrl_timer_1000()", 1e3);
    setInterval("ctrl_timer_200()", 200)
}

function ctrl_timer_1000() {
    ctrl_ajaxUpdateUI()
}

function ctrl_timer_200() {
    stat_ajaxUpdateStatus("/denon/ajax_control.cgi", 1);
    (timer_counter & 1) == 0 && ctrl_set_slider_value();
    timer_counter++
}

function ctrl_set_slider_value() {
    if (comm_isFullScreen() == 0) return;
    if (pitch_set_last_value != pitch_last_value) {
        var jo = {};
        jo.v_pitch = Math.floor(pitch_last_value);
        ctrl_ajaxPitchControl(Object.toJSON(jo));
        pitch_set_last_value = pitch_last_value;
        pitch_delay_counter = timer_counter
    }
    if (volume_set_last_value != volume_last_value) {
        var jo = {};
        jo.v_volume = Math.floor(volume_last_value);
        ctrl_ajaxVolumeControl(Object.toJSON(jo));
        volume_set_last_value = volume_last_value;
        volume_delay_counter = timer_counter
    }
    if (timer_counter - pitch_delay_counter > 6) pitch_enable_ref = true;
    if (timer_counter - volume_delay_counter > 6) volume_enable_ref = true
}

function ctrl_init_changeScreen() {
    if (comm_isMobileScreen() > 0) return;
    var e_schg = document.getElementById("scrn_chg");
    scrn_disp = 1;
    e_schg.className = "up_v"
}

function ctrl_changeScreen() {
    if (scrn_disp == 1) {
        document.getElementById("block_pv").style.display = "none";
        document.getElementById("block_list").style.display = "none";
        document.getElementById("block_line").style.display = "none";
        document.getElementById("block_lock").style.display = "none";
        document.getElementById("scrn_chg").className = "down_v";
        scrn_disp = 0
    } else {
        document.getElementById("block_pv").style.display = "block";
        document.getElementById("block_list").style.display = "block";
        document.getElementById("block_line").style.display = "block";
        document.getElementById("block_lock").style.display = "block";
        document.getElementById("scrn_chg").className = "up_v";
        scrn_disp = 1
    }
}

function ctrl_play() {
    ctrl_tportCommand(0)
}

function ctrl_stop() {
    ctrl_tportCommand(1)
}

function ctrl_cue() {
    ctrl_tportCommand(2)
}

function ctrl_pause() {
    ctrl_tportCommand(3)
}

function ctrl_fastRwd() {
    ctrl_tportCommand(4)
}

function ctrl_fastFwd() {
    ctrl_tportCommand(5)
}

function ctrl_trackRwd() {
    ctrl_tportCommand(6)
}

function ctrl_trackFwd() {
    ctrl_tportCommand(7)
}

function ctrl_skipBack() {
    ctrl_tportCommand(8)
}

function ctrl_tportCommand(cmd) {
    var jo = {};
    jo.v_command = cmd;
    ctrl_ajaxPlayControl(Object.toJSON(jo))
}

function ctrl_loadTrack() {
    var jo = {};
    jo.v_track = parseInt(comm_trimNumber(list_get_selectTrack()));
    ctrl_ajaxSetTrack(Object.toJSON(jo))
}

function ctrl_changeFolder(url) {
    var fc = document.form_control;
    fc.setAttribute("action", url);
    fc.setAttribute("method", "post");
    fc.submit()
}

function ctrl_downloadTrack(url) {
    var query, tno = parseInt(comm_trimNumber(list_get_selectTrack())),
        fname = list_get_selectTitle();
    query = url + "?tno=" + tno + "&fname=" + fname;
    location.href = query
}

function ctrl_enableSource(source_enable) {
    var e_source = document.getElementById("source"),
        e_server = document.getElementById("select_server");
    if (source_enable == true) {
        e_source.disabled = false;
        e_server.disabled = false;
        e_server.className = "select_server_v"
    } else {
        e_source.disabled = true;
        e_server.disabled = true;
        e_server.className = "select_server_i"
    }
}

function ctrl_selectSource(src) {
    document.form_source.source.options[src].selected = true;
    comm_updateSelectbox("source", src)
}

function ctrl_changeSource() {
    var v;
    v = document.form_source.source.options[document.form_source.source.selectedIndex].value;
    source_value = parseInt(v);
    var jo = {};
    jo.v_source = source_value;
    ctrl_ajaxSetSource(Object.toJSON(jo));
    ctrl_visibleSelectServer()
}

function ctrl_selectServer() {
    var e_server_url = document.getElementById("server_list"),
        fs = document.form_source_list;
    fs.setAttribute("action", e_server_url.value);
    fs.setAttribute("method", "post");
    fs.submit()
}

function ctrl_visibleSelectServer() {
    var e_server = document.getElementById("select_server");
    if (source_value == "3") e_server.style.visibility = "visible";
    else e_server.style.visibility = "hidden"
}

function ctrl_appendPitchValue(val) {
    var t = document.getElementById("pitch_value").innerHTML,
        v = parseFloat(t);
    v += val;
    v *= 10;
    v += 160;
    v = Math.round(v);
    pitch.setValue(v);
    var jo = {};
    jo.v_pitch = v
}

function ctrl_pitchUp() {
    ctrl_appendPitchValue(.1)
}

function ctrl_pitchDown() {
    ctrl_appendPitchValue(-.1)
}

function ctrl_updatePitchState(pitch_on, mkey_on) {
    var e_pitch = document.getElementById("pitch_swi"),
        e_mkey = document.getElementById("mkey");
    e_pitch.style.color = pitch_on == true ? "#ff0000" : "#ffffff";
    e_mkey.style.color = mkey_on == true ? "#ff0000" : "#ffffff"
}

function ctrl_enablePitch(pitch_enable, mkey_enable) {
    var e_pitch_up = document.getElementById("pitch_up"),
        e_pitch_down = document.getElementById("pitch_down"),
        e_pitch = document.getElementById("pitch_swi"),
        e_point = document.getElementById("pitch_point"),
        e_mkey = document.getElementById("mkey");
    if (comm_isFullScreen() > 0)
        if (pitch_enable == true) {
            e_pitch_up.disabled = false;
            e_pitch_up.className = "up_v";
            e_pitch_down.disabled = false;
            e_pitch_down.className = "down_v";
            e_point.className = "front_v";
            e_pitch.disabled = false;
            e_pitch.className = "pitch_v";
            pitch.setEnabled()
        } else {
            e_pitch_up.disabled = true;
            e_pitch_up.className = "up_i";
            e_pitch_down.disabled = true;
            e_pitch_down.className = "down_i";
            e_point.className = "front_i";
            e_pitch.disabled = true;
            e_pitch.className = "pitch_i";
            pitch.setDisabled()
        } else {
        var e_pitch_int = document.getElementById("pit_int"),
            e_pitch_small = document.getElementById("pit_small");
        if (pitch_enable == true) {
            e_pitch_int.disabled = false;
            e_pitch_small.disabled = false;
            e_pitch.disabled = false;
            e_pitch.className = "pitch_v"
        } else {
            e_pitch_int.disabled = true;
            e_pitch_small.disabled = true;
            e_pitch.disabled = true;
            e_pitch.className = "pitch_i"
        }
    } if (mkey_enable == true) {
        e_mkey.disabled = false;
        e_mkey.className = "mkey_v"
    } else {
        e_mkey.disabled = true;
        e_mkey.className = "mkey_i"
    }
}

function ctrl_pitchOn() {
    ctrl_ajaxPitchStatus()
}

function ctrl_mkeyOn() {
    ctrl_ajaxMkeyStatus()
}

function ctrl_setPitchValue(value) {
    var e_pitch_int = document.getElementById("pit_int"),
        e_pitch_small = document.getElementById("pit_small"),
        pint, i;
    if (value - 160 < 0) pint = Math.ceil((value - 160) / 10);
    else pint = Math.floor((value - 160) / 10);
    var psml = (value - 160) % 10;
    if (psml < 0) {
        psml *= -1;
        if (pint == 0) pint = "m" + pint
    }
    var e_pit_int = document.form_pitch.pit_int;
    for (i = 0; i < e_pit_int.length; i++)
        if (e_pit_int.options[i].value == pint) {
            e_pit_int.options[i].selected = true;
            comm_updateSelectbox("pit_int", i);
            break
        }
    var e_pit_small = document.form_pitch.pit_small;
    for (i = 0; i < e_pit_small.length; i++)
        if (e_pit_small.options[i].value == psml) {
            e_pit_small.options[i].selected = true;
            comm_updateSelectbox("pit_small", i);
            break
        }
    pitch_value = value
}

function ctrl_onChangePitchValue() {
    var e_pit_int = document.form_pitch.pit_int,
        e_pit_small = document.form_pitch.pit_small,
        v1, v2, val;
    v1 = e_pit_int.options[e_pit_int.selectedIndex].value;
    v2 = e_pit_small.options[e_pit_small.selectedIndex].value;
    if (v1 == "m0") v1 = "-0";
    val = v1 + "." + v2;
    val = parseFloat(val);
    val *= 10;
    val += 160;
    if (val > 320) val = 320;
    if (val < 0) val = 0;
    ctrl_setPitchValue(val);
    var jo = {};
    jo.v_pitch = val;
    ctrl_ajaxPitchControl(Object.toJSON(jo))
}

function ctrl_appendVolumeValue(val) {
    var t = document.getElementById("volume_value").innerHTML;
    if (t == "Mute") t = -60.1;
    var v = parseFloat(t);
    v += val;
    v *= 10;
    v += 601;
    v = Math.round(v);
    volume.setValue(v);
    var jo = {};
    jo.v_volume = v
}

function ctrl_volumeUp() {
    ctrl_appendVolumeValue(.1)
}

function ctrl_volumeDown() {
    ctrl_appendVolumeValue(-.1)
}

function ctrl_enableVolume(volume_enable) {
    var e_volume_up = document.getElementById("volume_up"),
        e_volume_down = document.getElementById("volume_down"),
        e_point = document.getElementById("volume_point");
    if (comm_isFullScreen() > 0)
        if (volume_enable == true) {
            e_volume_up.disabled = false;
            e_volume_up.className = "up_v";
            e_volume_down.disabled = false;
            e_volume_down.className = "down_v";
            e_point.className = "front_v";
            volume.setEnabled()
        } else {
            e_volume_up.disabled = true;
            e_volume_up.className = "up_i";
            e_volume_down.disabled = true;
            e_volume_down.className = "down_i";
            e_point.className = "front_i";
            volume.setDisabled()
        } else {
        var e_volume_int = document.getElementById("vol_int"),
            e_volume_small = document.getElementById("vol_small");
        if (volume_enable == true) {
            e_volume_int.disabled = false;
            e_volume_small.disabled = false
        } else {
            e_volume_int.disabled = true;
            e_volume_small.disabled = true
        }
    }
}

function ctrl_setVolumeValue(value) {
    var e_volume_int = document.getElementById("vol_int"),
        e_volume_small = document.getElementById("vol_small"),
        vint, vsml, i;
    if (value == 0) {
        vint = -61;
        vsml = 0
    } else {
        if (value - 601 < 0) vint = Math.ceil((value - 601) / 10);
        else vint = Math.floor((value - 601) / 10);
        vsml = (value - 601) % 10;
        if (vsml < 0) {
            vsml *= -1;
            if (vint == 0) vint = "m" + vint
        }
    }
    var e_vol_int = document.form_volume.vol_int;
    for (i = 0; i < e_vol_int.length; i++)
        if (e_vol_int.options[i].value == vint) {
            e_vol_int.options[i].selected = true;
            comm_updateSelectbox("vol_int", i);
            break
        }
    var e_vol_small = document.form_volume.vol_small;
    for (i = 0; i < e_vol_small.length; i++)
        if (e_vol_small.options[i].value == vsml) {
            e_vol_small.options[i].selected = true;
            comm_updateSelectbox("vol_small", i);
            break
        }
    volume_value = value
}

function ctrl_onChangeVolumeValue() {
    var e_vol_int = document.form_volume.vol_int,
        e_vol_small = document.form_volume.vol_small,
        v1, v2, val;
    v1 = e_vol_int.options[e_vol_int.selectedIndex].value;
    v2 = e_vol_small.options[e_vol_small.selectedIndex].value;
    if (v1 == "m0") v1 = "-0";
    val = v1 + "." + v2;
    val *= 10;
    val += 601;
    if (val > 701) val = 701;
    if (val < 0) val = 0;
    ctrl_setVolumeValue(val);
    var jo = {};
    jo.v_volume = val;
    ctrl_ajaxVolumeControl(Object.toJSON(jo));
    return 1
}

function ctrl_panelLock() {
    ctrl_ajaxLockStatus()
}

function ctrl_enableLock(lock_on) {
    var e_lock = document.getElementById("lockp");
    if (lock_on == true) e_lock.disabled = false;
    else e_lock.disabled = true
}

function ctrl_updateLockState(lock_on) {
    var e_lock = document.getElementById("lockp");
    if (lock_on == true) e_lock.style.color = "#ff0000";
    else e_lock.style.color = "#ffffff"
}

function ctrl_enableTportButton1(stop_on, cue_on, pause_on, play_on) {
    var e_play = document.getElementById("play"),
        e_stop = document.getElementById("stop"),
        e_cue = document.getElementById("cue"),
        e_pause = document.getElementById("pause");
    if (stop_on == true) {
        e_stop.disabled = false;
        e_stop.className = "stop_v"
    } else {
        e_stop.disabled = true;
        e_stop.className = "stop_i"
    } if (cue_on == true) {
        e_cue.disabled = false;
        e_cue.className = "cue_v"
    } else {
        e_cue.disabled = true;
        e_cue.className = "cue_i"
    } if (pause_on == true) {
        e_pause.disabled = false;
        e_pause.className = "pause_v"
    } else {
        e_pause.disabled = true;
        e_pause.className = "pause_i"
    } if (play_on == true) {
        e_play.disabled = false;
        e_play.className = "play_v"
    } else {
        e_play.disabled = true;
        e_play.className = "play_i"
    }
}

function ctrl_enableTportButton2(rwd_on, fwd_on, trwd_on, tfwd_on, bskip_on) {
    var e_rwd = document.getElementById("rwd"),
        e_fwd = document.getElementById("fwd"),
        e_trwd = document.getElementById("trwd"),
        e_tfwd = document.getElementById("tfwd"),
        e_bskip = document.getElementById("bskip");
    if (rwd_on == true) {
        e_rwd.disabled = false;
        e_rwd.className = "rewind_v"
    } else {
        e_rwd.disabled = true;
        e_rwd.className = "rewind_i"
    } if (fwd_on == true) {
        e_fwd.disabled = false;
        e_fwd.className = "foword_v"
    } else {
        e_fwd.disabled = true;
        e_fwd.className = "foword_i"
    } if (trwd_on == true) {
        e_trwd.disabled = false;
        e_trwd.className = "bfeed_v"
    } else {
        e_trwd.disabled = true;
        e_trwd.className = "bfeed_i"
    } if (tfwd_on == true) {
        e_tfwd.disabled = false;
        e_tfwd.className = "feed_v"
    } else {
        e_tfwd.disabled = true;
        e_tfwd.className = "feed_i"
    } if (bskip_on == true) {
        e_bskip.disabled = false;
        e_bskip.className = "bskip_v"
    } else {
        e_bskip.disabled = true;
        e_bskip.className = "bskip_i"
    }
}

function ctrl_enableCommand(load_enable, cd_enable, dload_enable) {
    var e_load = document.getElementById("load"),
        e_cd = document.getElementById("cd"),
        e_dload = document.getElementById("dload");
    if (load_enable == true) {
        e_load.disabled = false;
        e_load.className = "command_v"
    } else {
        e_load.disabled = true;
        e_load.className = "command_i"
    } if (cd_enable == true) {
        e_cd.disabled = false;
        e_cd.className = "command_v"
    } else {
        e_cd.disabled = true;
        e_cd.className = "command_i"
    } if (comm_isFullScreen() > 0)
        if (dload_enable == true) {
            e_dload.disabled = false;
            e_dload.className = "command_v"
        } else {
            e_dload.disabled = true;
            e_dload.className = "command_i"
        }
}

function ctrl_ajaxUpdateUI() {
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "get",
        parameters: "p=0&t=" + +new Date,
        onSuccess: ctrl_ajaxUpdateUIHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}
var ctrl_ajaxUpdateUIHandle = function(transport) {
    var uis;
    uis = transport.responseText.evalJSON();
    ctrl_enableTportButton1(uis.b_stop ? true : false, uis.b_cue ? true : false, uis.b_pause ? true : false, uis.b_play ? true : false);
    ctrl_enableTportButton2(uis.b_fastrwd ? true : false, uis.b_fastfwd ? true : false, uis.b_trackrwd ? true : false, uis.b_trackfwd ? true : false, uis.b_skipback ? true : false);
    ctrl_enablePitch(uis.b_pitch ? true : false, uis.b_mkey ? true : false);
    uis.b_pitch && ctrl_updatePitchState(uis.b_pitch == 2 ? true : false, uis.b_mkey == 2 ? true : false);
    ctrl_enableVolume(uis.b_volume ? true : false);
    ctrl_enableSource(uis.b_source ? true : false);
    ctrl_enableCommand(uis.b_load ? true : false, uis.b_cd ? true : false, uis.b_download ? true : false);
    ctrl_enableLock(uis.b_lock ? true : false);
    uis.b_lock && ctrl_updateLockState(uis.b_lock == 2 ? true : false);
    if (comm_isFullScreen() > 0) {
        pitch_enable_ref == true && pitch.setValue(uis.v_pitch);
        volume_enable_ref == true && volume.setValue(uis.v_volume)
    } else {
        pitch_value != uis.v_pitch && ctrl_setPitchValue(uis.v_pitch);
        volume_value != uis.v_volume && ctrl_setVolumeValue(uis.v_volume)
    } if (source_value != uis.v_source) {
        source_value = uis.v_source;
        ctrl_selectSource(source_value);
        ctrl_visibleSelectServer()
    }
    if (track_last_time != uis.v_trackLast) {
        track_last_time = uis.v_trackLast;
        ctrl_ajaxUpdateTrackList(comm_isMobileScreen())
    }
};

function ctrl_ajaxUpdateTrackList(pt) {
    document.getElementById("utrack_list").innerHTML = list_get_loadingTag();
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "get",
        parameters: "p=10&m=" + pt + "&t=" + +new Date,
        onSuccess: ctrl_ajaxUpdateTrackListHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}
var ctrl_ajaxUpdateTrackListHandle = function(transport) {
    document.getElementById("utrack_list").innerHTML = transport.responseText;
    list_selected(1)
};

function ctrl_ajaxPlayControl(json) {
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "post",
        parameters: "p=0&json=" + json + "&t=" + +new Date,
        onSuccess: ctrl_ajaxPostControlHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}

function ctrl_ajaxVolumeControl(json) {
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "post",
        parameters: "p=1&json=" + json + "&t=" + +new Date,
        onSuccess: ctrl_ajaxPostControlHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}

function ctrl_ajaxPitchControl(json) {
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "post",
        parameters: "p=2&json=" + json + "&t=" + +new Date,
        onSuccess: ctrl_ajaxPostControlHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}

function ctrl_ajaxPitchStatus() {
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "post",
        parameters: "p=3&t=" + +new Date,
        onSuccess: ctrl_ajaxPostControlHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}

function ctrl_ajaxMkeyStatus() {
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "post",
        parameters: "p=4&t=" + +new Date,
        onSuccess: ctrl_ajaxPostControlHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}

function ctrl_ajaxSetTrack(json) {
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "post",
        parameters: "p=5&json=" + json + "&t=" + +new Date,
        onSuccess: ctrl_ajaxPostControlHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}

function ctrl_ajaxSetSource(json) {
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "post",
        parameters: "p=6&json=" + json + "&t=" + +new Date,
        onSuccess: ctrl_ajaxSetSourceHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}
var ctrl_ajaxSetSourceHandle = function(transport) {
    if (source_value == "3") ctrl_selectServer();
    else ctrl_ajaxUpdateTrackList(comm_isMobileScreen())
};

function ctrl_ajaxLockStatus() {
    new Ajax.Request("/denon/ajax_control.cgi", {
        method: "post",
        parameters: "p=7&t=" + +new Date,
        onSuccess: ctrl_ajaxPostControlHandle,
        onFailure: ctrl_ajaxFailureHandle,
        onException: ctrl_ajaxExceptHandle
    })
}
var ctrl_ajaxPostControlHandle = function(transport) {},
    ctrl_ajaxFailureHandle = function(transport) {},
    ctrl_ajaxExceptHandle = function(transport, e) {}