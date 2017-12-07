$(document).ready(function(){
    if(typeof user !== 'undefined'){
        var source = new EventSource(
            Routing.generate('notification_push', {
                user: user
            })
        ),
            notificationsNumber = 0,
            initialTitle = document.title;

        if ($('.label-notifications').length) {
            notificationsNumber = parseFloat($('.label-notifications').html());

            document.title = '(' + notificationsNumber + ') ' + document.title;
        }

        source.onmessage = function (e) {
            var response = $.parseJSON(e.data);

            $('.notifications .dropdown-menu').prepend($(response));

            if(!$('.notifications').hasClass('show')){
                if (!$('.label-notifications').length) {
                    $('.notifications .dropdown-toggle').append($('<span class="label label-notifications">0</span>'));
                }

                notificationsNumber++;

                $('.label-notifications').html(notificationsNumber);
                document.title = '(' + notificationsNumber + ') ' + document.title;
            }
        };

        $('.notifications').click(function(){
            $('.label-notifications').remove();
            document.title = initialTitle;

            $.ajax({
                url: Routing.generate('notification_disactivate', {
                    user: user
                }),
                dataType: "json",
            });
        });
    }
});
