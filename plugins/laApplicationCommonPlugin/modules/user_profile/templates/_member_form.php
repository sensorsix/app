<form action="" method="post" class="form-horizontal" enctype="multipart/form-data">
  <fieldset>
      <div class="form-group">
        <?php echo $form['team']->renderLabel(null, array('style'=>'padding-top:0')) ?>
        <div class="controls col-xs-8">
          <?php foreach ($form['team'] as $key => $embeddedSchema) : ?>
            <div class="team-member-form" style="display: <?php echo $embeddedSchema['id']->getValue() || $embeddedSchema->hasError() ? 'block' : 'none' ?>">
              <?php foreach ($embeddedSchema as $key => $field) : ?>
                <?php if ($field instanceof sfFormFieldSchema) : ?>
                  <?php echo $field['username']->render() ?>
                  <?php if ($field['id']->getValue()) : ?>
                    <a class="btn btn-primary edit-user" data-id="<?php echo $field['id']->getValue() ?>" href="javascript:void(0)">Edit</a>
                    <a class="btn btn-warning delete-user" data-id="<?php echo $field['id']->getValue() ?>" href="javascript:void(0)">Delete</a>
                  <?php endif ?>
                  <div id="embedded-user-<?php echo $field['id']->getValue() ?>" style="display: <?php echo $field['id']->getValue() ? 'none' : 'block' ?>">
                    <div class="form-group">
                      <?php echo $field['email_address']->renderLabel(null, array('class'=>'col-md-3')) ?>
                      <div class="col-md-9">
                        <?php echo $field['email_address']->render(array('class'=>'form-control')) ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <?php if ($field['password']->hasError()) : ?>
                        <div class="col-md-offset-3 alert alert-danger">
                          <?php echo $field['password']->getError() ?>
                        </div>
                      <?php endif ?>
                      <?php echo $field['password']->renderLabel(null, array('class'=>'col-md-3')) ?>
                      <div class="col-md-9">
                        <?php echo $field['password']->render(array('class'=>'form-control')) ?>
                      </div>
                    </div>
                    <div class="form-group">
                      <?php if ($field['password_again']->hasError()) : ?>
                        <div class="col-md-offset-3 alert alert-danger">
                          <?php echo $field['password_again']->getError() ?>
                        </div>
                      <?php endif ?>
                      <?php echo $field['password_again']->renderLabel(null, array('class'=>'col-md-3')) ?>
                      <div class="col-md-9">
                        <?php echo $field['password_again']->render(array('class'=>'form-control')) ?>
                      </div>
                    </div>
                  </div>
                <?php elseif (!$field->isHidden()) : ?>
                  <?php echo $field->renderRow(); ?>
                <?php endif ?>
              <?php endforeach ?>
              <?php echo $embeddedSchema->renderHiddenFields() ?>
            </div>
          <?php endforeach ?>
          <a class="btn btn-success" id="add-team-member" href="javascript:void(0)">Add user</a>
        </div>
      </div>

    <?php echo $form->renderHiddenFields() ?>

    <div class="form-actions">
      <input class="btn btn-primary btn-lg" type="submit" name="register" value="<?php echo __('Save', null, 'sf_guard') ?>" />
    </div>
  </fieldset>
</form>

<script>
  $(function () {
    $('.edit-user').on('click', function () {
      $('#embedded-user-' + $(this).data('id')).show();
      $(this).remove();
    });

    var $add_team = $('#add-team-member');
    $add_team.on('click', function() {
      var $hidden_team_forms = $('.team-member-form:hidden');
      if ($hidden_team_forms.length == 1) {
        $(this).hide();
      }
      $('.team-member-form:hidden').first().show();
    });

    $('.delete-user').on('click', function () {
      var $embedded_user = $('#embedded-user-' + $(this).data('id'));
      $embedded_user.find('input[type=text]').val('');
      $embedded_user.parent().hide();

      $(this).remove();
    });
  });
</script>