<form action="<?php echo url_for('@user_profile\design') ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
  <fieldset>
    <?php if ($sf_user->getGuardUser()->account_type == 'Enterprise') : ?>
      <div class="form-group ">
        <?php echo $form['header_color']->renderLabel() ?>
        <div class="col-xs-8">
          <div class="input-group color-picker">
            <?php echo $form['header_color']->render() ?>
            <span class="input-group-addon"><i></i></span>
          </div>
        </div>
      </div>

      <?php echo $form['logo_file']->renderRow() ?>
      <?php echo $form['logo_url']->renderRow() ?>
    <?php endif ?>

    <?php echo $form->renderHiddenFields() ?>

    <div class="form-actions">
      <input class="btn btn-primary btn-lg" type="submit" name="register" value="<?php echo __('Save', null, 'sf_guard') ?>" />
    </div>
  </fieldset>
</form>

<script>
  $(function () {
    $('.color-picker').colorpicker();
  });
</script>