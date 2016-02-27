<?php
$custom_fields = json_decode($form['custom_fields']->getValue());
?>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="editRowModalLabel"><?php echo ((!isset($type) || $type !== 'new')? "Edit " : "Create ") . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE); ?></h4>
</div>
<div class="modal-body form-horizontal modal-alternative-edit" id="editRowBody">
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
            <?php echo $form->renderGlobalErrors() ?>
            <label class="control-label modal-label"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?> name</label>
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
            <label class="control-label modal-label">Work progress: </label> <span id="range_percentage"></span>
            <?php echo $form['work_progress']->renderError() ?>
            <?php echo $form['work_progress']->render(array('class' => 'form-control')) ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label">Assigned to</label>
            <?php echo $form['assigned_to']->renderError() ?>
            <?php echo $form['assigned_to']->render(array('class' => 'form-control')) ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label">Due date</label>
            <?php echo $form['due_date']->renderError() ?>
            <div class="input-group">
              <span class="glyphicon glyphicon-calendar input-group-addon calendar-group-icon" aria-hidden="true"></span>
              <?php echo $form['due_date']->render(array('class' => 'form-control')) ?>
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label">Notify date</label>
            <?php echo $form['notify_date']->renderError() ?>
            <div class="input-group">
              <span class="glyphicon glyphicon-calendar input-group-addon calendar-group-icon" aria-hidden="true"></span>
              <?php echo $form['notify_date']->render(array('class' => 'form-control')) ?>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-tab" id="tab-details">
        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label"><?php echo __('Description') ?></label>
            <?php echo $form['additional_info']->renderError() ?>
            <?php echo $form['additional_info']->render(array('class' => 'form-control')) ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label"><?php echo __('Notes') ?></label>
            <?php echo $form['notes']->renderError() ?>
            <?php echo $form['notes']->render(array('class' => 'form-control')) ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label"><?php echo __('Links') ?></label>
            <div class="row-fuild">
              <div class="mb10">
                <a id="add-link-button" class="btn btn-primary" href="javascript:void(0)"><?php echo __('Add link') ?></a>
              </div>
            </div>
            <div id="links">
              <?php $link_number = 1; ?>
              <?php foreach ($form->getObject()->AlternativeLink as $link) : ?>
                <?php include_partial('link_field', array('link' => $link, 'link_number' => $link_number)) ?>
                <?php $link_number++; ?>
              <?php endforeach ?>
            </div>
          </div>
        </div>

        <?php if (!isset($type) || $type !== 'new'): ?>
          <div class="form-group">
            <div class="col-sm-12">
              <label class="control-label modal-label"><?php echo __('Files') ?></label>
              <?php echo $form['upload']->render(array('class' => 'form-control')) ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label"><?php echo __('Tags') ?></label>
            <?php echo $form['tags']; ?>
          </div>
        </div>

      </div>

      <div class="modal-tab" id="tab-advanced">
        <?php if (!isset($type) || $type !== 'new'): ?>
          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo __('Created at:') ?></label>
            <div class="col-sm-9" style="padding-top: 7px;">
              <?php echo $form->getObject()->created_at ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo __('Created by:') ?></label>
            <div class="col-sm-9" style="padding-top: 7px;">
              <?php echo $form->getObject()->created_by ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo __('Last change at:') ?></label>
            <div class="col-sm-9" style="padding-top: 7px;">
              <?php echo $form->getObject()->updated_at ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label"><?php echo __('Last change by:') ?></label>
            <div class="col-sm-9" style="padding-top: 7px;">
              <?php echo $form->getObject()->updated_by ?>
            </div>
          </div>
        <?php endif; ?>

        <div class="form-group">
          <label class="col-sm-3 control-label"><?php echo __('Votes:') ?></label>
          <div class="col-sm-9">
            <?php echo $form->getObject()->score ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label">External ID:</label>
            <?php echo $form['external_id']->renderError() ?>
            <?php echo $form['external_id']->render(array('class' => 'form-control', 'maxlength' => 50)) ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label">Relationships to other <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?></p>:</label>
            <?php echo $form['related_alternatives']->renderError() ?>
            <div class="input-group">
              <span class="glyphicon glyphicon-search input-group-addon calendar-group-icon" id="related_alternatives_search_button" aria-hidden="true"></span>
              <?php echo $form['related_alternatives']->render(array('class' => 'form-control')) ?>
            </div>
          </div>
        </div>

        <?php if (count($custom_fields)) foreach ($custom_fields as $key => $value): ?>
          <div class="col-sm-12 text-muted">
            <label class="control-label modal-label"><?php echo $key; ?></label>
            <p><?php echo $value; ?></p>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="form-group">
        <div class="inner-cell">
          <span id="fetch-url" data-url="<?php echo url_for('@alternative\fetch?id=' . $form->getObject()->id) ?>"></span>
        </div>

        <?php if (!isset($type) || $type !== 'new'): ?>
          <input type="hidden" id="save_url" value="<?php echo url_for('@alternative\update?id=' . $form->getObject()->id) ?>">
        <?php else: ?>
          <input type="hidden" id="save_url" value="<?php echo url_for('@alternative\create?decision_id=' . $form->getObject()->Decision->id) ?>">
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
<div class="modal-footer">
  <?php if (!isset($type) || $type !== 'new'): ?>
    <div class="pull-left">
      <a href="javascript:void(0)" title="Delete" class="delete btn btn-danger btn-small edit-delete"><i lass="glyphicon glyphicon-remove-circle"></i> Delete the <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?></a>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary save_alternative" id="save">Save changes</button>
  <?php else: ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary save_alternative" id="save">Create</button>
  <?php endif; ?>
