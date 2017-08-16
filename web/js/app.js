$(document).ready(function(){
  $('.btn-reply').click(function(){
    if($('#reply-panel').length){
      $('#reply-panel').remove();
    }

    var id = $(this).attr('comment-id'),
      author = $(this).attr('comment-author'),
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

  $('.btn-vote').click(function(){
    var id = $(this).attr('comment-id');

    $.ajax({
      url: Routing.generate('comment_vote', {comment: id}),
      dataType: "json",
    })
    .done(function( data ) {
      $('#votes-comment-'+id).html(data.votes);
      $('.vote-button[comment-id='+id+']').remove();
    });
  });

  $(document).on('submit', '.comment-form', function(event){
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

          var commentsNumber = parseFloat($('#comments-number').html());
          $('#comments-number').html(commentsNumber+1);
        }
        else {
          $(response.comment).insertBefore('#comment-'+response.parent+' #reply-panel');
        }
    });
  });

  $('.btn-show-replies').click(function(){
    var id = $(this).attr('comment-id');

    $.ajax({
      url: Routing.generate('comment_list_replies', {parent: id, show: 1}),
      dataType: "json",
    })
    .done(function( response ) {
      $('#comment-'+id+' .replies').replaceWith(response.replies);
    });
  });
});
