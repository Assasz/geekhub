$(document).ready(function(){
  $('.main').css('min-height', $(window).innerHeight()+'px');

  $('.btn-reply').click(function(){
    var id = $(this).attr('reply-to');
    var replyForm = $('.comment-form').clone().addClass('reply');
    var form = replyForm.find('form');
    form.attr('action', form.attr('action')+'/'+id);

    if($('#'+id+' .replies').length){
      replyForm.appendTo('#'+id+' .replies');
    }
    else {
      replyForm.insertAfter('#'+id);
    }
  });
});
