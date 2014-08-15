var mouse_over, selected_id = 0;

function list_onLoad() {
    mouse_over = false;
    selected_id = 1;
    window.addEventListener && window.addEventListener("DOMMouseScroll", list_onMousewheel, false);
    window.onmousewheel = document.onmousewheel = list_onMousewheel;
    list_selected(1)
}

function list_wheel_handler(delta) {
    delta *= -1;
    if (mouse_over == false) {
        document.window.scrollTop += delta * 30;
        return
    }
    if (document.getElementById("tno_body")) document.getElementById("tno_body").scrollTop += delta * 30;
    if (document.getElementById("track_body")) document.getElementById("track_body").scrollTop += delta * 30;
    if (document.getElementById("size_body")) document.getElementById("size_body").scrollTop += delta * 30;
    if (document.getElementById("fnm_body")) document.getElementById("fnm_body").scrollTop += delta * 30
}

function list_onMousewheel(event) {
    var delta = 0;
    if (!event) event = window.event;
    if (event.wheelDelta) delta = event.wheelDelta / 120;
    else if (event.detail) delta = -event.detail / 3;
    delta && list_wheel_handler(delta);
    event.preventDefault && event.preventDefault();
    event.returnValue = false
}

function list_onMouseOver() {
    mouse_over = true
}

function list_onMouseOut() {
    mouse_over = false
}

function list_onScroll() {
    var e_tno = document.getElementById("tno_body"),
        e_trk = document.getElementById("track_body"),
        e_siz = document.getElementById("size_body");
    if (e_tno) e_tno.scrollTop = document.getElementById("fnm_body").scrollTop;
    if (e_trk) e_trk.scrollTop = document.getElementById("fnm_body").scrollTop;
    if (e_siz) e_siz.scrollTop = document.getElementById("fnm_body").scrollTop
}

function list_selected(no) {
    var tno_id, trk_id, siz_id, fnm_id, color;
    if (document.getElementById("tno1") == null) {
        selected_id = 0;
        return
    }
    if (selected_id > 0) {
        tno_id = "tno" + selected_id;
        trk_id = "track" + selected_id;
        siz_id = "size" + selected_id;
        fnm_id = "fnm" + selected_id;
        if (selected_id & 1) color = "#606060";
        else color = "#505050"; if (document.getElementById(tno_id)) document.getElementById(tno_id).style.backgroundColor = color;
        if (document.getElementById(trk_id)) document.getElementById(trk_id).style.backgroundColor = color;
        if (document.getElementById(siz_id)) document.getElementById(siz_id).style.backgroundColor = color;
        if (document.getElementById(fnm_id)) document.getElementById(fnm_id).style.backgroundColor = color
    }
    tno_id = "tno" + no;
    trk_id = "track" + no;
    siz_id = "size" + no;
    fnm_id = "fnm" + no;
    color = "rgb(15,56,81)";
    if (document.getElementById(tno_id)) document.getElementById(tno_id).style.backgroundColor = color;
    if (document.getElementById(trk_id)) document.getElementById(trk_id).style.backgroundColor = color;
    if (document.getElementById(siz_id)) document.getElementById(siz_id).style.backgroundColor = color;
    if (document.getElementById(fnm_id)) document.getElementById(fnm_id).style.backgroundColor = color;
    selected_id = no
}

function list_get_track(row) {
    if (row <= 0) return "";
    var id = "tnod" + row;
    if (document.getElementById(id) == null) return "";
    return document.getElementById(id).innerHTML
}

function list_get_title(row) {
    if (row <= 0) return "";
    var id = "trackd" + row;
    if (document.getElementById(id) == null) return "";
    return document.getElementById(id).innerHTML
}

function list_get_size(row) {
    if (row <= 0) return "";
    var id = "sized" + row;
    if (document.getElementById(id) == null) return "";
    return document.getElementById(id).innerHTML
}

function list_get_folder(row) {
    if (row <= 0) return "";
    var id = "fnmd" + row;
    if (document.getElementById(id) == null) return "";
    return document.getElementById(id).innerHTML
}

function list_get_selectRno() {
    return selected_id
}

function list_get_selectTrack() {
    var id;
    if (selected_id <= 0) return "----";
    id = "tnod" + selected_id;
    return document.getElementById(id).innerHTML
}

function list_get_selectTitle() {
    var id;
    if (selected_id <= 0) return "----";
    id = "trackd" + selected_id;
    return document.getElementById(id).innerHTML
}

function list_get_selectSize() {
    var id;
    if (selected_id <= 0) return "----";
    id = "sized" + selected_id;
    return document.getElementById(id).innerHTML
}

function list_get_selectFolder() {
    var id;
    if (selected_id <= 0) return "----";
    id = "fnmd" + selected_id;
    return document.getElementById(id).innerHTML
}

function list_pageUp() {
    if (document.getElementById("tno_body")) document.getElementById("tno_body").scrollTop += -50 * 4;
    if (document.getElementById("track_body")) document.getElementById("track_body").scrollTop += -50 * 4;
    if (document.getElementById("size_body")) document.getElementById("size_body").scrollTop += -50 * 4;
    if (document.getElementById("fnm_body")) document.getElementById("fnm_body").scrollTop += -50 * 4
}

function list_pageDown() {
    if (document.getElementById("tno_body")) document.getElementById("tno_body").scrollTop += 50 * 4;
    if (document.getElementById("track_body")) document.getElementById("track_body").scrollTop += 50 * 4;
    if (document.getElementById("size_body")) document.getElementById("size_body").scrollTop += 50 * 4;
    if (document.getElementById("fnm_body")) document.getElementById("fnm_body").scrollTop += 50 * 4
}

function list_get_loadingTag() {
    var tag = "<table class='data'><tr style='vertical-align:middle;'><td style='text-align:center;vertical-align:middle;'>Now Loading...</td></tr></table>";
    return tag
}