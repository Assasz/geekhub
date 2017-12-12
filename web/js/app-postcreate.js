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

    var timeout, validate = function(){
        if(timeout){
            clearTimeout(timeout);
        }

        var field = $('#post_title'),
            error = $('<p id="title_form_error" class="form-error"></p>'),
            errorSelector = $('#title_form_error'),
            errorContent = 'This title is invalid. Length should be between 3 and 255 characters.';

        timeout = setTimeout(function(){
            if(field.val().length > 255 || field.val().length < 3){
                if(!errorSelector.length){
                    error.html(errorContent);
                    error.insertAfter(field);
                } else {
                    errorSelector.html(errorContent);
                }

                field.addClass('invalid');
            } else {
                if(errorSelector.length){
                    errorSelector.remove();
                }

                field.removeClass('invalid');
            }
        }, 1000);
    }

    $('#post_title').on('input propertychange', validate);
    $('#post_add_post').click(validate);

    $('[data-action="post-preview"]').click(function () {
        var editorText = CKEDITOR.instances.post_content.getData();

        $('#post-preview').modal();
        $('.modal-body').html(editorText);

        $('pre code').each(function(i, e){
            hljs.highlightBlock(e);
        });
    });
});
