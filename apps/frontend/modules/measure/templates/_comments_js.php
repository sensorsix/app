<?php $email = $sf_user->isAuthenticated() ? $sf_user->getGuardUser()->email_address : $sf_user->getAttribute('email_address', null, 'measurement/email/' . $role_id); ?>
<script type="text/javascript">
$(function() {
  $('.collapse').collapse();

  $('.view-more-comments').on('click', function() {
    $('.comment-' + $(this).data('id')).show();
    $(this).hide();
  });

  $('.comments-list').each(function () {
    $('li:last-child', this).prev('li').andSelf().show();
  });

  $('.accordion-toggle').on('click', function (e) {
    var stop = false, $content = $(this.hash);

    if ($(this.hash).hasClass('in')) {
      stop = ($content.find('.item-tooltip:visible').length && $(this).hasClass('comment-icon'))
        || ($content.find('.comments:visible').length && !$(this).hasClass('comment-icon'));
    }

    if ($(this).hasClass('comment-icon')) {
      //$content.find('.item-tooltip').hide();
      $content.find('.comments').toggle();
      $content.find('.comment-box').show();
    }

    if (stop) {
      e.stopPropagation();
      return false;
    }
  });

  $('.send-comment').on('click', function() {
    var id = $(this).data('id'),
      $comment_box = $('#comment-box-' + id),
      $comments_list = $('#comments-list-' + id),
      text = $comment_box.val().replace( /\n/g, "<br />" );

    if ($comment_box.val().length) {
      var params = {
        id: id,
        prioritization: $(this).data('prioritization'),
        text: text
      };
      var template = '<li><strong><?php echo $email ?></strong>#comment_text</li>';

      $comments_list.append(template.replace('#comment_text', text));
      $comment_box.val('');
      $comment_box.closest('.comment-box').fadeOut(500);

      $.post('<?php echo url_for('measure\commentSave') ?>', params);
    }
  });
});
</script>

 