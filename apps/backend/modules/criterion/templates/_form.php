<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="editRowModalLabel"><?php echo (!isset($type) || $type !== 'new')? "Edit criteria" : "Create criteria"; ?></h4>
</div>
<div class="modal-body form-horizontal modal-criterion-edit" id="editRowBody">
  <div class="form-group">
    <label class="col-sm-2 control-label"><?php echo __('Criteria name') ?></label>
    <div class="col-sm-10">
      <?php echo $form->renderGlobalErrors() ?>
      <?php echo $form['name']->renderError() ?>
      <?php echo $form['name']->render() ?>
      <?php echo $form->renderHiddenFields(false) ?>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label"><?php echo __('Description') ?></label>
    <div class="col-sm-10">
      <?php echo $form['description']->renderError() ?>
      <?php echo $form['description']->render() ?>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label"><?php echo __('Type') ?></label>
    <div class="col-sm-10">
      <?php echo $form['variable_type']->renderError() ?>
      <?php echo $form['variable_type']->render() ?>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label"><?php echo __('Measure') ?></label>
    <div class="col-sm-10">
      <?php echo $form['measurement']->renderError() ?>
      <?php echo $form['measurement']->render() ?>
    </div>
  </div>

  <?php if (!isset($type) || $type !== 'new'): ?>
    <input type="hidden" id="save_url" value="<?php echo url_for('@criterion\update?id=' . $form->getObject()->id) ?>">
  <?php else: ?>
    <input type="hidden" id="save_url" value="<?php echo url_for('@criterion\create?decision_id=' . $form->getObject()->Decision->id) ?>">
  <?php endif; ?>
</div>
<div class="modal-footer">
  <?php if (!isset($type) || $type !== 'new'): ?>
    <div class="pull-left">
      <a href="javascript:void(0)" title="Delete" class="delete btn btn-danger btn-small edit-delete"><i lass="glyphicon glyphicon-remove-circle"></i> Delete the criteria</a>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary save_criterion" id="save">Save changes</button>
  <?php else: ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary save_criterion" id="save">Create</button>
  <?php endif; ?>
</div>

<script type="text/javascript">
  /*<![CDATA[*/
  $(function () {
    $('#editRowContent').css({height: '450px'});
    $('.modal-footer').css({bottom: 0, left: 0, position: 'absolute', right: 0});
    $('.modal-criterion-edit').closest('.modal-dialog').css({'margin-top': ($(window).height() - 450) / 2});

    $(window).resize(function(){
      $('.modal-criterion-edit').closest('.modal-dialog').css({'margin-top': ($(window).height() - 450) / 2});
    });

    // Popovers initialization.
    $('.help-info').tooltip();

    var variable_type = $('#criterion_variable_type');

    $('#criterion_measurement').change(function () {
      if ($(this).val() == 'comment') {
        variable_type.val('Info');
        variable_type.attr('disabled', true);
      } else {
        variable_type.removeAttr('disabled');
      }
    }).change();

    var default_text = '<?php echo __('New criterion') ?>';

    $('#criterion_name').on('change', function () {
      if (!$(this).val()) {
        $(this).val(default_text);
      }
    }).on('focus', function () {
      if ($(this).val() == default_text) {
        $(this).val('');
      }
    });
  });
  /*]]>*/
</script>