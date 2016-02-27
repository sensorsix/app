<?php
/**
 * @var Decision $decision
 * @var sfWebResponse $sf_response
 * @var costAnalyze $costAnalyze
 */
$sf_response->setTitle('Planner');
decorate_with('steps_layout');

/** @var AnalyzeCollapse $analyzeCollapse */
$analyzeCollapse = $decision->getRawValue()->AnalyzeCollapse;
?>

<?php slot('app_name'); ?>
  <?php echo __("Planner"); ?>
<?php end_slot(); ?>

<?php slot('project_name'); ?>
  <?php echo $decision->name ?>
<?php end_slot(); ?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array('decision_id' => $decision->getId()));?>
<?php end_slot(); ?>

<?php if ($decision->hasInAnalyze('cost')) : ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwoOne">
          <?php echo __('Budget allocation') ?>
        </a>
      </h4>
    </div>
    <div id="collapseTwoOne" data-section="budget_allocation" class="panel-collapse collapse<?php echo $analyzeCollapse->budget_allocation ? '' : ' in' ?>">
      <div class="panel-body">
        <?php if ($costAnalyze->hasData()) : ?>
          <?php $costAnalyze->render(); ?>
        <?php else : ?>
          <div class="alert alert-info"><?php echo __('No data') ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif ?>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseSixOne">
          <?php echo $decision->relatedExists('Type') && $decision->type_id == 2 ? "<p class='label_capitalize'>" . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE) ."</p> planner" : 'Partition allocation' ?>
        </a>
      </h4>
    </div>
    <div id="collapseSixOne" data-section="partition_allocation" class="panel-collapse collapse<?php echo $analyzeCollapse->partition_allocation ? '' : ' in' ?>">
      <div class="panel-body">
        <?php
        include_partial('partition_allocation', array(
            'releases' => $releases,
            'decision' => $decision,
            'alternatives_json' => $alternatives_json
          )
        );
        ?>
      </div>
    </div>
  </div>

<?php slot('closure');?>
  <script type="text/javascript">
    $(function () {
      $('.collapse').collapse({toggle: false});
      $('.panel-heading a').on('click', function () {
        $.post('<?php echo url_for('@analyze\collapse') ?>', { type: $(this.hash).data('section'), collapse: $(this.hash).hasClass('in') });
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