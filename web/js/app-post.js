$(document).ready(function() {
    $(document).on('click', '[data-action="comment-reply"]', function(event) {
        event.preventDefault();

        if ($('.reply-panel').length) {
            $('.reply-panel').remove();
        }

        var id = $(this).data('comment-id'),
            author = $(this).data('comment-author'),
            panel = $('.comment-panel').clone().attr('class', 'reply-panel'),
            form = panel.find('form'),
            textarea = form.find('textarea');

        form.attr('action', form.attr('action') + '/' + id);
        textarea.attr('autofocus', 'autofocus');
        textarea.val('@' + author + ' ');
        form.find('button').html('Add reply');

        if ($('#replies-' + id).length) {
            if(!$('#replies-' + id).is(':visible')){
                var btn = $('[data-action="show-comment-replies"][data-comment-id="' + id + '"]');

                btn.html('Hide replies');
                btn.attr('data-action', 'hide-comment-replies');
                $('#replies-' + id).show();
            }

            panel.insertAfter('#replies-' + id);
        } else {
            panel.insertAfter('#comment-' + id);
        }

        $('html, body').animate({
            scrollTop: $(".reply-panel").offset().top - $(".reply-panel").outerHeight(true)
        }, 1000);
    });

    $(document).on('click', '[data-action="comment-vote"]', function() {
        var id = $(this).data('comment-id'),
            counter = $('[data-comment-votes][data-comment-id=' + id + ']');

        $.ajax({
                url: Routing.generate('comment_vote', {
                    comment: id
                }),
                dataType: "json",
            })
            .done(function(data) {
                counter.html(data.votes);
                counter.removeClass('hidden');
                $('[data-action="comment-vote"][data-comment-id=' + id + ']').addClass('clicked');
            });
    });

    $(document).on('submit', '[data-action="comment-post"]', function(event) {
        event.preventDefault();
        var data = $(this).serialize();

        $.ajax({
                url: $(this).attr('action'),
                dataType: "json",
                data: data,
                type: $(this).attr('method'),
            })
            .done(function(response) {
                if (response.parent == null) {
                    $(response.comment).insertAfter('.comment-group-header');

                    var commentsNumber = parseFloat($('[data-comments-number]').html());
                    $('[data-comments-number]').html(commentsNumber + 1);
                } else {
                    if(!$('#replies-'+response.parent).length){
                        $('<div id="replies-' + response.parent + '" class="replies"></div>').insertAfter('#comment-' + response.parent);
                    }

                    $(response.comment).appendTo($('#replies-' + response.parent));
                }
            });
    });

    $(document).on('input propertychange', '[data-action="comment-post"] textarea', function() {
        var button = $('[data-action="comment-post"] button[type="submit"]');

        if (!$(this).val()) {
            button.prop('disabled', true);
        } else {
            button.prop('disabled', false);
        }
    });

    $(document).on('click', '[data-action="show-comment-replies"]', function(event, id) {
        event.preventDefault();
        var id = id || $(this).data('comment-id'),
            btn = $('[data-action="show-comment-replies"][data-comment-id="' + id + '"]');

        btn.html('Hide replies');
        btn.attr('data-action', 'hide-comment-replies');

        if(!$('#replies-'+id).length){
            $.ajax({
                url: Routing.generate('comment_list_replies', {
                    parent: id,
                    show: 1
                }),
                dataType: "json",
            })
            .done(function(response) {
                if ($('#replies-' + id).length) {
                    $('#replies-' + id + ' .comment-container').replaceWith($(response.replies));
                } else {
                    $('<div id="replies-' + id + '" class="replies"></div>').insertAfter('#comment-' + id);
                    $(response.replies).appendTo('#replies-' + id);
                }
            });
        } else {
            $('#replies-'+id).show();
        }
    });

    $(document).on('click', '[data-action="hide-comment-replies"]', function(event) {
        event.preventDefault();
        var id = $(this).data('comment-id'),
            repliesNumber = $('#replies-' + id + ' .comment-container').length,
            btn = $('[data-action="hide-comment-replies"][data-comment-id="' + id + '"]');

        $('#replies-' + id).hide();
        btn.html('Show replies (' + repliesNumber + ')');
        btn.attr('data-action', 'show-comment-replies');

        if($('.reply-panel').length){
            $('.reply-panel').remove();
        }
    });

    $('[data-action="post-like"]').click(function() {
        var id = $(this).data('post-id');

        $.ajax({
                url: Routing.generate('post_like', {
                    post: id
                }),
                dataType: "json",
            })
            .done(function(response) {
                $('[data-post-likes]').html(response.likes);
                $('.btn-rate').prop('disabled', true);
            });
    });

    $('[data-action="post-dislike"]').click(function() {
        var id = $(this).data('post-id');

        $.ajax({
                url: Routing.generate('post_dislike', {
                    post: id
                }),
                dataType: "json",
            })
            .done(function(response) {
                $('[data-post-dislikes]').html(response.dislikes);
                $('.btn-rate').prop('disabled', true);
            });
    });

    $('pre code').each(function(i, e){
        hljs.highlightBlock(e);
    });

    $(document).on('click', 'a[href^="#"]', function(event) {
        window.setTimeout(function() {
        offsetAnchor();
        }, 0);
    });

    window.setTimeout(offsetAnchor, 0);

    function offsetAnchor() {
        if(location.hash.length !== 0) {
            var id = window.location.hash.substring(9);

            window.scrollTo(window.scrollX, window.scrollY - 70);
        }
    }
});
