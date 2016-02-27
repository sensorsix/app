<div id="logical-filter-form-container">
  <form id="logical-filter-form" class="form-inline">
    <div class="form-row">
    <?php echo $form['criterion_id']->render(array('class' => 'form-control col-md-span1')) ?>
    <?php echo $form['logical_operator']->render(array('class' => 'form-control col-md-span1')) ?>
    <?php echo $form['value']->render(array('class' => 'form-control col-md-span2')) ?>
    <?php echo $form->renderHiddenFields() ?>
    <a class="btn btn-primary" id="logical-filter-add-button" href="javascript:void(0)"><?php echo __('Add') ?></a>
    </div>
  </form>
</div>