<?php use_helper('I18N') ?>
<div class="row">
  <div class="page-header col-xs-7 col-xs-offset-3">
    <h1><?php echo __('Register', null, 'sf_guard') ?></h1>
  </div>
  <?php echo get_partial('sfGuardRegister/form', array('form' => $form)) ?>
</div>
