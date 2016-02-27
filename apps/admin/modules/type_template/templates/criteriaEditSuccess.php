<?php
/**
 * @var sfWebResponse $sf_response
 * @var TypeTemplateForm $form
 * @var CriteriaTemplate[] $criteria
 */
?>
<?php $sf_response->setSlot('menu_type_template_active', true) ?>
<?php include_partial('global/menu') ?>

<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-5">
    <h1>Edit Criteria template</h1>
    <?php include_partial('criteria_form', array('form' => $form)) ?>
  </div>
  <div class="col-md-3"></div>
</div>