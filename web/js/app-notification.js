$(document).ready(function(){
    if(typeof user !== 'undefined'){
        var source = new EventSource(
            Routing.generate('notification_push', {
                user: user
            })
        );

        source.onmessage = function (e) {
            var response = $.parseJSON(e.data);

            $('.notifications').prepend($(response));
        };
    }
});
