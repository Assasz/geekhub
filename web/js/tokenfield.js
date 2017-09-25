$(document).ready(function(){
    var tags = [];

    $.ajax({
        url: Routing.generate('tag_list'),
        dataType: "json",
        async: false
    })
    .done(function( response ) {
        tags =  response.tags;
    });

    $('.tokenfield').tokenfield({
        autocomplete: {
            source: tags,
            delay: 100
        },
        limit: 5,
        delimiter: ' '
    });
});
