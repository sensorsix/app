<?php
/**
 * @var sfWebResponse $sf_response
 */

decorate_with('steps_layout');
?>

<?php slot('sidebar'); ?>
<?php include_partial("global/leftSidebar", array());?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
Portfolio
<?php end_slot(); ?>

<p class="lead">Create and manage your projects or products as porfolios, product lines or similar</p>

<p>If you want to be part of a closed beta, please sign up here <a href="mailto:info@sensorsix.com">info@sensorsix.com</a></p>
