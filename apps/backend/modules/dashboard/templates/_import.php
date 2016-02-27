<?php
/**
 * @var sfWebResponse $sf_response
 * @var sfWebRequest $sf_request
 * @var Decision $decision
 */
?>

<h3 class="grey-header">Your project is empty</h3>
<p class="grey-header mr-top-25">In order to start to prioritising and analyzing. Please create some items to your project by choosing one of the options below.</p>
<div class="row mr-top-25 import-dashboard">
  <div class="col-md-4">
    <div class="row">
      <div class="col-md-12 text-center mr-top-25">
        <img src="<?php echo image_path('logo-excel.png'); ?>">
      </div>
      <div class="col-md-12 mr-top-25 text-center import-dashboard-button">
        <a title="Upload and Import" href="#" onClick="return false;" style="width: 90%;" class="btn btn-success" data-toggle="modal" data-target="#ExcelModal">Upload and Import</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="row">
      <div class="col-md-12 text-center mr-top-25">
        <img src="<?php echo image_path('logo-trello.png'); ?>">
      </div>
      <div class="col-md-12 mr-top-25 text-center import-dashboard-button">
        <a title="Connect and Import" href="#" onClick="return false;" style="width: 90%; " class="btn btn-success <?php if ($decision->getExternalId()) echo 'disabled' ?>" data-toggle="modal" data-target="#TrelloModal">Connect and Import</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="row">
      <div class="col-md-12 text-center mr-top-50"><h4>Create Manually</h4></div>
      <div class="col-md-12 mr-top-25 text-center import-dashboard-button">
        <a id="add-row" data-toggle="tooltip" title="Create" data-placement="bottom" href="#" onClick="return false;" style="width: 90%;" class="btn btn-success">Create</a>
      </div>
    </div>
  </div>
</div>

<?php if (!$decision->getExternalId()): ?>
  <?php include_component('dashboard', 'importTrello', array(
    'modal_header'  => 'Import ' . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true),
    'url_import'    => url_for('dashboard/importFromTrello', array('decision_id' => $decision->getId()))
  )) ?>
<?php endif; ?>

<?php
include_partial('importExcel', array(
  'modal_header'  => 'Import ' . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true),
  'url_import'    => url_for('@dashboard\importFromExcel', array('decision_id' => $decision->getId()))
));
?>

<script>
  $(function () {
    var applyActionsForAlternative = function ($button) {
      if ($button.data('delete-url')) {
        $('.edit-delete').on('click', function () {
          if (confirm('Are you sure?')) {
            $.ajax({
              url    : $button.data('delete-url'),
              type   : "POST",
              success: function (response) {
                window.location.reload();
              },
              error  : function (response) {

              }
            });
          }
        });
      }

      $('.save_alternative').on('click', function () {
        var
          links_post = [],
          related_alternatives = [],
          due_date = new Date($('#alternative_due_date').val()),
          notify_date = new Date($('#alternative_notify_date').val());

        $('#links').children().each(function () {
          links_post.push({'link': $('.link-input', $(this)).val(), 'id': $('.link-input', $(this)).data('link-id')});
        });

        $('#alternative_related_alternatives option:selected').each(function () {
          related_alternatives.push($(this).val());
        });

        $.ajax({
          url    : $('#save_url').val(),
          type   : "POST",
          data   : {
            "alternative[name]"           : $('#alternative_name').val(),
            "alternative[status]"         : $('#alternative_status').val(),
            "alternative[additional_info]": CKEDITOR.instances['alternative_additional_info'].getData(),
            "alternative[notes]"          : CKEDITOR.instances['alternative_notes'].getData(),
            "alternative[assigned_to]"    : $('#alternative_assigned_to').find('option:selected').val(),
            "alternative[external_id]"    : $('#alternative_external_id').val(),
            "alternative[work_progress]"  : $('#alternative_work_progress').val(),
            "alternative[due_date]"       : $('#alternative_due_date').val() ? due_date.getFullYear() + '-' + (due_date.getMonth() + 1) + '-' + due_date.getDate() : '',
            "alternative[notify_date]"    : $('#alternative_notify_date').val() ? notify_date.getFullYear() + '-' + (notify_date.getMonth() + 1) + '-' + notify_date.getDate() : '',
            "tags"                        : JSON.stringify($(".tags_input").tagsinput('items')),
            "links"                       : JSON.stringify(links_post),
            "related_alternatives"        : JSON.stringify(related_alternatives)
          },
          success: function (response) {
            try {
              response = jQuery.parseJSON(response);
            } catch (e) {
            }

            if (typeof response === 'object') {
              window.location.reload();
            } else {
              editor = CKEDITOR.instances['alternative_additional_info'];
              if (editor) {
                editor.destroy(true);
              }
              editor = CKEDITOR.instances['alternative_notes'];
              if (editor) {
                editor.destroy(true);
              }

              $('#editRowContent').html(response);

              applyActionsForAlternative($button);
            }
          },
          error  : function (response) {
            $('#editRowModal').modal('hide');
          }
        });
      });
    };

    $('#add-row').on('click', function () {
      var $this = $(this);

      $.get('<?php echo url_for('alternative\new') ?>', {title: 'New <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?>'}, function (response) {
        $('#editRowModal').modal('show');

        // Check if ckeditor was created and destroy it
        $.each(['alternative_additional_info', 'alternative_notes', 'decision_objective'], function (index, value) {
          var editor = CKEDITOR.instances[value];
          if (editor) {
            editor.destroy(true);
          }
        });

        $('#editRowContent').html(response);

        applyActionsForAlternative($this);

        $('.modal-tab').css('overflow-y', 'scroll').css('overflow-x', 'hidden');
        $('.modal-footer').css('position', 'absolute').css('left', 0).css('right', 0).css('bottom', 0);
      });
    });
  });
</script>