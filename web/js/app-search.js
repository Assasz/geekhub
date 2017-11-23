$(document).ready(function(){
    $(document).on('click', '[data-action="search"]', function(e){
        e.preventDefault();
        var type = $(this).data('type'),
            sort = $(this).data('sort');

        $.ajax({
            url: Routing.generate('search', {
                type: type,
                sort: sort
            }),
            dataType: "json",
        })
        .done(function(response) {
            $('.search-content').replaceWith($(response.view));
        });
    });
})
