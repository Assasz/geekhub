$(document).ready(function(){
  $('.main').css('min-height', $(window).innerHeight()+'px');

  $('.btn-reply').click(function(){
    var id = $(this).attr('comment-id');
    var replyForm = $('.comment-form').clone().addClass('reply');
    var form = replyForm.find('form');
    form.attr('action', form.attr('action')+'/'+id);

    if($('#comment-'+id+' .replies').length){
      replyForm.appendTo('#comment-'+id+' .replies');
    }
    else {
      replyForm.insertAfter('#comment-'+id);
    }
  });

  $('.vote-button').click(function(){
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
});
