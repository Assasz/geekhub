$(document).ready(function(){
    var conn = new WebSocket('ws://localhost:8888');

    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        var response = JSON.parse(e.data);

        $('.chat-container').append(response.user+': '+response.msg);
    };

    $('button[data-action="send-msg"]').click(function(){
        var body = $('.chat-control').val(),
            user = $('.chat-control').data('user');

        if(body.length > 0){
            sendMessage(body, user);
        }
    });

    $('textarea[data-action="send-msg"]').keypress(function(e){
        var body = $(this).val(),
            user = $(this).data('user');

        if(e.keyCode == 13 && body.length > 0){
            e.preventDefault();
            $(this).val('');
            sendMessage(body, user);
        }
    });

    $('[data-action="toggle-chat"]').click(function(){
        $('#chat').toggleClass('toggled');
    });

    function sendMessage(body, user){
        var msg = {
            "body": body,
            "user": user
        };

        conn.send(JSON.stringify(msg));
    }
});
