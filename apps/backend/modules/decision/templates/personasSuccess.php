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
Personas
<?php end_slot(); ?>

<p class="lead"> Build the persona along with demographic data, photos etc and share it in powerpoints or through a URL, so the rest of the company is aligned with who you are building for
</p>
<p>If you want to be part of a closed beta, please sign up here <a href="mailto:info@sensorsix.com">info@sensorsix.com</a></p>
