<?php
/**
 * @var sfWebResponse $sf_response
 * @var CriteriaTemplateForm $form
 * @var int $template_id
 */
?>
<?php $sf_response->setSlot('menu_type_template_active', true) ?>
<?php include_partial('global/menu') ?>

<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-5">
    <h1>New Criteria template</h1>
    <?php include_partial('criteria_form', array('form' => $form, 'template_id' => $template_id)) ?>
  </div>
  <div class="col-md-3"></div>
</div>