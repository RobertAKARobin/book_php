function submit(form,button){
    var action = $(form).prop("action"),
        method = $(form).prop("method"),
        input = $(form).serialize(),
        output;
    $(button).prop("disabled","true");
    $.ajax(
        {
            type: method,
            url: action,
            data: input
        }
    ).done(function(output){
        output = JSON.parse(output);
        if(output[0].toLowerCase() == "error"){
            $(button).prop("disabled","");
        }
        $(form).find(".result").text(output[1]);
    });
}

$(document).ready(function(){

$(".match ul+ul, .match ol+ol").each(function(){
    var children = $(this).children();
    for (x = children.length; x >= 0; x--) {
        $(this).append(children[Math.random() * x | 0]);
    }
});

$("form").each(function(){
    var form = $(this);
    $(form).append("<p class=\"result\"></p>"
    ).find("*[type=submit]"
    ).prop("type","button"
    ).addClass("submit"
    ).click(function(){
        submit(form,$(this));
    });
});

});