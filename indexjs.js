$(document).ready(function(){

$(".match ul+ul, .match ol+ol").each(function(){
    var children = $(this).children();
    for (x = children.length; x >= 0; x--) {
        $(this).append(children[Math.random() * x | 0]);
    }
});

});