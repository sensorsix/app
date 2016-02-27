<form action="" method="post" class="form-horizontal" enctype="multipart/form-data">
  <fieldset>
    <?php echo $form['first_name']->renderRow() ?>
    <?php echo $form['last_name']->renderRow() ?>
    <?php echo $form['email_address']->renderRow() ?>
    <?php echo $form['country']->renderRow() ?>

    <div class="form-group">
      <a id="change-password-button" class="btn btn-default" href="javascript:void(0)">Change password</a>
    </div>

    <div id="change-password" style="display: none">
      <?php echo $form['password']->renderRow() ?>
      <?php echo $form['password_again']->renderRow() ?>
    </div>

    <?php echo $form->renderHiddenFields() ?>

    <div class="form-actions">
      <input class="btn btn-primary btn-lg" type="submit" name="register" value="<?php echo __('Save', null, 'sf_guard') ?>" />
    </div>
  </fieldset>
</form>

<script>
  $(function () {
    $('#change-password-button').on('click', function () {
      $('#change-password').toggle();
    });
  });
</script>