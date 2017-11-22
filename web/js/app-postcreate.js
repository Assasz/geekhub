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
            source: function(request, response) {
                var results = $.ui.autocomplete.filter(tags, request.term);

                response(results.slice(0, 10));
            },
            delay: 100
        },
        limit: 5,
        delimiter: ' '
    });

    $("#post_image").fileinput({
        showUpload: false,
        required: true,
        allowedFileTypes: ['image'],
        minImageHeight: 400,
        minImageWidth: 1200,
        maxFileSize: 4096,
        maxFileCount: 1,
        msgPlaceholder: 'Select image (min. 1200x400)...',
        msgValidationErrorClass: 'form-error',
        msgErrorClass: 'form-error'
    });
});
