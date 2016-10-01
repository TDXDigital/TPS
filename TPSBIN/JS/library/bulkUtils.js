/**
 * Created by j.oliver on 2016-09-25.
 */
var values = {
    labels: null,
    genres: null,
    categorys: null,
    schedules: null,
    formats: null,
    locales: null
};
var labels = function(){return values['labels'];};
var genres = function(){return values['genres'];};
var categorys = function(){return values['categorys'];}; //yes this is spelt wrong but it maintains the convention
var schedules = function(){return values['schedules'];};
var formats = function(){return values['formats'];};
var locales = function(){return values['locales'];};
// levenstein sets
var labelsSet = FuzzySet();
var genresSet = FuzzySet();
var categorysSet = FuzzySet();
var categorysKeySet = FuzzySet();
var schedulesSet = FuzzySet();
var formatsSet = FuzzySet();
var localesSet = FuzzySet();

var calls = {
    pending: 0,
    complete: 0
};

var expected = {
    "artist": "Artist",
    "album": "Album",
    "genre": "Genre",
    "locale": "Region",
    "label": "Label",
    "category": "Category",
    "schedule": "Schedule",
    "format": "Format",
    "accept": "Keep",
    "print": "Print",
    "playlist": "Playlist",
    "indate": "Date In",
    "reldate": "Release Date",
    "notes": "Notes",
    "VA": "Various Artists"
};

function buildHeader(){
    header = $("#head");
    header.append("<th>Save</th>");
    $.each(expected, function(hk, hv){
        header.append("<th>"+hv+"</th>");
    });
}

function saveRow(key){
    $("#r_"+key).each(function() {
        var row = this;
        var values = "";
        params = {};
        $('input', this).each(function() {
            x = $(this);
            if(x[0].name === undefined || x[0].name == ""){
                console.log(this.id);
                return;
            }
            var value = "";
            var name = "";
            name = x[0].name.replace('[]', '');
            try{
                value = $(this).val()
            }
            catch(err){
                value = x.val();
            }
            params[name] = value;
        });
        $('select', this).each(function() {
            x = $(this);
            if(x[0].name === undefined || x[0].name == ""){
                //console.log(this.id);
                return;
            }
            var value = "";
            var name = "";
            name = x[0].name.replace('[]', '');
            try{
                value = $(this).val()
            }
            catch(err){
                value = x.val();
            }
            params[name] = value;
        });
        console.log(params);
        if(!('artist' in params) || !('label' in params)){
            console.log("missing key parameter, cannot process");
            return false;
        }
        btnText = "";
        btnProcess = "<i class=\"fa fa-circle-o-notch fa-spin\" aria-hidden=\"true\"></i>";
        $.ajax({
            url: "../new",
            method: "POST",
            data: params,
            beforeSend: function(){
                btn = $("#r_"+key+"_SaveButton");
                btnText = btn.html();
                btn.html(btnProcess);
                btn.removeClass('btn-primary');
                btn.removeClass('btn-default');
                btn.removeClass('btn-danger');
                btn.removeClass('btn-success');
                btn.addClass('btn-primary');
                btn.prop('disabled', true);
            },
            success: function(data){
                console.log("OK");
                btn = $("#r_"+key+"_SaveButton");
                btn.html(btnText);
                btn.removeClass('btn-primary');
                btn.addClass('btn-success');
                //btn.prop('disabled', false);
            },
            error: function (data) {
                console.log(data.responseText);
                btn = $("#r_"+key+"_SaveButton");
                btn.html(btnText);
                btn.removeClass('btn-primary');
                btn.addClass('btn-danger');
                btn.prop('disabled', false);
            }
        })
    });
}

function updateProgress(){
    var valeur = 0;
    try {
        valeur = (calls.complete/calls.pending)*100
    }
    catch (err){
        valeur = 0;
    }
    $('.progress-bar').css('width', valeur+'%').attr('aria-valuenow', valeur);
    if(valeur >= 100){
        $('.progress-bar').removeClass('active');
    }
    else{
        if(!$('.progress-bar').hasClass('active')){
            $('.progress-bar').addClass('active');
        }
    }
}

function all(array, condition){
    for(var i = 0; i < array.length; i++){
        if(!condition(array[i])){
            return false;
        }
    }
    return true;
}

function isValidDate(dateString) {
    var regEx = /^\d{4}-\d{2}-\d{2}$/;
    return String(dateString).match(regEx) != null;
}

function stepSelect(key, vals, translated){
    var select = {"genre": genres(), "locale": locales(), "label": labels(), "format": formats(),
        "schedule": schedules(), "category": categorys()};
    var param = vals[translated];
    if(!param === undefined){

    }
    options = select[translated];
    str += "<select name='"+key+"' class='chosen-select'>";
    ptr = this[key+"sSet"];
    mStr = "";
    if(!(param === undefined || param === null)){
        mStr = String(param);
    }
    matched = null;
    if(ptr && !(param === undefined)){
        matched = ptr.get(mStr);
    }
    if(!matched && this[key+"sKeySet"]){
        ptr = this[key+"sKeySet"];
        matched = ptr.get(mStr);
    }
    $.each(options, function(kx, vx){
        str += "<option value='"+kx+"'";
        if(matched && (matched[0][1] == vx || matched[0][1] == kx)){
            str += " selected ";
        }
        str +=">"+vx+"</option>";
    });
    str += "</select>";
}

