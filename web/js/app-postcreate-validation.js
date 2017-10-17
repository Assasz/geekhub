$(document).ready(function(){
    var timeout;

    $('#post_title').on('input propertychange', function(){
        if(timeout){
            clearTimeout(timeout);
        }

        var field = $(this),
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
    });
});
