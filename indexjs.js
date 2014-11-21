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

$("#colors ol:nth-of-type(1) li").each(function(){
    var hex = $(this).prop("title"),
        rgb = hex.match(/(.{1,2})/g),
        txt = "fff",
        color,
        bg;
    for(var x in rgb){ rgb[x] = parseInt(rgb[x], 16); }
    if(
        rgb[0] > 255
      ||rgb[1] > 130
      ||rgb[2] > 255
      ){
        txt = "000";
    }
    $(this).css("background-color", "#"+hex);
    $(this).find("span").css("color", "#"+txt);
});

});