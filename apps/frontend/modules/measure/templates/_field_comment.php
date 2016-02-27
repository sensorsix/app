<?php $comment_number = $comments->count() - 2; ?>
<div class="comments"<?php echo isset($visible) ? '' : 'style="display: none"'; ?>>
  <?php if ($comment_number > 0) : ?>
    <a class="view-more-comments" data-id="<?php echo $id ?>" href="javascript:void(0)">
      <?php echo format_number_choice('[1] View one more comment|(1,+Inf] View %1% more comments', array('%1%' => $comment_number), $comment_number, 'sf_admin') ?>
    </a>
  <?php endif; ?>
  <ul class="comments-list" id="comments-list-<?php echo $id ?>">
    <?php foreach ($comments as $i => $comment) : ?>
    <li class="comment-<?php echo $id ?>" style="display: none;">
      <strong><?php echo $comment->getRawValue()->relatedExists('User') ? $comment->User->email_address : $comment->email  ?></strong>
       - <?php echo $comment->getRawValue()->text ?>
    </li>
    <?php endforeach ?>
  </ul>
  <div class="comment-box">
    <div class="row-fuild">
      <textarea class="form-control" id="comment-box-<?php echo $id ?>" rows="3"></textarea>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <a class="btn btn-success send-comment" data-id="<?php echo $id ?>" data-prioritization="<?php echo $prioritization ?>" href="javascript:void(0)">Ok</a>
      </div>
    </div>
  </div>
</div>
