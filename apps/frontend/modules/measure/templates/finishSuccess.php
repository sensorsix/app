<?php
/**
 * @var sfGuardUser $sf_user
 * @var Role $role
 * @var CriteriaAnalyze $criteriaAnalyze
 * @var StackedBarChart $stackedBarChart
 */
include_component('measure', 'logo');
?>
<?php if ($sf_user->isAnonymous() && isset($sf_culture) && $sf_culture == 'en') : ?>
  <div class="form-group">
    <a class="btn btn-success btn-large" href="<?php echo url_for('@sf_guard_register') ?>"><?php echo __('Try it Yourself') ?></a>
  </div>
<?php endif ?>
  <div class="description">
    <?php echo __('Thank you for participating in this workspace.') ?><br/><br/>
  </div>
<?php if ($role->show_criteria_weights || $role->show_alternatives_score) : ?>
  Here are the preliminary results from the workspace<br/><br/>
  <?php if ($role->show_criteria_weights) : ?>
    <?php if ($criteriaAnalyze->hasData()) : ?>
      <?php $criteriaAnalyze->render(); ?>
    <?php else : ?>
      <div class="alert alert-info"><?php echo __('No data') ?></div>
    <?php endif; ?>
  <?php endif ?>
  <?php if ($role->show_alternatives_score) : ?>
    <?php if ($stackedBarChart->hasData()) : ?>
      <?php $stackedBarChart->render(); ?>
    <?php else : ?>
      <div class="alert alert-info"><?php echo __('No data') ?></div>
    <?php endif ?>
  <?php endif ?>
<?php endif ?>
<?php if ($sf_user->isAnonymous() && $sf_culture == 'en') : ?>
  <div class="form-group">
    <a class="btn btn-success btn-large" href="<?php echo url_for('@sf_guard_register') ?>"><?php echo __('Try it free') ?></a>
  </div>
<?php endif?>
<?php if (isset($sf_culture) && $sf_culture == 'en') : ?>
  <div class="column subscribe-wrapper" style="width: 30%">
    <form id="subscribe-newsletters">
      <div class="input-group">
        <input id="subscriber-email" type="email" class="form-control input-lg" name="email" placeholder="Email">
        <span class="input-group-btn">
          <button id="subscribe-button" class="btn btn-success btn-lg" type="button"><?php echo __('Get News') ?></button>
        </span>
      </div>
    </form>
  </div>
<?php endif ?>