function stepSelectUpdate(key){
    var td = $('#'+key);
    td.html(stepSelect());
}

function stepFn(results, parser){
    stepped++;
    if (results)
    {
        if (results.data)
            rowCount += results.data.length;
        var list = $("#list");
        list.append("<tr id='r_"+rowCount+"'></tr>");
        var row = $("#r_"+rowCount);
        var strings = ['artist', 'album', 'label', 'notes'];
        var dates = ['indate', 'reldate'];
        var bools = ['accept', 'print', 'playlist', 'VA'];
        var select = {"genre": genres(), "locale": locales(), "label": labels(), "format": formats(),
            "schedule": schedules(), "category": categorys()};
        vals = results.data[0];
        var hasData = false;
        row.append("<button type=\"button\" class=\"btn btn-default\" aria-label=\"Save\" onclick='saveRow(" +
            rowCount + ")' id='r_"+rowCount+"_SaveButton'>" +
            "<i class='fa fa-floppy-o' aria-hidden=\"true\"></i>" +
            "</button>");
        for(var key in expected)
        {
            var translated = $("#ma_"+key+"_sel").val();
            var vax = "";
            if(translated != ""){
                vax = vals[translated];
            }
            var str = "<td id='td_"+key+"_"+stepped+"'>" +
                "<input value='"+vax+"' readonly/>" +
                "<input type='hidden' value='false' id='changed_"+key+"'/>";
            if(translated && translated.trim() != "" && translated != "N/A" && !(key in select)){
                if(bools.indexOf(key) > -1)
                {
                    str += "<input type='checkbox' name='"+key+"[]'";
                    if(!['no', 'false', '0', ''].indexOf(vals[translated]) < 0){
                        str += " checked ";
                    }
                    str += "/>";
                }
                else if(dates.indexOf(key) > -1){
                    var dateString = vals[translated];
                    var d = new Date();
                    var month = d.getMonth();
                    var day = d.getDate();
                    try {
                        var dateObj = Date.parse(vals[translated]);
                        if(dateObj != undefined){
                            var dateString = dateObj.toString("yyyy-MM-dd");
                        }
                    }
                    catch (err){
                        dateString = d.getFullYear() + '-' +
                            (month<10 ? '0' : '') + month + '-' +
                            (day<10 ? '0' : '') + day;
                        console.log(err);
                    }
                    if(!isValidDate(dateString)){
                        console.log("invalid date "+dateString+" will use today as default");
                        dateString = d.getFullYear() + '-' +
                            (month<10 ? '0' : '') + month + '-' +
                            (day<10 ? '0' : '') + day;
                    }
                    str += "<input value='"+dateString+"' type='date' name='"+key+"[]'";
                    if(dates.indexOf(key) < 3){
                        str += " required ";
                    }
                    str += "/>";
                }
                else {
                    str += "<input value='" + vals[translated] + "' id='v_" + key + "_" + rowCount + "' name='" + key + "[]'>";
                }
                hasData = true;
            }
            else if(key in select){
                str += "<input type='hidden' value='"+translated+"'>";
                var param = vals[translated];
                if(!param === undefined){

                }
                options = select[key];
                str += "<select name='"+key+"' class='chosen-select'>";
                ptr = eval(key+"sSet");
                mStr = "";
                if(!(param === undefined || param === null)){
                    mStr = String(param);
                }
                matched = null;
                if(ptr && !(param === undefined)){
                    matched = ptr.get(mStr);
                }
                if(!matched && this[key+"sKeySet"]){
                    ptr = eval(key+"sKeySet");
                    matched = ptr.get(mStr);
                }
                $.each(options, function(kx, vx){
                    str += "<option value='"+kx+"'";
                    if(matched && (matched[0][1] == vx || matched[0][1] == kx)){
                        str += " selected ";
                    }
                    str +=">"+vx+"</option>";
                });
                str += "</select>";
            }
            else{
                if(strings.indexOf(key) > -1)
                {
                    str += "<input value='' type='text' name='"+key+"[]'";
                    if(strings.indexOf(key) < 3){
                        str += " required ";
                    }
                    str += "/>";
                }
                else if(dates.indexOf(key) > -1){
                    var d = new Date();
                    var month = d.getMonth();
                    var day = d.getDate();
                    var output = d.getFullYear() + '-' +
                        (month<10 ? '0' : '') + month + '-' +
                        (day<10 ? '0' : '') + day;
                    str += "<input value='"+output+"' type='date' name='"+key+"[]'";
                    if(dates.indexOf(key) < 3){
                        str += " required ";
                    }
                    str += "/>";
                }
                else if(bools.indexOf(key) > -1)
                {
                    str += "<input type='checkbox' name='"+key+"[]'";
                    if(bools.indexOf(key) < 1){
                        str += " checked ";
                    }
                    str += "/>";
                }
                else{
                    str += "<input value='' type='text' name='"+key+"[]'/>";
                }
            }
            str += "</td>";
            if(hasData){
                row.append(str);
            }
        }
        if(!hasData){
            row.remove()
        }
        if (results.errors)
        {
            errorCount += results.errors.length;
            firstError = firstError || results.errors[0];
        }
    }
    $(".chosen-select").chosen();
}

