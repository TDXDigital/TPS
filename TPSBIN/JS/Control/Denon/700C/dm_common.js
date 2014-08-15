var page_type, navigator_type = 0,
    func_standby = null;

function comm_onLoad() {
    comm_getPageType();
    comm_viewNavigatorButton();
    comm_startKeepAlive()
}

function comm_addOnload(func) {
    try {
        window.addEventListener("load", func, false)
    } catch (e) {
        window.attachEvent("onload", func)
    }
}

function comm_resizeWindow() {
    if (navigator_type == 0)
        if (comm_isMobileScreen()) window.resizeTo(480 + 35, 900);
        else window.resizeTo(1024, 900)
}

function comm_getPageType() {
    var e_pt = document.getElementById("pt");
    if (e_pt == null) return;
    page_type = parseInt(e_pt.value)
}

function comm_isFullScreen() {
    return page_type == 0 ? 1 : 0
}

function comm_isMobileScreen() {
    return page_type == 0 ? 0 : 1
}

function comm_updateSelectbox(id, idx) {
    document.getElementById("select" + id).childNodes[0].nodeValue = document.getElementById(id).options[idx].childNodes[0].nodeValue
}

function menu_post(url) {
    var fm = document.form_menu;
    fm.setAttribute("action", url);
    fm.setAttribute("method", "post");
    fm.submit()
}

function menu_changeNavi() {
    menu_post(document.getElementById("ru").value)
}

function comm_setViewport() {
    if (navigator.userAgent.indexOf("iPhone") > 0) {
        document.write('<meta name="viewport" content="initial-scale=0.667, minimum-scale=0.667, maximum-scale=0.667">');
        navigator_type = 1
    } else if (navigator.userAgent.indexOf("iPad") > 0) {
        document.write('<meta name="viewport" content="device-width, initial-scale=1.0">');
        navigator_type = 2
    } else if (navigator.userAgent.indexOf("Android") > 0 && navigator.userAgent.indexOf("Mobile") > 0) {
        document.write('<meta name="viewport" content="width=480px">');
        navigator_type = 3
    } else if (navigator.userAgent.indexOf("Android") > 0 && navigator.userAgent.indexOf("Mobile") <= 0) {
        document.write('<meta name="viewport" content="width=980px">');
        navigator_type = 4
    } else navigator_type = 0
}

function comm_isPhoneView() {
    return navigator_type == 1 || navigator_type == 3 ? 1 : 0
}

function comm_isTabletView() {
    return navigator_type == 2 || navigator_type == 4 ? 1 : 0
}

function comm_viewNavigatorButton() {
    var e_cn = document.getElementById("chg_navi");
    if (e_cn == null) return;
    if (comm_isPhoneView() || comm_isTabletView()) e_cn.style.visibility = "hidden"
}
window.onbeforeunload = function(e) {};

function comm_trimNumber(s) {
    return s.replace(/^0+/, "")
}

function comm_startKeepAlive() {
    var e_sid = document.getElementsByName("sid")[0];
    if (e_sid == null || e_sid.value == 255) return;
    comm_timer_3000();
    comm_init_timer()
}

function comm_init_timer() {
    setInterval("comm_timer_3000()", 3e3)
}

function comm_timer_3000() {
    var jo = {};
    jo.v_sid = parseInt(document.getElementsByName("sid")[0].value);
    comm_ajaxKeepAlive(Object.toJSON(jo))
}

function comm_ajaxKeepAlive(json) {
    new Ajax.Request("/denon/ajax_keepalive.cgi", {
        method: "post",
        parameters: "p=0&json=" + json + "&t=" + +new Date,
        onSuccess: comm_ajaxPostControlHandle,
        onFailure: comm_ajaxFailureHandle,
        onException: comm_ajaxExceptHandle
    })
}

function comm_ajaxSessionClose(json) {
    new Ajax.Request("/denon/ajax_keepalive.cgi", {
        method: "post",
        parameters: "p=1&json=" + json + "&t=" + +new Date,
        onSuccess: comm_ajaxPostControlHandle,
        onFailure: comm_ajaxFailureHandle,
        onException: comm_ajaxExceptHandle
    })
}

function comm_registStandbyFunction(func) {
    func_standby = func
}

function comm_startGetStandbyStatus() {
    comm_timer_1500();
    setInterval("comm_timer_1500()", 1500)
}

function comm_timer_1500() {
    comm_ajaxGetStandbyStatus()
}

function comm_redirectPoffScreen() {
    document.form_redirect_poff.submit()
}

function comm_ajaxGetStandbyStatus() {
    new Ajax.Request("/denon/ajax_standby.cgi", {
        method: "get",
        parameters: "p=0&t=" + +new Date,
        onSuccess: comm_ajaxGetStandbyStatusHandle,
        onFailure: comm_ajaxFailureHandle,
        onException: comm_ajaxExceptHandle
    })
}
var comm_ajaxGetStandbyStatusHandle = function(transport) {
        var ss;
        ss = transport.responseText.evalJSON();
        func_standby(ss.b_standby)
    },
    comm_ajaxPostControlHandle = function(transport) {},
    comm_ajaxFailureHandle = function(transport) {},
    comm_ajaxExceptHandle = function(transport, e) {}