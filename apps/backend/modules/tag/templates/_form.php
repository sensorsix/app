<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('tag/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?> class="form-horizontal">
  <?php if (!$form->getObject()->isNew()): ?>
    <input type="hidden" name="sf_method" value="put" />
  <?php endif; ?>
  <fieldset>
    <div class="form-group ">
      <?php echo $form['user_id']->renderLabel(array(), array('class' => 'col-xs-4 control-label')) ?>
      <?php echo $form['user_id']->renderError() ?>
      <div class="col-xs-8">
        <?php echo $form['user_id']->render(array('class' => 'form-control')) ?>
      </div>
    </div>

    <div class="form-group ">
      <?php echo $form['name']->renderLabel(array(), array('class' => 'col-xs-4 control-label')) ?>
      <?php echo $form['name']->renderError() ?>
      <div class="col-xs-8">
        <?php echo $form['name']->render(array('class' => 'form-control')) ?>
      </div>
    </div>

    <div class="form-actions">
      <?php echo $form->renderHiddenFields(false) ?>
      &nbsp;<a class="btn" href="<?php echo url_for('tag/index') ?>">Back to list</a>
      <?php if (!$form->getObject()->isNew()): ?>
        &nbsp;<?php echo link_to('Delete', 'tag/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class'=>'btn btn-danger btn-lg')) ?>
      <?php endif; ?>
      <input class="btn btn-primary btn-lg" type="submit" value="<?php echo __('Save', null, 'sf_guard') ?>" />
    </div>
  </fieldset>
</form>