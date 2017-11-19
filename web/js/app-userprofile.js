$(document).ready(function(){
    $('[data-action="follow"]').click(function(){
        var id = $(this).data('user-id'),
            button = $(this);

        $.ajax({
            url: Routing.generate('user_follow', {
                user: id
            }),
            dataType: "json",
        })
        .done(function(response) {
            var followers  = parseFloat($('[data-followers-counter]').html()) + 1;

            $('[data-followers-counter]').html(followers);
            button.addClass('btn-clicked').html(
                '<span class="fa fa-fw fa-check" aria-hidden="true"></span>'+
                'Followed'
            ).prop('disabled', true);
        });
    });

    $('[data-action="toggle-about"]').click(function(e){
        e.preventDefault();
        $('.about-full').slideToggle("slow");
    });
});
