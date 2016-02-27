<?php
/**
 * @var sfWebResponse $sf_response
 */

$sf_response->setTitle('Responses');
decorate_with('steps_layout');
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array('decision_id' => $decision->getId()));?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
Collaborate / Responses
<?php end_slot(); ?>

<?php slot('project_name'); ?>
<?php echo $decision->name ?>
<?php end_slot(); ?>

<?php slot('app_menu'); ?>
<ul id="menu" class="nav nav-pills small">
  <li class="active">
    <a id="menu-link-response" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@response?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa fa-comment-o"></i> Responses</a>
  </li>

</ul>
<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>
<a class="btn btn-primary" href="<?php echo url_for('@response\export') ?>"><i class="fa fa-download"></i>  <?php echo __('Export') ?></a>
<?php end_slot(); ?>


<?php slot('navigation_links_left'); ?>
<a class="btn btn-primary" href="<?php echo url_for('@wall') ?>"><?php echo __('Back') ?></a>
<?php end_slot(); ?>

<?php slot('navigation_links'); ?>
<a class="steps-navigation step-prev" href="<?php echo url_for('@wall') ?>">&lt;</a>
<?php end_slot(); ?>

<?php slot('section1'); ?>
<?php if ($table->hasData()) : ?>
  <div class="large-table">
    <?php $table->render(); ?>
  </div>

<?php else : ?>
  <div class="alert alert-info">
    No responses
  </div>
<?php endif ?>
<script>
  $(document).ready(function () {
    $('#response-table_wrapper').children(":first").children(":first").html('<div style="float: left;"><label>Number of responses: ' + <?php echo count($table->getBodyData());?> + '</div>');
  });
</script>
<?php end_slot(); ?>
