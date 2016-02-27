<?php
/**
 * @var Decision $decision
 * @var AnalyzeCollapse $analyzeCollapse
 * @var sfWebResponse $sf_response
 * @var LogicalFilterView $logicalFilter
 * @var RoleFilterView $roleFilter
 * @var StatusFilterView $statusFilter
 * @var TagFilterView $tagFilter
 * @var StackedBarChart $stackedBarChart
 * @var RadarChart $radarChart
 * @var PointChart $pointChart
 * @var BubbleChart $bubbleChart
 * @var CostAnalyze $costAnalyze
 * @var CumulativeGainChart $cumulativeChart
 * @var CriteriaAnalyze $criteriaAnalyze
 * @var Doctrine_Collection|Comment[] $comments
 */

$sf_response->setTitle('Analyze');
decorate_with('steps_layout');
$analyzeCollapse = $decision->getRawValue()->AnalyzeCollapse;
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array('decision_id' => $decision->getId()));?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
Analyze
<?php end_slot(); ?>

<?php slot('project_name'); ?>
<?php echo $decision->name ?>
<?php end_slot(); ?>


<?php slot('app_menu'); ?>

<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>

<?php end_slot(); ?>


<?php slot('navigation_links_left'); ?>
<a class="btn btn-primary" href="<?php echo url_for('@role') ?>"><?php echo __('Back') ?></a>
<a class="btn btn-primary" href="<?php echo url_for('@wall') ?>"><?php echo __('Next') ?></a>
<?php end_slot(); ?>

<?php slot('navigation_links'); ?>
<a class="steps-navigation step-prev" href="<?php echo url_for('@role') ?>">&lt;</a>
<a class="steps-navigation step-next" href="<?php echo url_for('@wall') ?>">&gt;</a>
<?php end_slot(); ?>

