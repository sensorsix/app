<?php
/**
 * @var Decision $decision
 * @var sfWebResponse $sf_response
 * @var Doctrine_Collection|WallPost[] $posts
 * @var sfUser $sf_user
 * @var criteriaAnalyze $criteriaAnalyze
 * @var stackedBarChart $stackedBarChart
 * @var bubbleChart $bubbleChart
 * @var radarChart $radarChart
 * @var cumulativeGainChart $cumulativeChart
 * @var pointChart $pointChart
 * @var costAnalyze $costAnalyze
 * @var chartsHandler $chartsHandler
 */

$sf_response->setTitle('Wall');
decorate_with('steps_layout');
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array('decision_id' => $decision->getId()));?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
  Wall <small>Pinned components</small>
<?php end_slot(); ?>

<?php slot('project_name'); ?>
  <?php echo $decision->name ?>
<?php end_slot(); ?>

<?php slot('app_menu'); ?>
<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>
<?php end_slot(); ?>

<?php slot('navigation_links_left'); ?>
  <a class="btn btn-primary" href="<?php echo url_for('@analyze') ?>"><?php echo __('Back') ?></a>
  <a class="btn btn-primary" href="<?php echo url_for('@response') ?>"><?php echo __('Next') ?></a>
<?php end_slot(); ?>

<?php slot('navigation_links'); ?>
  <a class="steps-navigation step-prev" href="<?php echo url_for('@analyze') ?>">&lt;</a>
  <a class="steps-navigation step-next" href="<?php echo url_for('@response') ?>">&gt;</a>
<?php end_slot(); ?>

<?php slot('section1'); ?>
  <div class="posts">
    <?php if (!$posts->count()) : ?>
      No posts
    <?php endif ?>
    <?php foreach ($posts as $post): ?>
      <div id="post-<?php echo $post->id ?>" class="well">
        <div class="form-wrap">
          <input id="title-field-<?php echo $post->id ?>" class="description-text form-control" value="<?php echo $post->title ?>" type="text"/>
        </div>
        <div class="form-wrap">
          <?php
          if ($post->type){
            if(in_array($post->type, array('partition', 'cost', 'criteria', 'radar', 'cumulative', 'bubble', 'alternatives', 'xy'))){
              $chartsHandler->generateChart($post->id);
            }
          }else{
            echo $post->getRawValue()->content;
          }
          ?>
        </div>
        <div class="form-wrap">
          <textarea class="description-text form-control" id="comment-box-<?php echo $post->id ?>" cols="30" rows="5"><?php echo $post->comment ?></textarea>
        </div>
        <div class="buttons-wrapper right">
          <a class="btn btn-primary save-comment" id="save-comment-<?php echo $post->id ?>" href="javascript:void(0)">Save</a>
          <a class="delete-button btn btn-danger" id="delete-post-<?php echo $post->id ?>" href="javascript:void(0)">Delete</a>
        </div>
      </div>
    <?php endforeach ?>
  </div>
  <div class="form-group">
    <label for="wall-link" class="control-label col-xs-2">Link to wall</label>

    <div class="col-xs-10">
      <div class="input-group col-xs-5">
        <input id="wall-link" type="text" class="form-control" onclick="$(this).select()" readonly="readonly" value="<?php echo $url ?>"/>
          <span class="input-group-btn">
            &nbsp;<a id="wall-button" class="btn btn-primary" href="javascript:void(0)"><?php echo __('Wall') ?></a>
          </span>
      </div>
    </div>
  </div>

  <a id="wall-export" class="btn btn-primary btn-lg" href="<?php echo url_for('@wall\export') ?>"><?php echo _('Export') ?></a>
<?php end_slot(); ?>

<?php slot('closure'); ?>
  <script type="text/javascript">
    $(function () {
      <?php if (false && $sf_user->getGuardUser()->account_type != 'Enterprise') : ?>
        $('#wall-export').on('click', function () {
          alert('Export is an Enterprise function. Please upgrade');
          return false
        });
      <?php endif ?>

      $('.save-comment').click(function () {
        var id = $(this).attr('id').replace('save-comment-', '');
        $.post('<?php echo url_for('@wall\saveComment') ?>', {
          id     : id,
          title  : $('#title-field-' + id).val(),
          comment: $('#comment-box-' + id).val()
        });
      });

      $('#wall-button').click(function () {
        window.open($('#wall-link').val());
      });

      $('.delete-button').click(function () {
        if (confirm('<?php echo 'You are about to delete this ' . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) . '. Press Ok to continue.' ?>')) {
          var id = $(this).attr('id').replace('delete-post-', '');
          $.post('<?php echo url_for('@wall\deletePost') ?>', { id: id }, function (response) {
            $('#post-' + id).remove();
          });
        }
      });
    });
  </script>

  <style type="text/css">
    footer .box-1 {
      box-shadow: none;
      background: none;
    }
  </style>
<?php end_slot(); ?>