</div>

<style>
  .select2-close-mask {
    z-index: 10001;
  }

  .select2-dropdown {
    z-index: 10002;
  }
</style>

<script type="text/javascript">
  /*<![CDATA[*/
  $(function () {
    var default_text               = '<?php echo __('New') . ' ' . strtolower($form->getObject()->Decision->getAlternativeAlias()) ?>',
        delete_file                = [],
        link_number                = <?php echo $link_number; ?>,
        $alternative_work_progress = $("#alternative_work_progress"),
        $range_percentage          = $("#range_percentage"),
        $alternative_due_date      = $('#alternative_due_date'),
        $alternative_notify_date   = $('#alternative_notify_date');

    $('.tags_input').tagsinput({maxChars: 25});
    $('.bootstrap-tagsinput input').removeAttr('style');
    $('.bootstrap-tagsinput').css('width', '100%');

    // Popovers initialization.
    $('.help-info').tooltip();

    // Rename node in the tree when on the name edit
    $('#alternative_name').on('change', function () {
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
    $('#fileupload').fileupload({
      url        : '<?php echo url_for('@alternative\upload?id=' . $form->getObject()->id) ?>',
      maxFileSize: 20000000,
      autoUpload : false
    });

    // Load existing files:
    $('#fileupload').each(function () {
      var that = this;
      $.getJSON(
        $(this).fileupload('option', 'url'),
        function (result) {
          if (result && result.length) {
            $(that).fileupload('option', 'done').call(that, null, {result: result});
          }
        }
      );
    });

    $('#fileupload').bind('fileuploaddestroy', function (e, data) {
      delete_file.push(data.url);
      data.url = '';
    });

    $('.save_alternative').click(function(){
      $('.start').each(function(){
        $(this).find('button').click();
      });

      delete_file.forEach(function(url, i){
        $.ajax({
          'url'  : url,
          'type' : 'delete'
        });
      });
    });
    <?php endif; ?>

    $('#add-link-button').click(function () {
      $('#links').append($('<div id="alternative-link-' + link_number + '" class="form-group"><div class="col-xs-12"><a class="link-delete glyphicon glyphicon-remove pull-right" data-id="' + link_number + '" title="<?php echo __('Delete') ?>" href="javascript:void(0);"></a><div class="field-wrapper"><input type="text" value="" name="alternative_link[]" class="form-control link-input"/></div></div></div>'));
      bindLinkDelete();
      link_number++;
    });

    function bindLinkDelete() {
      $('.link-delete').unbind('click').on('click', function () {
        if (confirm('<?php echo 'You are about to delete this ' . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) . '. Press Ok to continue.' ?>')) {
          $('#alternative-link-' + $(this).data('id')).remove();
        }
      });
    }

    bindLinkDelete();

    $(".modal-left-menu").on("click", "a", function(){
      $(".modal-tab").hide();
      $($(this).closest("li").data('tab')).show();
      $(".modal-left-menu").find('a.active').removeClass('active');
      $(this).addClass('active');

      if ($(this).closest("li").data('tab') == '#tab-advanced') {
        $("#alternative_related_alternatives").select2();
        $('.select2-selection').css({'border-bottom-left-radius': 0, 'border-top-left-radius': 0});
      }
    }).find("a:first").click();

    $range_percentage.text($alternative_work_progress.val() + '%');
    $alternative_work_progress.on('change mousemove', function(){ $range_percentage.text($(this).val() + '%'); });

    var $input_due_date = $alternative_due_date.pickadate({formatSubmit: 'yyyy/mm/dd', container: '#editRowModal'});
    var picker_due_date = $input_due_date.data('pickadate');
    $alternative_due_date.closest('.input-group').find('.calendar-group-icon').click(function(e){
      e.stopPropagation();
      e.preventDefault();
      picker_due_date.open();
    });

    var $input_notify_date = $alternative_notify_date.pickadate({formatSubmit: 'yyyy/mm/dd', container: '#editRowModal'});
    var picker_notify_date = $input_notify_date.data('pickadate');
    $alternative_notify_date.closest('.input-group').find('.calendar-group-icon').click(function(e){
      e.stopPropagation();
      e.preventDefault();
      picker_notify_date.open();
    });

    $('#related_alternatives_search_button').on('click', function(){
      $("#alternative_related_alternatives").select2('open');
    });

    $('.modal-tab').css({height: $(window).height() - 210});
    $('.modal-alternative-edit')
      .closest('.modal-content').css({height: $(window).height() - 60}).end()
      .closest('.modal-dialog').css({'margin-top': '30px', 'margin-bottom': '30px'});

    $( window ).resize(function() {
      $('.modal-alternative-edit')
        .closest('.modal-content').css({height: $(window).height() - 60}).end()
        .closest('.modal-dialog').css({'margin-top': '30px', 'margin-bottom': '30px'});
      $('.modal-tab').css({height: $(window).height() - 210 });
    });
  });
  /*]]>*/
</script>