/* 
 * The MIT License
 *
 * Copyright 2016 J.oliver.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

function toggle(source) {
    checkboxes = document.getElementsByName('bulkEditId[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
      checkboxes[i].checked = source.checked;
    }
  }

function getBatchOptions(){
    var batch = $.ajax("../batch/options")
        .done(function(data){
            if ( console && console.log ) {
                console.log( data );
            }
            $.each(data,function (key, value){
                $("#batchSelect")
                    .append($("<option></option>")
                    .attr("value", key)
                    .text(value));
            });
    });
}

function batchChange(element, url){
    console.log(element);
    var val = element.val();
    if(!val){
        alert (val);
        return true;
    }
    console.log(val);
    
    var batch = $.ajax({
        url:"../batch/options/"+val, 
        dataType: "json"
    })
    .done(function(data){
        if ( console && console.log ) {
            console.log( data );
        }
        if($.type(data) === "boolean"){
            $("#batchExtension").html("");
            return true;
        }
        if ( console && console.log ) {
            console.log($.type(data));
            console.log(data.inputType);
            console.log(data.values);
        }
        if(data.inputType==="select"){
            $("#batchExtension").html(
                "<select name='value' id='beval' required>"+
                "<option>Select</option></select>");
            $.each(data.values, function(key, value){
                $("#beval")
                    .append($("<option></option>")
                    .attr("value", key)
                    .text(value));
            });
        }
        else if(data.inputType==="attribute"){
            $("#batchExtension").html(
                "Attribute: <select name='value' id='beval' required>"+
                "<option>Select</option></select>"+
                "&nbsp;<span id='beattr'></span>");
            $.each(data.values, function(key, value){
                $("#beval")
                    .append($("<option></option>")
                    .attr("value", key)
                    .attr("class", value.input)
                    .text(value.value));
            });
            
        }
        changeAttrOptions("#beval option:selected", "#beattr");
        //$("#batchExtension").html("THIS IS A TEST");
    })
    .fail(function(data){
        if ( console && console.log ) {
            console.log( data );
        }
    });
    
}

function changeAttrOptions(divId, targetObj){
    console.log("ChangeAttr"+divId.selector);
    console.log(divId);
    console.log(targetObj);
    var divId=$(divId);
    var targetObj = $(targetObj);
    var classList = divId.attr('class');//.split(/\s+/);
    console.log(classList);
    if(divId.hasClass('text')){
        console.log("TEXT");
        targetObj.html("<input type='text' name='attribute' "+
        "placeholder='attribute value' required/>");
    }
    else if(divId.hasClass("select")){
        console.log("SELECT");
        var urlVal = "../batch/options/"+divId.val();
        var data3 = $.ajax({
            url: urlVal,
            dataType: "json"
        }).done(function (data2){
            targetObj.html("<select name='attribute' id='beattersel'"+
                    "required><option>Select</option></select>");
            console.log(data2);
            $.each(data2, function(key2, value2){
                $("#beattersel")
                    .append(
                    $("<option></option>")
                    .attr("value", key2)
                    .text(value2)
                    );
            });
        }).fail(function (jqXhr, status, exception){
            console.log(status);
        });
    }
}
 
$(function () {
    getBatchOptions();
    $(document).on("change", "#batchSelect", function() {
        batchChange($("#batchSelect option:selected"));
    });
    $(document).on("change", "#beval", function() {
        console.log("setting onchange for beval");
        changeAttrOptions("#beval option:selected", "#beattr");
    });
});
