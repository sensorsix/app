<?php
/**
 * @var TypeTemplateForm $form
 * @var int $template_id
 */
?>
<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form class="form-horizontal" action="<?php echo url_for('type_template/criteria'.($form->getObject()->isNew() ? 'Create' : 'Update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '?template_id='.$template_id)) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
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
      <?php echo $form['variable_type']->renderLabel(array(), array('class' => 'col-xs-4 control-label')) ?>
      <?php echo $form['variable_type']->renderError() ?>
      <div class="col-xs-8">
        <?php echo $form['variable_type']->render(array('class' => 'form-control')) ?>
      </div>
    </div>

    <div class="form-group ">
      <?php echo $form['measurement']->renderLabel(array(), array('class' => 'col-xs-4 control-label')) ?>
      <?php echo $form['measurement']->renderError() ?>
      <div class="col-xs-8">
        <?php echo $form['measurement']->render(array('class' => 'form-control')) ?>
      </div>
    </div>

    <div class="form-actions text-center">
      <?php echo $form->renderHiddenFields(false) ?>
      <?php if (!$form->getObject()->isNew()): ?>
        &nbsp;<a class="btn" href="<?php echo url_for('type_template/edit?id='.$form->getObject()->Template->getId()) ?>">Back to template</a>
        &nbsp;<?php echo link_to('Delete', 'type_template/criteriaDelete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class'=>'btn btn-danger btn-lg')) ?>
      <?php else: ?>
        &nbsp;<a class="btn" href="<?php echo url_for('type_template/edit?id='.$template_id) ?>">Back to template</a>
      <?php endif; ?>
      <input class="btn btn-primary btn-lg" type="submit" value="<?php echo __('Save', null, 'sf_guard') ?>" />
    </div>
  </fieldset>
</form>