function mapHeaders(results, parser) {
    if (results){
        if(results.data)
        {
            a = FuzzySet();
            for(var x in results.data[0]){
                a.add(x);
            }
            //var matches = a.get(results.data[0]);
            for(var key in expected){
                var sel = $("#ma_"+key+"_sel");
                var matchVal = a.get(expected[key]);
                sel.append("<option>N/A<option>");
                for(var match in results.data[0])
                {
                    if(match.trim() == "") continue;
                    var str = "<option value='"+match+"'";
                    if(matchVal && match == matchVal[0][1] && matchVal[0][0] > 0.5){
                        str += " selected "
                    }
                    str += ">"+match+"</option>";
                    sel.append(str);
                }
            }
        }
        $("#matchArea").show();
        enableButton();
        if(results.errors)
        {
            console.log(results.errors[0])
        }
    }
}

function completeFn(results){
    end = now();
    if (results && results.errors)
    {
        if (results.errors)
        {
            errorCount = results.errors.length;
            firstError = results.errors[0];
        }
        if (results.data && results.data.length > 0)
            rowCount = results.data.length;
    }

    printStats("Parse complete");
    console.log("    Results:", results);

    // icky hack
    setTimeout(enableButton, 100);
    $("#matchArea").show();
    enableStep()
}

function completeLoad(data, param){
    values[param] = data;
    ptr = this[param+"Set"];
    kptr = this[param+"Set"];
    $.each(data, function(key, value){
        ptr.add(value);
        if(!(kptr === undefined)){
            kptr.add(key);
        }
    });
    //alert(data);
    console.log("set "+param);

    // http://stackoverflow.com/a/23391100
    if(all([labels, genres, categorys, schedules, formats, locales], function(e){return (e()!=null);})){
        enableStep();
    }
    calls.complete += 1;
    updateProgress();
}

function errorFn(err, file){
    end = now();
    console.log("ERROR:", err, file);
    enableButton();
}

function enableButton()
{
    $('#loadHeaders').prop('disabled', false);
}
function enableStep()
{
    $('#process').prop('disabled', false);
}

function now()
{
    return typeof window.performance !== 'undefined'
        ? window.performance.now()
        : 0;
}

function getData(url, param, callbackFunction, data){
    data = data || null;
    return $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function(data){
            callbackFunction(data, param)
        },
        beforeSend: function(){
            calls.pending+=1;
        },
        data: data
    });
}

function getGenres(){
    return getData('./options/genre', "genres", completeLoad);
}

function getLabels(){
    return getData('../../label/', 'labels', completeLoad, {'full':true});
}

function getCategories(){ //Is actually government categories
    return getData('../parameters/governmentcodes', 'categorys', completeLoad);
}

function getFormats(){
    return getData('../parameters/formats', 'formats', completeLoad);
}

function getSchedules(){
    return getData('../parameters/scheduleblocks', 'schedules', completeLoad);
}

function getRegions(){
    return getData('../parameters/regions', 'locales', completeLoad);
}

function printStats(data){
    console.log(data);
};

function processRows(){
    stepped = 0;
    rowCount = 0;
    errorCount = 0;
    firstError = undefined;

    var config = standardConfig(0, stepFn);
    var input = $('#input').val();

    // Allow only one parse at a time
    $(this).prop('disabled', true);
    console.log("--------------------------------------------------");

    if (!$('#input')[0].files.length)
    {
        alert("Please choose at least one file to parse.");
        return enableStep();
    }

    $('#input').parse({
        config: config,
        before: function(file, inputElem)
        {
            start = now();
            console.log("Parsing file...", file);
        },
        error: function(err, file)
        {
            console.log("ERROR:", err, file);
            firstError = firstError || err;
            errorCount++;
        },
        complete: function()
        {
            end = now();
            printStats("Done with all files");
        }
    });
    //enableStep();
}

function standardConfig(preview, step) {
    return config = {
        delimiter: $('#delimiter').val(),
        header: true,
        dynamicTyping: true,
        skipEmptyLines: true,
        preview: preview,
        step: step,
        encoding: '',
        worker: true,
        comments: '',
        complete: completeFn,
        error: errorFn,
        download: false
    };

}

