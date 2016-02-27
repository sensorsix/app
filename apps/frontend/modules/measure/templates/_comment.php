<div class="row">
  <div class="col-xs-12 col-md-8 col-md-offset-2">
    <div class="panel-group" id="accordion">
      <?php foreach ($collection as $item) : ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="row">
            <div class="col-xs-5 col-sm-7">
              <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $item->id ?>">
                  <?php echo $item->name ?>
                </a>
              </h4>
            </div>
            <div class="col-xs-7 col-sm-5">
              <div class="form-line control-group<?php echo isset($errors[$item->id]) ? ' error' : '' ?>" style="margin: 3px 0 3px 0">
                <?php if (isset($errors[$item->id])) : ?>
                <ul class="alert alert-error controls input-xlarge" style="margin: 0 0 3px 0; width: 220px; clear: both;">
                  <li><?php echo $errors[$item->id] ?></li>
                </ul>
                <?php endif; ?>
                <textarea rows="3" style="max-width: 93%" name="measurement[<?php echo $item->id ?>]"><?php echo isset($values[$item->id]) ? $values[$item->id] : '' ?></textarea>
                <a style="margin-top: 7px" class="accordion-toggle comment-icon right" data-parent="#accordion2" data-toggle="collapse" href="#collapse-<?php echo $item->id ?>">
                  <img src="<?php echo image_path('comment.png'); ?>" />
                </a>
              </div>
            </div>
          </div>
        </div>
        <div id="collapse-<?php echo $item->id ?>" class="panel-collapse collapse<?php echo $item->getRawValue()->getTooltip() ? '' : ' in' ?>">
          <div class="panel-body">
            <div class="item-tooltip">
              <?php echo $item->getRawValue()->getTooltip() ?>
            </div>
            <?php include_partial('field_comment', array('id' => $item->id, 'comments' => isset($comments[$item->id]) ? $comments[$item->id] : array(), 'prioritization' => $prioritization)) ?>
          </div>
        </div>
      </div>
      <?php endforeach ?>
    </div>
  </div>
</div>
<script type="text/javascript">
/*<![CDATA[*/
$(function () {
  $('.collapse').collapse();
});
/*]]>*/
</script>
<?php include_partial('comments_js', array('role_id' => $role_id)) ?>