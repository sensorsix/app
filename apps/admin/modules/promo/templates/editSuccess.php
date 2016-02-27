<?php
/**
 * @var sfWebResponse $sf_response
 * @var PromoCodeForm $form
 */
?>
<?php $sf_response->setSlot('menu_promo_active', true) ?>
<?php include_partial('global/menu') ?>

<div class="row">
  <div class="col-md-3"></div>
  <div class="col-md-5">
    <h1>Edit Promo Code</h1>
    <?php include_partial('form', array('form' => $form)) ?>
  </div>
  <div class="col-md-3"></div>
</div>