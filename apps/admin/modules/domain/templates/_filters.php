<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<div class="sf_admin_filter">
  <?php if ($form->hasGlobalErrors()): ?>
    <?php echo $form->renderGlobalErrors() ?>
  <?php endif; ?>

  <form class="form-inline" action="<?php echo url_for('sf_guard_user_collection', array('action' => 'filter')) ?>" method="post">
    <div class="form-group">
      <?php foreach ($configuration->getFormFilterFields($form) as $name => $field): ?>
        <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
        <?php include_partial('domain/filters_field', array(
            'name'       => $name,
            'attributes' => $field->getConfig('attributes', array()),
            'label'      => $field->getConfig('label'),
            'help'       => $field->getConfig('help'),
            'form'       => $form,
            'field'      => $field,
            'class'      => ' sf_admin_form_row sf_admin_'.strtolower($field->getType()).' sf_admin_filter_field_'.$name,
        )) ?>
      <?php endforeach; ?>
    </div>
    <div class="form-group">
      <?php echo $form->renderHiddenFields() ?>
      <button class="btn btn-default">
        <?php echo link_to(__('Reset', array(), 'sf_admin'), 'sf_guard_user_collection', array('action' => 'filter'), array('query_string' => '_reset', 'method' => 'post')) ?>
      </button>
      <button class="btn btn-primary" type="submit">
        <?php echo __('Filter', array(), 'sf_admin') ?>
      </button>
    </div>
  </form>
</div>
