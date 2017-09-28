$(document).ready(function(){
    var validation = {
        email: false,
        username: false,
        forename: false,
        surname: false,
        password: false
    }, timeout;

    function isSuccessful(obj){
        for(var child in obj)
            if(!obj[child]){
                return false;
            }

        return true;
    }

    $('#fos_user_registration_form_email').on('input propertychange', function(){
        if(timeout){
            clearTimeout(timeout);
        }

        timeout = setTimeout(function(){
            var field = $('#fos_user_registration_form_email'),
                error = $('<p id="email_form_error" class="form-error"></p>');

            function isEmail(email) {
                var regex = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);

                return regex.test(email);
            }

            if(isEmail(field.val())){
                $.ajax({
                  url: Routing.generate('signup_check_email_ajax'),
                  dataType: "json",
                  data: {email: field.val()},
                  type: 'POST',
                  async: false,
                })
                .done(function( response ) {
                  if(!response.valid){
                      var errorContent = 'This e-mail address is already in use.';

                      if(!$('#email_form_error').length){
                          error.html(errorContent);
                          error.insertAfter('#fos_user_registration_form_email');
                      } else {
                          $('#email_form_error').html(errorContent);
                      }

                      field.addClass('invalid').removeClass('valid');
                      validation.email = false;
                  }
                  else {
                      if($('#email_form_error').length){
                          $('#email_form_error').remove();
                      }

                      field.addClass('valid').removeClass('invalid');
                      validation.email = true;
                  }
                });
            } else {
                var errorContent = 'This e-mail address is invalid.';

                if(!$('#email_form_error').length){
                    error.html(errorContent);
                    error.insertAfter('#fos_user_registration_form_email');
                } else {
                    $('#email_form_error').html(errorContent);
                }

                field.addClass('invalid').removeClass('valid');
                validation.email = false;
            }
        }, 1000);
    });

    $('#fos_user_registration_form_username').on('input propertychange', function(){
        if(timeout){
            clearTimeout(timeout);
        }

        timeout = setTimeout(function(){
            var field = $('#fos_user_registration_form_username'),
                error = $('<p id="username_form_error" class="form-error"></p>');

            if(field.val().length > 180 || field.val().length < 2){
                var errorContent = 'This username is invalid. Length should be between 2 and 180 characters.';

                if(!$('#username_form_error').length){
                    error.html(errorContent);
                    error.insertAfter('#fos_user_registration_form_username');
                } else {
                    $('#username_form_error').html(errorContent);
                }

                field.addClass('invalid').removeClass('valid');
                validation.username = false;
            } else {
                if($('#username_form_error').length){
                    $('#username_form_error').remove();
                }

                field.addClass('valid').removeClass('invalid');
                validation.username = true;
            }
        }, 1000);
    });

    $('#fos_user_registration_form_plainPassword_first, #fos_user_registration_form_plainPassword_second').on('input propertychange', function(){
        if(timeout){
            clearTimeout(timeout);
        }

        timeout = setTimeout(function(){
            var error = $('<p id="password_form_error" class="form-error"></p>'),
                firstPassword = $('#fos_user_registration_form_plainPassword_first'),
                secondPassword = $('#fos_user_registration_form_plainPassword_second');

            if(firstPassword.val().length > 4096 || firstPassword.val().length < 2){
                var errorContent = 'This password is invalid. Length should be between 2 and 4096 characters.';

                if(!$('#password_form_error').length){
                    error.html(errorContent);
                    error.insertAfter('#fos_user_registration_form_plainPassword_first');
                } else {
                    $('#password_form_error').html(errorContent);
                }

                if(secondPassword.hasClass('valid')){
                    secondPassword.addClass('invalid').removeClass('valid');
                }

                firstPassword.addClass('invalid').removeClass('valid');
                validation.password = false;
            } else if (firstPassword.val() != secondPassword.val() && secondPassword.val().length > 0) {
                var errorContent = 'These passwords don\'t match.';

                if(!$('#password_form_error').length){
                    error.html(errorContent);
                    error.insertAfter('#fos_user_registration_form_plainPassword_first');
                } else {
                    $('#password_form_error').html(errorContent);
                }

                secondPassword.addClass('invalid').removeClass('valid');
                validation.password = false;
            } else if (firstPassword.val() != secondPassword.val() && secondPassword.val().length <= 0 && firstPassword.val().length <= 4096 && firstPassword.val().length >= 2) {
                if($('#password_form_error').length){
                    $('#password_form_error').remove();
                }

                firstPassword.addClass('valid').removeClass('invalid');
                validation.password = false;
            } else {
                if($('#password_form_error').length){
                    $('#password_form_error').remove();
                }

                firstPassword.addClass('valid').removeClass('invalid');
                secondPassword.addClass('valid').removeClass('invalid');
                validation.password = true;
            }
        }, 1000);
    });

    $('#fos_user_registration_form_forename').on('input propertychange', function(){
        if(timeout){
            clearTimeout(timeout);
        }

        timeout = setTimeout(function(){
            var field = $('#fos_user_registration_form_forename'),
                error = $('<p id="forename_form_error" class="form-error"></p>');

            function hasOnlyLetters(string) {
                var regex = new RegExp(/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/u);

                return regex.test(string);
            }

            if(!hasOnlyLetters(field.val()) || field.val().length > 180 || field.val().length < 2){
                var errorContent = 'This forename is invalid. Length should be between 2 and 180 characters. No special characters and numbers.';

                if(!$('#forename_form_error').length){
                    error.html(errorContent);
                    error.insertAfter('#fos_user_registration_form_forename');
                } else {
                    $('#forename_form_error').html(errorContent);
                }

                field.addClass('invalid').removeClass('valid');
                validation.forename = false;
            } else {
                if($('#forename_form_error').length){
                    $('#forename_form_error').remove();
                }

                field.addClass('valid').removeClass('invalid');
                validation.forename = true;
            }
        }, 1000);
    });

    $('#fos_user_registration_form_surname').on('input propertychange', function(){
        if(timeout){
            clearTimeout(timeout);
        }

        timeout = setTimeout(function(){
            var field = $('#fos_user_registration_form_surname'),
                error = $('<p id="surname_form_error" class="form-error"></p>');

            function hasOnlyLetters(string) {
                var regex = new RegExp(/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/u);

                return regex.test(string);
            }

            if(!hasOnlyLetters(field.val()) || field.val().length > 180 || field.val().length < 2){
                var errorContent = 'This surname is invalid. Length should be between 2 and 180 characters. No special characters and numbers.';

                if(!$('#surname_form_error').length){
                    error.html(errorContent);
                    error.insertAfter('#fos_user_registration_form_surname');
                } else {
                    $('#surname_form_error').html(errorContent);
                }

                field.addClass('invalid').removeClass('valid');
                validation.surname = false;
            } else {
                if($('#surname_form_error').length){
                    $('#surname_form_error').remove();
                }

                field.addClass('valid').removeClass('invalid');
                validation.surname = true;
            }
        }, 1000);
    });

    setInterval( function(){
        if(isSuccessful(validation)){
            $('input[type="submit"]').prop('disabled', false);
        } else {
            $('input[type="submit"]').prop('disabled', true);
        }
    }, 100);
});
