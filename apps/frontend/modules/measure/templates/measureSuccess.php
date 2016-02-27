<?php
/**
 * @var Measurement $measurement
 * @var sfGuardUser $sf_user
 * @var Measurement $measurement
 * @var Role $role
 * @var sfWebResponse $sf_response
 * @var string $formURL
 */

include_component('measure', 'logo') ?>

<?php if ($sf_user->isAnonymous()) : ?>
  <div class="control-group" style="position: absolute; top: 15px; right: 15px;">
    <a class="btn btn-success btn-large" href="<?php echo url_for('@sf_guard_register') ?>"><?php echo __('Try it free') ?></a>
  </div>
<?php endif?>
<form class="form-horizontal rating-form" action="<?php echo $formURL ?>" method="post">
  <?php if ($measurement->getMethodObject()->hasData()) : ?>
    <?php $measurement->render() ?>
  <?php else : ?>
    <div class="alert alert-info"><?php echo __('No data') ?></div>
  <?php endif ?>
  <div class="row">
    <div class="col-xs-2 col-md-2">
      <?php if ($measurement->hasPreviousStep()) : ?>
        <input class="btn btn-inverse right" type="submit" name="back" value="<?php echo __('Back') ?>" />
      <?php endif ?>
    </div>

    <?php if ($measurement->hasNextStep() || $role->continue_url) : ?>
      <div class="col-xs-8 col-md-8">
        <div class="progress">
          <div class="progress-bar" role="progressbar" style="width: <?php echo $measurement->getProgress() ?>;">
            <span class="sr-only">60% Complete</span>
          </div>
        </div>
      </div>
      <div class="col-xs-2 col-md-2">
        <input class="btn btn-primary left" type="submit" name="next" value="<?php echo __('Next') ?>" />
      </div>
    <?php else : ?>
      <div class="col-xs-6 col-md-8">
        <div class="progress">
          <div class="progress-bar" role="progressbar" style="width: <?php echo $measurement->getProgress() ?>;">
            <span class="sr-only">60% Complete</span>
          </div>
        </div>
      </div>
      <div class="col-xs-4 col-md-2">
        <input class="btn btn-primary left" type="submit" name="next" value="<?php echo __('Submit answers') ?>" />
      </div>
    <?php endif ?>
  </div>
</form>

<script>
  $(function () {
    $('.colorbox').colorbox();
  });
</script>
