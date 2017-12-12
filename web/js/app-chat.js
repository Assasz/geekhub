$(document).ready(function(){
    var loadResults = true,
        offset = 20,
        container = $('.chat-container'),
        newMessages = 0,
        conn = new WebSocket('ws://localhost:8888');;

    conn.onopen = function(e) {
        console.log("Connection with chat established!");
    };

    conn.onmessage = function(e) {
        var response = JSON.parse(e.data),
            message = $(
                '<div class="chat-message">'+
                    '<img src="'+assetDir+response.profilePicture+'" class="message-author-img img-circle" alt="'+response.username+'">'+
                    '<div class="message-body">'+
                        '<p>'+response.body+'</p>'+
                        '<div class="message-caption">'+
                            '<span>'+response.username+'</span>'+
                            '<time>'+response.date+'</time>'+
                        '</div>'+
                    '</div>'+
                '</div>'
            );

        if(user == response.userID){
            message.addClass('user-message');
        }

        if(!container.hasClass('toggled')){
            newMessages++;
        } else {
            newMessages = 0;
        }

        $('.label-chat').html(newMessages).css('background-color', '#2772DD');

        container.append(message);
        container.scrollTop(container[0].scrollHeight);
    };

    $('button[data-action="send-msg"]').click(function(){
        var chatControl = $('.chat-control');
            body = chatControl.val();

        if(body.length > 0){
            chatControl.val('');
            sendMessage(body, user);
        }
    });

    $('textarea[data-action="send-msg"]').keypress(function(e){
        var body = $(this).val();

        if(e.keyCode == 13){
            e.preventDefault();

            if(body.length > 0){
                $(this).val('');
                sendMessage(body, user);
            }
        }
    });

    $('[data-action="toggle-chat"]').click(function(){
        $('#chat').toggleClass('toggled');

        if($('#chat').hasClass('toggled')){
            $('.chat-control').focus();
            container.scrollTop(container[0].scrollHeight);
        }

        newMessages = 0;

        $('.newmessages').html('0').css('background-color', '#383B9F');
    });

    $('.chat-container').scroll(function(){
        var scrollPosition = container[0].scrollHeight + container.height();

        if(loadResults){
            if($(this).scrollTop() == 0){

                $.ajax({
                    url: Routing.generate('chatmessage_load', {
                        offset: offset
                    }),
                    dataType: "json",
                })
                .done(function(response) {
                    container.prepend(response.newContent);
                    offset += response.resultsNumber;

                    if(response.resultsNumber < 1){
                        loadResults = false;
                    }
                })
                .then(function(){
                    var newScrollPosition = container[0].scrollHeight + container.height();

                    container.scrollTop(newScrollPosition - scrollPosition);
                });
            }
        }
    });

    var loader = $('<img src="'+assetDir+'images/loader.gif" class="loader" alt="Loading">');

    $(document)
        .ajaxStart(function () {
            container.prepend(loader);
        })
        .ajaxStop(function () {
            loader.remove();
        });

    function sendMessage(body, user){
        var msg = {
            "body": body,
            "user": user
        };

        conn.send(JSON.stringify(msg));
    }
});
