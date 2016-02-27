<?php
use_helper('I18N');
$routes = $sf_context->getRouting()->getRoutes();
?>

<form action="<?php echo url_for('@sf_guard_signin') ?>" method="post" role="form">
  <h5 class="sign-in-form-title">Login</h5>
  <?php echo $form->renderHiddenFields(false) ?>
  <?php echo $form->renderGlobalErrors() ?>
  <div class="form-group">
    <?php echo $form['username']->renderError() ?>
    <?php echo $form['username']->render(array('placeholder' => 'Email', 'style' => 'text-align:center', 'class' => 'form-control')) ?>
  </div>
  <div class="form-group">
    <?php echo $form['password']->renderError() ?>
    <?php echo $form['password']->render(array('placeholder' => 'Password', 'style' => 'text-align:center', 'class' => 'form-control')) ?>

    <?php if (isset($routes['sf_guard_forgot_password'])): ?>
      <p><a class="sign-in-forgot-password" href="<?php echo url_for('@sf_guard_forgot_password') ?>">Forgot your password?</a></p>
    <?php endif; ?>
  </div>
  <div class="form-group">
    <input class="btn btn-success btn-block" type="submit" value="<?php echo __('Login', null, 'sf_guard') ?>"/>
  </div>
<!--  <div class="additional-links">
    <p><a href="mailto:<?php /*echo sfConfig::get('app_info_email')*/?>">Contact</a></p>
  </div>-->
</form>