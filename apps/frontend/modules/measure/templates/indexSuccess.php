<?php
/**
 * @var Decision $decision
 * @var MeasurementStartForm $form
 */
include_component('measure', 'logo');
?>
<h1 class="title" style="margin-top: 0"><?php echo __('Measure') ?></h1>
<form class="form-horizontal measure" action="<?php echo url_for('@measure\start') ?>" method="post">
  <div class="description">
    <p><?php echo __('Thank you for participating in this workspace') ?>: "<?php echo $decision->name ?>"</p>
    <?php if ($sf_culture == 'en') : ?>
    <p><?php echo __('Press "Start" to enter the questionnaire.') ?></p>
    <?php endif ?>
  </div>
  <div class="row-fluid">
    <div class="span2"></div>
    <div class="span10">
      <?php echo $form['email_address']->renderError() ?>
      <?php echo $form['email_address']->renderLabel() ?>
      <div class="controls">
        <?php echo $form['email_address']->render() ?>
        <?php echo $form->renderHiddenFields() ?>
      </div>
    </div>
  </div>
  <div class="controls start">
    <input class="btn btn-large" type="submit" name="start" value="Start" />
  </div>
</form>