<?php slot('section1'); ?>
<div class="panel-group analyze-accordion" id="accordion">
<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapseZero">
        <?php echo __('Filter') ?>
      </a>
    </h4>
  </div>
  <div id="collapseZero" data-section="filter" class="panel-collapse collapse<?php echo $analyzeCollapse->filter ? '' : ' in' ?>">
    <div class="panel-body">
      <?php if ($decision->hasInAnalyze('logical_filter')) : ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseZeroOne">
                <?php echo __('Logical') ?>
              </a>
            </h4>
          </div>
          <div id="collapseZeroOne" data-section="logical_filter" class="panel-collapse collapse<?php echo $analyzeCollapse->logic_filter ? '' : ' in' ?>">
            <div class="panel-body">
              <?php $logicalFilter->render(); ?>
            </div>
          </div>
        </div>
      <?php endif ?>
      <?php if ($decision->hasInAnalyze('role_filter')) : ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseZeroTwo">
                <?php echo __('Role') ?>
              </a>
            </h4>
          </div>
          <div id="collapseZeroTwo" data-section="role" class="panel-collapse collapse<?php echo $analyzeCollapse->role ? '' : ' in' ?>">
            <div class="panel-body">
              <?php $roleFilter->render(); ?>
            </div>
          </div>
        </div>
      <?php endif ?>

      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseZeroThree">
              <?php echo __('Status') ?>
            </a>
          </h4>
        </div>
        <div id="collapseZeroThree" data-section="status_filter" class="panel-collapse collapse<?php echo $analyzeCollapse->status_filter ? '' : ' in' ?>">
          <div class="panel-body">
            <?php $statusFilter->render(); ?>
          </div>
        </div>
      </div>


      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseZeroFour">
              <?php echo __('Tag') ?>
            </a>
          </h4>
        </div>
        <div id="collapseZeroFour" data-section="tag_filter" class="panel-collapse collapse<?php echo $analyzeCollapse->tag_filter ? '' : ' in' ?>">
          <div class="panel-body">
            <?php $tagFilter->render(); ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
        <?php echo __('Criteria weights') ?>
      </a>
    </h4>
  </div>
  <div id="collapseOne" data-section="criteria_weights" class="panel-collapse collapse<?php echo $analyzeCollapse->criteria_weights ? '' : ' in' ?>">
    <div class="panel-body">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOneOne">
              <?php echo __('Bar chart') ?>
            </a>
          </h4>
        </div>
        <div id="collapseOneOne" data-section="bar_chart" class="panel-collapse collapse<?php echo $analyzeCollapse->bar_chart ? '' : ' in' ?>">
          <div class="panel-body">
            <?php if ($criteriaAnalyze->hasData()) : ?>
              <?php $criteriaAnalyze->render(); ?>
            <?php else : ?>
              <div class="alert alert-info"><?php echo __('No data') ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
        <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?></p> score
      </a>
    </h4>
  </div>
  <div id="collapseThree" data-section="alternative_score" class="panel-collapse collapse<?php echo $analyzeCollapse->alternative_score ? '' : ' in' ?>">
    <div class="panel-body">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThreeOne">
              <?php echo __('Horizontal stacked bar') ?>
            </a>
          </h4>
        </div>
        <div id="collapseThreeOne" data-section="stacked_bar" class="panel-collapse collapse<?php echo $analyzeCollapse->stacked_bar ? '' : ' in' ?>">
          <div class="panel-body">
            <?php if ($stackedBarChart->hasData()) : ?>
              <?php $stackedBarChart->render(); ?>
            <?php else : ?>
              <div class="alert alert-info"><?php echo __('No data') ?></div>
            <?php endif ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapseFore">
        <?php echo __('Graphs') ?>
      </a>
    </h4>
  </div>
  <div id="collapseFore" data-section="graphs" class="panel-collapse collapse<?php echo $analyzeCollapse->graphs ? '' : ' in' ?>">
    <div class="panel-body">
      <?php if ($decision->hasInAnalyze('xy')) : ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseForeOne">
                <?php echo __('XY plot') ?>
              </a>
            </h4>
          </div>
          <div id="collapseForeOne" data-section="xy_plot" class="panel-collapse collapse<?php echo $analyzeCollapse->xy_plot ? '' : ' in' ?>">
            <div class="panel-body">
              <?php if ($pointChart->hasData()) : ?>
                <?php $pointChart->render(); ?>
              <?php else : ?>
                <div class="alert alert-info"><?php echo __('No data') ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif ?>
      <?php if ($decision->hasInAnalyze('bubble')) : ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseForeTwo">
                <?php echo __('Bubble plot') ?>
              </a>
            </h4>
          </div>
          <div id="collapseForeTwo" data-section="bubble_plot" class="panel-collapse collapse<?php echo $analyzeCollapse->bubble_plot ? '' : ' in' ?>">
            <div class="panel-body">
              <?php if ($bubbleChart->hasData()) : ?>
                <?php $bubbleChart->render(); ?>
              <?php else : ?>
                <div class="alert alert-info"><?php echo __('No data') ?></div>
              <?php endif ?>
            </div>
          </div>
        </div>
      <?php endif ?>

      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a class="panel-toggle" data-toggle="collapse" data-parent="" href="#collapseFiveOne">
              <?php echo __('Radar plot') ?>
            </a>
          </h4>
        </div>
        <div id="collapseFiveOne" data-section="cumulative_gain" class="panel-collapse collapse<?php echo $analyzeCollapse->cumulative_gain ? '' : ' in' ?>">
          <div class="panel-body">
            <?php if ($radarChart->hasData()) : ?>
              <?php $radarChart->render(); ?>
            <?php else : ?>
              <div class="alert alert-info"><?php echo __('No data') ?></div>
            <?php endif ?>
          </div>
        </div>
      </div>

      <?php if ($decision->hasInAnalyze('cumulative')) : ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseForeThree">
                <?php echo __('Cumulative gain') ?>
              </a>
            </h4>
          </div>
          <div id="collapseForeThree" data-section="cumulative_gain" class="panel-collapse collapse<?php echo $analyzeCollapse->cumulative_gain ? '' : ' in' ?>">
            <div class="panel-body">
              <?php if ($cumulativeChart->hasData()) : ?>
                <?php $cumulativeChart->render(); ?>
              <?php else : ?>
                <div class="alert alert-info"><?php echo __('No data') ?></div>
              <?php endif ?>
            </div>
          </div>
        </div>
      <?php endif ?>
    </div>
  </div>
</div>
<?php if ($decision->hasInAnalyze('partitioner')) :
  /*
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseSixOnecollapseSix">
          <?php echo __('Partitions') ?>
        </a>
      </h4>
    </div>
    <div id="collapseSixOnecollapseSix" data-section="partitions" class="panel-collapse collapse<?php echo $analyzeCollapse->partitions ? '' : ' in' ?>">
      <div class="panel-body">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#collapseSixOne">
                <?php echo $decision->relatedExists('Type') && $decision->type_id == 2 ? __('Release planner') : __('Partition allocation') ?>
              </a>
            </h4>
          </div>
          <div id="collapseSixOne" data-section="partition_allocation" class="panel-collapse collapse<?php echo $analyzeCollapse->partition_allocation ? '' : ' in' ?>">
            <div class="panel-body">
              <?php include_partial('partition_allocation', array('releases' => $releases, 'decision' => $decision)) ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  */
  ?>
<?php endif ?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h4 class="panel-title">
      <a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven">
        <?php echo __('Comments') ?>
      </a>
    </h4>
  </div>
  <div id="collapseSeven" data-section="comments" class="panel-collapse collapse<?php echo $analyzeCollapse->comments ? '' : ' in' ?>">
    <div class="panel-body">
      <?php if ($comments->count()) : ?>
        <?php include_partial('comments', array('comments' => $comments)) ?>
      <?php else : ?>
        <div class="alert alert-info"><?php echo __('No data') ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>
</div>
<?php end_slot(); ?>

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
