$(document).ready(function(){
  $(document).on('click', '[data-action="comment-reply"]', function(){
    if($('#reply-panel').length){
      $('#reply-panel').remove();
    }

    var id = $(this).data('comment-id'),
      author = $(this).data('comment-author'),
      panel = $('#comment-panel').clone().attr('id', 'reply-panel'),
      form = panel.find('form'),
      textarea = form.find('textarea');

    form.attr('action', form.attr('action')+'/'+id);
    textarea.attr('autofocus', 'autofocus');
    textarea.val('@'+author+' ');
    form.find('button').html('Add reply');

    if($('#comment-'+id+' .replies').length){
      panel.appendTo('#comment-'+id+' .replies');
    }
    else {
      panel.insertAfter('#comment-'+id);
    }

    $('html, body').animate({
        scrollTop: $("#reply-panel").offset().top-$("#reply-panel").outerHeight(true)
    }, 1000);
  });

  $(document).on('click', '[data-action="comment-vote"]', function(){
    var id = $(this).data('comment-id');

    $.ajax({
      url: Routing.generate('comment_vote', {comment: id}),
      dataType: "json",
    })
    .done(function( data ) {
      $('[data-comment-votes-indicator][data-comment-id='+id+']').html(data.votes);
      $('[data-action="comment-vote"][data-comment-id='+id+']').remove();
    });
  });

  $(document).on('submit', '[data-action="comment-post"]', function(event){
    event.preventDefault();
    var data = $(this).serialize();

    $.ajax({
      url: $(this).attr('action'),
      dataType: "json",
      data: data,
      type: $(this).attr('method'),
    })
    .done(function( response ) {
        if(response.parent == null) {
          $(response.comment).insertAfter('.comments li:eq(0)');

          var commentsNumber = parseFloat($('[data-comments-number-indicator]').html());
          $('[data-comments-number-indicator]').html(commentsNumber+1);
        }
        else {
          $('<ul class="replies"></ul>').appendTo('#comment-'+response.parent);
          $(response.comment).appendTo('#comment-'+response.parent+' .replies');
        }
    });
  });

  $(document).on('input propertychange', '[data-action="comment-post"] textarea', function(){
      var button = $('[data-action="comment-post"] button[type="submit"]');

      if(!$(this).val()){
          button.prop('disabled', true);
      } else {
          button.prop('disabled', false);
      }
  });

  $(document).on('click', '[data-action="show-comment-replies"]', function(){
    var id = $(this).data('comment-id');

    $.ajax({
      url: Routing.generate('comment_list_replies', {parent: id, show: 1}),
      dataType: "json",
    })
    .done(function( response ) {
      $('#comment-'+id+' .replies').replaceWith(response.replies);
    });
  });

  $('[data-action="post-like"]').click(function(){
      var id = $(this).data('post-id');

      $.ajax({
        url: Routing.generate('post_like', {post: id}),
        dataType: "json",
      })
      .done(function( response ) {
        $('[data-post-dislikes-indicator]').html(response.likes);
        $('.btn-rate').prop('disabled', true);
      });
  });

  $('[data-action="post-dislike"]').click(function(){
      var id = $(this).data('post-id');

      $.ajax({
        url: Routing.generate('post_dislike', {post: id}),
        dataType: "json",
      })
      .done(function( response ) {
        $('[data-post-dislikes-indicator]').html(response.dislikes);
        $('.btn-rate').prop('disabled', true);
      });
  });
});
