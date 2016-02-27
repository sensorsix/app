<?php use_javascript('/libs/jquery-numeric/jquery.numeric.min.js?v=1.4.1.1') ?>
<div class="form-group">
  <div class="col-xs-8 col-xs-offset-1 well-large">
    <?php foreach ($collection as $item) : ?>
      <div class="accordion" id="accordion2">
        <div class="accordion-group">
          <div class="accordion-heading">
            <div class="form-group">
              <div class="col-xs-7">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapse-<?php echo $item->id ?>">
                  <?php echo $item->name ?>
                </a>
              </div>
              <div class="col-xs-5">
                <div class="form-line control-group<?php echo isset($errors[$item->id]) ? ' error' : '' ?>" style="margin: 3px 0 3px 0">
                  <?php if (isset($errors[$item->id])) : ?>
                    <ul class="alert alert-error controls input-xlarge" style="margin: 0 0 3px 0; width: 220px; clear: both;">
                      <li><?php echo $errors[$item->id] ?></li>
                    </ul>
                  <?php endif; ?>
                  <input style="width: auto" class="numeric input-xlarge right" type="text" name="measurement[<?php echo $item->id ?>]" value="<?php echo isset($values[$item->id]) ? $values[$item->id] : '' ?>"/>
                  <a class="accordion-toggle comment-icon right" data-parent="#accordion2" data-toggle="collapse" href="#collapse-<?php echo $item->id ?>">
                    <img src="<?php echo image_path('comment.png'); ?>" />
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div id="collapse-<?php echo $item->id ?>" class="accordion-body collapse<?php echo $item->getRawValue()->getTooltip() ? '' : ' in' ?>">
            <div class="accordion-inner">
              <div class="item-tooltip">
                <?php echo $item->getRawValue()->getTooltip() ?>
              </div>
              <?php include_partial('field_comment', array('id' => $item->id, 'comments' => isset($comments[$item->id]) ? $comments[$item->id] : array(), 'prioritization' => $prioritization)) ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach ?>
  </div>
</div>

<script type="text/javascript">
/*<![CDATA[*/
$(function () {
  $(".numeric").numeric({ decimal: "," });
});
/*]]>*/
</script>
<?php include_partial('comments_js', array('role_id' => $role_id)) ?>