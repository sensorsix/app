<?php
/**
 * @var TypeTemplateForm $form
 */
?>
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form class="form-horizontal" action="<?php echo url_for('type_template/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <?php if (!$form->getObject()->isNew()): ?>
    <input type="hidden" name="sf_method" value="put" />
  <?php endif; ?>
  <fieldset>
    <div class="form-group ">
      <?php echo $form['name']->renderLabel(array(), array('class' => 'col-xs-4 control-label')) ?>
      <?php echo $form['name']->renderError() ?>
      <div class="col-xs-8">
        <?php echo $form['name']->render(array('class' => 'form-control')) ?>
      </div>
    </div>

    <div class="form-group ">
      <?php echo $form['type_id']->renderLabel(array(), array('class' => 'col-xs-4 control-label')) ?>
      <?php echo $form['type_id']->renderError() ?>
      <div class="col-xs-8">
        <?php echo $form['type_id']->render(array('class' => 'form-control')) ?>
      </div>
    </div>

    <div class="form-group ">
      <?php echo $form['alternative_alias']->renderLabel(array(), array('class' => 'col-xs-4 control-label')) ?>
      <?php echo $form['alternative_alias']->renderError() ?>
      <div class="col-xs-8">
        <?php echo $form['alternative_alias']->render(array('class' => 'form-control')) ?>
      </div>
    </div>

    <div class="form-group ">
      <?php echo $form['alternative_plural_alias']->renderLabel(array(), array('class' => 'col-xs-4 control-label')) ?>
      <?php echo $form['alternative_plural_alias']->renderError() ?>
      <div class="col-xs-8">
        <?php echo $form['alternative_plural_alias']->render(array('class' => 'form-control')) ?>
      </div>
    </div>

    <div class="form-group ">
      <?php echo $form['partitioner_alias']->renderLabel(array(), array('class' => 'col-xs-4 control-label')) ?>
      <?php echo $form['partitioner_alias']->renderError() ?>
      <div class="col-xs-8">
        <?php echo $form['partitioner_alias']->render(array('class' => 'form-control')) ?>
      </div>
    </div>

    <div class="form-actions text-center">
      <?php echo $form->renderHiddenFields(false) ?>
      &nbsp;<a class="btn" href="<?php echo url_for('type_template/index') ?>">Back to list</a>
      <?php if (!$form->getObject()->isNew()): ?>
        &nbsp;<?php echo link_to('Delete', 'type_template/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class'=>'btn btn-danger btn-lg')) ?>
      <?php endif; ?>
      <input class="btn btn-primary btn-lg" type="submit" value="<?php echo __('Save', null, 'sf_guard') ?>" />
    </div>
  </fieldset>
</form>