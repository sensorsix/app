<?php
/**
 * @var ScheduleDemoForm $schedule_demo_form
 * @var sfGuardUser $sf_user
 */
?>
<div class="search-form">
  <form action="" method="post">
    <?php foreach ($schedule_demo_form->getGlobalErrors() as $error) : ?>
      <div class="errorLabel"><?php echo $error ?></div>
    <?php endforeach ?>
    <div class="error-message">
      <?php if ($schedule_demo_form['email']->hasError()) : ?>
        <div class="errorLabel email-address">
          <?php echo $schedule_demo_form['email']->getError() ?>
        </div>
      <?php endif ?>
    </div>
    <div class="left formField">
      <?php echo $schedule_demo_form['email']->render(array('class' => 'email', 'placeholder' => 'Email')); ?>
    </div>
    <?php echo $schedule_demo_form->renderHiddenFields() ?>
    <button type="submit" class="btn btn-success btn-lg btn-submit-form">Schedule a demo</button>
    <?php if ($sf_user->hasFlash('schedule_notice')): ?>
      <div class="row errorLabel" style="margin-top: 10px;">
        <?php echo __($sf_user->getFlash('schedule_notice')) ?>
      </div>
    <?php endif ?>
  </form>
</div>