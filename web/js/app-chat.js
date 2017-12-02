$(document).ready(function(){
    var conn = new WebSocket('ws://localhost:8888');

    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        var response = JSON.parse(e.data),
            message = $(
                '<div class="chat-message">'+
                    '<img src="'+response.profilePicture+'" class="message-author-img img-circle" alt="'+response.username+'">'+
                    '<div class="message-body">'+
                        '<p>'+response.body+'</p>'+
                        '<div class="message-caption">'+
                            '<span>'+response.username+'</span>'+
                            '<time>'+response.date+'</time>'+
                        '</div>'+
                    '</div>'+
                '</div>'
            );

        if($('.chat-control').data('user') == response.userID){
            message.addClass('user-message');
        }

        $('.chat-container').append(message);
        $('.chat-container').scrollTop($('.chat-container')[0].scrollHeight);
    };

    $('button[data-action="send-msg"]').click(function(){
        var chatControl = $('.chat-control');
            body = chatControl.val(),
            user = chatControl.data('user');

        if(body.length > 0){
            chatControl.val('');
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
        $('.chat-control').focus();
    });

    function sendMessage(body, user){
        var msg = {
            "body": body,
            "user": user
        };

        conn.send(JSON.stringify(msg));
    }
});
