<?php
/**
 * @var sfWebResponse $sf_response
 * @var TypeTemplateForm $form
 */
?>
<?php $sf_response->setSlot('menu_type_template_active', true) ?>
<?php include_partial('global/menu') ?>

<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-5">
    <h1>New Type template</h1>
    <?php include_partial('form', array('form' => $form)) ?>
  </div>
  <div class="col-md-3"></div>
</div>