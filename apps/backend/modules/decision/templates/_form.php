<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="editRowModalLabel">Edit <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?></h4>
</div>
<div class="modal-body form-horizontal" id="editRowBody">
  <div class="row">
    <div class="col-md-2">
      <ul class="modal-left-menu">
        <li data-tab="#tab-overview"><a href="javascript:void(0)">Overview</a></li>
        <li data-tab="#tab-details"><a href="javascript:void(0)">Details</a></li>
        <li data-tab="#tab-advanced"><a href="javascript:void(0)">Advanced</a></li>
      </ul>
    </div>
    <div class="col-md-10 modal-tabs">
      <div class="modal-tab" id="tab-overview">
        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label"><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?></p> name</label>
            <?php echo $form['name']->renderError() ?>
            <?php echo $form['name']->render(array('class' => 'form-control')) ?>
            <?php echo $form->renderHiddenFields(false) ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label"><?php echo __('Assigned to') ?></label>
            <?php echo $form['assigned_to']->renderError() ?>
            <?php echo $form['assigned_to'] ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label">Status</label>
            <?php echo $form['status']->renderError() ?>
            <?php echo $form['status'] ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label"><?php echo __('Tags') ?></label>
            <?php echo $form['tags'];?>
          </div>
        </div>

        <table>
          <tr>
            <td></td>
            <td>&nbsp;</td>
            <td> <?php if (true || in_array($sf_user->getGuardUser()->account_type, array('Pro', 'Enterprise'))) : ?>
                <?php echo $form['upload']->render() ?>
              <?php else : ?>
                Import is a Pro subscription function. Please upgrade
              <?php endif ?></td>

          </tr>
        </table>
      </div>
      <div class="modal-tab" id="tab-details">
        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label">Project description  <span class="glyphicon glyphicon-info-sign help-info" data-toggle="tooltip"
                                                                                title="What is the goal of your decision?" data-placement="right"></span></label>
            <?php echo $form['objective']->renderError() ?>
            <?php echo $form['objective']->render(array('class' => 'form-control')) ?>
          </div>
        </div>
      </div>
      <div class="modal-tab" id="tab-advanced">
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
          <label class="col-sm-2 control-label">Color:</label>
          <div class="col-sm-10">
            <?php echo $form['color']->renderError() ?>
            <?php echo $form['color']->render(array('class' => 'form-control')) ?>
          </div>
        </div>

        <?php if ($form->getObject()->isNew()): ?>
          <div class="form-group">
            <div class="col-sm-12">
              <label class="control-label modal-label">Type</label>
              <?php echo $form['type_id']->renderError() ?>
              <?php echo $form['type_id'] ?>
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-12">
              <label class="control-label modal-label">Template</label>
              <?php echo $form['template_id']->renderError() ?>
              <?php echo $form['template_id'] ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <?php if (!$form->getObject()->isNew()): ?>
  <div class="pull-left">
    <a href="javascript:void(0)" title="Delete" class="delete btn btn-danger btn-small edit-delete"><i class="glyphicon glyphicon-remove-circle"></i> Delete the <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?></a>
  </div>
  <?php endif; ?>

  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

  <?php if (!$form->getObject()->isNew()): ?>
    <a href="javascript:void(0)" class="btn btn-primary save_project" id="save">Save changes</a>
  <?php else: ?>
    <a href="javascript:void(0)" class="btn btn-primary create_project">Create</a>
  <?php endif; ?>
</div>

<?php if (!$form->getObject()->isNew()): ?>
  <input type="hidden" id="save_url" value="<?php echo url_for('@decision\update?id=' . $form->getObject()->id) ?>">
<?php else: ?>
  <input type="hidden" id="save_url" value="<?php echo url_for('@decision\create') ?>">
<?php endif; ?>

<script type="text/javascript">
  /*<![CDATA[*/
  $(function () {
    var default_text          = '<?php echo 'New ' . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?>',
        $decision_start_date  = $('#decision_start_date'),
        $decision_end_date    = $('#decision_end_date'),
        templates            = <?php echo $form->getTemplatesJson() ?>,
        $template_select     = $('#decision_template_id');

    $('.tags_input').tagsinput({maxChars: 25});
    $('.bootstrap-tagsinput input').removeAttr('style');
    $('.bootstrap-tagsinput').css('width', '100%');

    $('.help-info').tooltip();

    $('#editRowContent').css({height: '500px'});
    $('.modal-footer').css({bottom: 0, left: 0, position: 'absolute', right: 0});
    $('.modal-dialog').css({'margin-top': ($(window).height() - 500) / 2});

    $(window).resize(function(){
      $('.modal-dialog').css({'margin-top': ($(window).height() - 500) / 2});
    });

    $(".modal-left-menu").on("click", "a", function(){
      $(".modal-tab").hide();
      $($(this).closest("li").data('tab')).show();
      $(".modal-left-menu").find('a.active').removeClass('active');
      $(this).addClass('active');
    }).find("a:first").click();

//    $('#decision_color').colorpicker();
    $('#decision_color > option').each(function(){
      $(this).data('color', $(this).val());
    });
    $('#decision_color').colorselector();

    // Rename node in the tree when on the name edit
    $('#decision_name').on('change', function () {
      if (!$(this).val()) {
        $(this).val(default_text);
      }
    }).on('focus', function () {
      if ($(this).val() == default_text) {
        $(this).val('');
      }
    });

    // Excel import
    $('#fileupload').fileupload({
      add: function(e, data) {
        var uploadErrors = [];
        var acceptFileTypes = /^application\/(vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet|vnd\.ms-excel|msexcel|x-msexcel|x-ms-excel|x-excel|x-dos_ms_excel|xls|x-xls)$/;
        if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
          uploadErrors.push('Not an accepted file type (.xls or .xlsx)');
        }
        if(data.originalFiles[0]['size'].length && data.originalFiles[0]['size'] > 20000000) {
          uploadErrors.push('File size is too big');
        }
        if(uploadErrors.length > 0) {
          alert(uploadErrors.join("\n"));
        } else {
          data.submit();
        }
      },
      url            : '<?php echo url_for('@decision\upload?id=' . $form->getObject()->id) ?>',
      autoUpload     : true
    }).on('fileuploaddone', function () {
      alert('The file was successfully imported');
    }).on('fileuploadfail', function () {
      alert('There was a problem with the import of your file');
    });

    var $input_start_date = $decision_start_date.pickadate({formatSubmit: 'yyyy/mm/dd', container: '#editRowModal'});
    var picker_start_date = $input_start_date.data('pickadate');
    $decision_start_date.closest('.input-group').find('.calendar-group-icon').click(function(e){
      e.stopPropagation();
      e.preventDefault();
      picker_start_date.open();
    });

    var $input_end_date = $decision_end_date.pickadate({formatSubmit: 'yyyy/mm/dd', container: '#editRowModal'});
    var picker_end_date = $input_end_date.data('pickadate');
    $decision_end_date.closest('.input-group').find('.calendar-group-icon').click(function(e){
      e.stopPropagation();
      e.preventDefault();
      picker_end_date.open();
    });

    $('#decision_type_id :nth-child(2)').prop('selected', true);

    $('#decision_type_id').on('change', function () {
      var type_id = $(this).val(),
          type_templates = templates[type_id];

      $template_select.html('');
      for (var template_id in type_templates) {
        $template_select.append($('<option/>').val(template_id).text(type_templates[template_id]));
      }

      $template_select.trigger('change');
    }).change();

    $('.modal-tab').css({height: 360});
  });
  /*]]>*/
</script>

<style>
  .btn-colorselector {
    border: 1px solid black;
  }
</style>
