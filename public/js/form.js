'use strict';
function init(){
    $('#order-form').submit(function(e) {
        e.preventDefault();
        var data = $(this).serialize(),
            method = $(this).attr('method'),
            action = $(this).attr('action');
        $.ajax({
            type: method,
            url: action,
            data: data
        }).done(function(ms) {
            console.log(ms);
            if(ms === 'ok') {
                $('body .overlay').css('display', 'flex');
                $('body .overlay .btn').click(function(){
                    $('body .overlay').css('display', 'none');
                });
            }
        }).fail(function(er) {
        console.log(er)});
        });
}
window.onload = init;