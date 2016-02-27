<?php use_helper('I18N') ?>
<div class="row">
  <div class="page-header col-xs-7 col-xs-offset-3">
    <h1><?php echo __('Forgot your password?', null, 'sf_guard') ?></h1>
  </div>
  <div class="form-group col-xs-7 col-xs-offset-3">
    <?php echo __('Do not worry, we can help you get back in to your account safely!', null, 'sf_guard') ?>
    <?php echo __('Fill out the form below to request an e-mail with information on how to reset your password.', null, 'sf_guard') ?>
  </div>
  <form action="<?php echo url_for('@sf_guard_forgot_password') ?>" method="post" class="form-horizontal col-xs-8 col-xs-offset-2">
    <div class="form-group">
      <div class="col-xs-12">
        <div class="form-group">
            <?php echo $form['email_address']->renderLabel(null, array('class' => 'col-xs-4 control-label')) ?>
          <div class="col-xs-8">
              <?php echo $form['email_address']->render() ?>
              <?php echo $form->renderHiddenFields() ?>
          </div>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-xs-8 col-xs-offset-4">
        <input class="btn btn-success btn-lg" type="submit" name="change" value="<?php echo __('Request', null, 'sf_guard') ?>"/>
      </div>
    </div>
  </form>
</div>

