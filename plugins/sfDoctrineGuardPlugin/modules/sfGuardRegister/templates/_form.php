<?php use_helper('I18N') ?>

<form action="<?php echo url_for('@sf_guard_register') ?>" method="post" class="form-horizontal col-xs-7 col-xs-offset-2">
  <div class="form-group">
    <div class="col-xs-12">
      <?php echo $form->renderHiddenFields();
      echo $form['email_address']->renderRow();
      echo $form['password']->renderRow();
      ?>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-8 col-xs-offset-4" style="color: #999; font-size: 13px;">
      By filling in the form above and clicking the "Sign Up" button, you accept and agree to <a href="<?php echo url_for('@terms');?>" target="_blank">Terms of Service</a>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-8 col-xs-offset-4">
      <input class="btn btn-success btn-lg" type="submit" name="register" value="<?php echo __('Register', null, 'sf_guard') ?>"/>
    </div>
  </div>
</form>