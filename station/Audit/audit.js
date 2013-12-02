function EditOnly(){
    $("#form1 :input, #form1 select").attr('disabled', true);
}

function CreateOnly(){
    $("#form2 :input, #form2 select").attr('disabled', true);
}

$(document).ready(function () {
    $("#form1 input[type='reset']").on("click", function () {
        $("#form2 :input, #form2 select").attr('disabled', false);
    });
    $("#form2 input[type='reset']").on("click", function () {
        $("#form1 :input, #form1 select").attr('disabled', false);
    });
    $("#form1 :input, form1 select").on("change",function () {
        CreateOnly();
    });
});