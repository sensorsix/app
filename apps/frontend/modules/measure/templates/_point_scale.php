<?php use_javascript('/libs/jquery-rating/jquery.rating.js') ?>
<?php use_stylesheet('/libs/jquery-rating/jquery.rating.css') ?>

<div class="row">
  <div class="col-xs-12 col-md-8 col-md-offset-2">
    <div class="panel-group" id="accordion">
    <?php foreach ($collection as $item) : ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <div class="row">
            <div class="col-xs-5 col-sm-8">
              <h4 class="panel-title">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $item->id ?>">
                  <?php echo $item->name ?>
                </a>
              </h4>
            </div>
            <div class="col-xs-7 col-sm-4">
              <div class="right">
                <?php include_partial('stars', array('id' => $item->id, 'values' => $values, 'stars' => $stars)) ?>
              </div>
              <a class="accordion-toggle comment-icon right" data-parent="#accordion" data-toggle="collapse" href="#collapse-<?php echo $item->id ?>">
                <img src="<?php echo image_path('comment.png'); ?>" />
              </a>
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

<?php include_partial('comments_js', array('role_id' => $role_id)) ?>