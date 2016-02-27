<?php
/**
 * @var sfWebResponse $sf_response
 * @var UserProfileForm $form
 */

$sf_response->setTitle('My account');
$sf_response->setSlot('disable_menu', true);
?>

<div class="row">
  <div class="col-md-3">
    <?php include_partial("tabs"); ?>
  </div>
  <div class="col-md-5">
    <h1 class="title"><?php echo __('My account') ?></h1>
      <?php //include_component('payment', 'paymentForm') ?>
      <?php include_partial('user_profile/form', array('form' => $form)) ?>
  </div>
</div>
