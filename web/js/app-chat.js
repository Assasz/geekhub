$(document).ready(function(){
    var conn = new WebSocket('ws://localhost:8888');

    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        var response = JSON.parse(e.data);

        $('#chat').append(response.user+': '+response.msg);
    };

    $('[data-action="send-msg"]').click(function(){
        var msg = {
            "body": $('#chat_message').val(),
            "user": $(this).data('user')
        };

        conn.send(JSON.stringify(msg));
    });
});
