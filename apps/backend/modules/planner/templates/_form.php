<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="editRowModalLabel"><?php echo ((!isset($type) || $type !== 'new')? "Edit " : "Create ") . 'Release' ?></h4>
</div>

<div class="modal-body form-horizontal modal-release-edit" id="editRowBody">
  <div class="row">
    <div class="col-md-12 modal-tabs">

      <div class="form-group">
        <div class="col-sm-12">
          <?php echo $form->renderGlobalErrors() ?>
          <label class="control-label modal-label">Release name</label>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name']->render(array('class' => 'form-control')) ?>
          <?php echo $form->renderHiddenFields(false) ?>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-12">
          <label class="control-label modal-label"><?php echo __('Status') ?></label>
          <?php echo $form['status']->renderError() ?>
          <?php echo $form['status']->render(array('class' => 'form-control')) ?>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-12">
          <label class="control-label modal-label">Start date</label>
          <?php echo $form['start_date']->renderError() ?>
          <div class="input-group">
            <span class="glyphicon glyphicon-calendar input-group-addon calendar-group-icon" aria-hidden="true"></span>
            <?php echo $form['start_date']->render(array('class' => 'form-control')) ?>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-12">
          <label class="control-label modal-label">End date</label>
          <?php echo $form['end_date']->renderError() ?>
          <div class="input-group">
            <span class="glyphicon glyphicon-calendar input-group-addon calendar-group-icon" aria-hidden="true"></span>
            <?php echo $form['end_date']->render(array('class' => 'form-control')) ?>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-12">
          <label class="control-label modal-label"><?php echo __('Tags') ?></label>
          <?php echo $form['tags']; ?>
        </div>
      </div>

      <div class="form-group">
        <?php if (!isset($type) || $type !== 'new'): ?>
          <input type="hidden" id="save_url_release" value="<?php echo url_for('@planner2\updateRelease?id=' . $form->getObject()->id) ?>">
        <?php else: ?>
          <input type="hidden" id="save_url_release" value="<?php echo url_for('@planner2\createRelease?decision_id=' . $form->getObject()->Decision->id) ?>">
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<div class="modal-footer">
  <?php if (!isset($type) || $type !== 'new'): ?>
    <div class="pull-left">
      <a href="javascript:void(0)" title="Delete" data-release_id="<?php echo $form->getObject()->id ?>" data-criterion_id="<?php echo $form->getObject()->criterion_id ?>" class="btn btn-danger btn-small release-delete"><i lass="glyphicon glyphicon-remove-circle"></i> Delete the Release</a>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary save_release" id="save" data-criterion_id="<?php echo $form->getObject()->criterion_id ?>">Save changes</button>
  <?php else: ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary save_release" id="save" data-criterion_id="<?php echo $form->getObject()->criterion_id ?>">Create</button>
  <?php endif; ?>
</div>

<script type="text/javascript">
  /*<![CDATA[*/
  $(function () {
    var default_text        = '<?php echo __('New Release') ?>',
      $release_start_date = $('#project_release_start_date'),
      $release_end_date   = $('#project_release_end_date')
    ;

    $('.tags_input').tagsinput({maxChars: 25});
    $('.bootstrap-tagsinput input').removeAttr('style');
    $('.bootstrap-tagsinput').css('width', '100%');

    // Rename node in the tree when on the name edit
    $('#project_release_name').on('change', function () {
      if (!$(this).val()) {
        $(this).val(default_text);
      }
    }).on('focus', function () {
      if ($(this).val() == default_text) {
        $(this).val('');
      }
    }).on('keyup', function (e) {
      if (e.keyCode == 13) {
        $('a.add').trigger('click');
      }
    });

    <?php if (!isset($type) || $type !== 'new'): ?>
    $('.save_release').click(function(){
      $('.start').each(function(){
        $(this).find('button').click();
      });
    });
    <?php endif; ?>

    var $input_start_date = $release_start_date.pickadate({formatSubmit: 'yyyy/mm/dd', container: '#editRowModal'});
    var picker_start_date = $input_start_date.data('pickadate');
    $release_start_date.closest('.input-group').find('.calendar-group-icon').click(function(e){
      e.stopPropagation();
      e.preventDefault();
      picker_start_date.open();
    });

    var $input_end_date = $release_end_date.pickadate({formatSubmit: 'yyyy/mm/dd', container: '#editRowModal'});
    var picker_end_date = $input_end_date.data('pickadate');
    $release_end_date.closest('.input-group').find('.calendar-group-icon').click(function(e){
      e.stopPropagation();
      e.preventDefault();
      picker_end_date.open();
    });

    $('.modal-release-edit')
        .closest('.modal-content').css({height: $(window).height() - 60}).end()
        .closest('.modal-dialog').css({'margin-top': '30px', 'margin-bottom': '30px'});

    $( window ).resize(function() {
      $('.modal-release-edit')
          .closest('.modal-content').css({height: $(window).height() - 60}).end()
          .closest('.modal-dialog').css({'margin-top': '30px', 'margin-bottom': '30px'});
      $('.modal-tab').css({height: $(window).height() - 210 });
    });
  });
  /*]]>*/
</script>