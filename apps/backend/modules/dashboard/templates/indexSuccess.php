<?php
/**
 * @var Dashboard $dashboard
 * @var sfWebResponse $sf_response
 * @var sfWebRequest $sf_request
 * @var Decision $decision
 * @var StackedBarChart $stackedBarChart
 * @var CumulativeGainChart $cumulativeChart
 *
 */

$sf_response->setTitle('Dashboard');
decorate_with('steps_layout');
use_helper('Escaping');

$form = new AlternativeForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array('decision_id' => $decision->getId()));?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
Overview
<?php end_slot(); ?>

<?php slot('project_name'); ?>
<?php echo $decision->name ?>  - <a href="#" onClick="return false;" class="edit-decision" data-edit-url = "<?php echo url_for('decision\edit', array('id' => $decision->getId())) ?>" data-delete-url = "<?php echo url_for('decision\delete', array('id' => $decision->getId())) ?>">Edit</a>
<?php end_slot(); ?>

<?php slot('app_menu'); ?>
  <ul id="menu" class="nav nav-pills small">
    <li<?php echo has_slot('menu_alternative_active') ? ' class="active"' : ($sf_request->hasParameter('decision_id') ? '' : ' class="disabled"') ?>>
      <a id="menu-link-alternative" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@alternative?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa-tasks"></i> <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?></p></a>
    </li>
    <?php if ($sf_user->getGuardUser()->account_type !== 'Light'): ?>
    <li<?php echo has_slot('menu_criterion_active') ? ' class="active"' : ($sf_request->hasParameter('decision_id') ? '' : ' class="disabled"') ?>>
      <a id="menu-link-criterion" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@criterion?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa-filter"></i> Criteria</a>
    </li>
    <?php endif; ?>
  </ul>
<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>
  <?php if (count($dashboard->getBodyData())): ?>
    <a id="add-row" data-toggle="tooltip" title="New <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?>" data-placement="bottom" href="#" onClick="return false;"  class="btn btn-success"><i class="fa fa-plus"></i>  Add New</a>
    <a class="btn btn-success" id="excel-export" href="<?php echo url_for('@alternative\excelExport') ?>"><?php echo __('Export') ?></a>
    <?php if (count($dashboard->getBodyData())): ?>
      <span id="fileupload-top-modal">
        <a data-toggle="tooltip" title="" data-placement="bottom" class="btn btn-default fileinput-button help-info" data-original-title="Import">
          <i class="fa fa-upload"></i> Import
        </a>
      </span>
    <?php else: ?>
      <?php $upload_widget->render('import') ?>
    <?php endif; ?>
  <?php endif; ?>
<?php end_slot(); ?>

<?php if (count($dashboard->getBodyData())): ?>
  <?php include_partial('table_with_graph', array(
    'stackedBarChart'   => $stackedBarChart,
    'cumulativeChart'   => $cumulativeChart,
    'dashboard'         => $dashboard,
    'decision'          => $decision,
  )); ?>
  <?php include_partial('importModal', array(
      'decision'          => $decision
  )); ?>
<?php else: ?>
  <?php include_partial('import', array(
      'decision'          => $decision
  )); ?>
<?php endif; ?>

<!-- Modal -->
<div class="modal" id="editRowModal" tabindex="-1" role="dialog" aria-labelledby="editRowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="editRowContent">

    </div>
  </div>
</div>

<script>
  $(function () {
    // Excel import
    $('#fileupload-top').fileupload({
      add: function(e, data) {
        var uploadErrors = [];
        var acceptFileTypes = /^application\/(vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet|vnd\.ms-excel|msexcel|x-msexcel|x-ms-excel|x-excel|x-dos_ms_excel|xls|x-xls)$/;
        if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
          uploadErrors.push('Not an accepted file type (.xls or .xlsx)' + data.originalFiles[0]['type']);
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
      url               : '<?php echo url_for('@alternative\import') ?>',
      autoUpload        : true,
      uploadTemplateId  : 'template-upload-top',
      downloadTemplateId: 'template-download-top',
      pasteZone         : null
    }).on('fileuploaddone', function () {
      alert('The file was successfully imported');
      window.location = window.location;
    }).on('fileuploadfail', function () {
      alert('There was a problem with the import of your file');
    });

    var applyActionsForDecision = function($button){
      $('.edit-delete').on('click', function(){
        if (confirm('Are you sure?')) {
          $.ajax({
            url : $button.data('delete-url'),
            type: "POST",
            success : function (response) {
              window.location.href = '<?php echo url_for('@decision'); ?>';
            },
            error: function(response){

            }
          });
        }
      });

      $('.save_project').on('click', function(){
        var start_date  = new Date($('#decision_start_date').val()),
          end_date    = new Date($('#decision_end_date').val());

        $.ajax({
          url : $('#save_url').val(),
          type: "POST",
          dataType: 'json',
          data: {
            "decision[name]"        : $('#decision_name').val(),
            "decision[assigned_to]" : $('#decision_assigned_to').val(),
            "decision[objective]"   : CKEDITOR.instances['decision_objective'].getData(),
            "decision[start_date]"  : $('#decision_start_date').val() ? start_date.getFullYear() + '-' + (start_date.getMonth() + 1) + '-' + start_date.getDate() : '',
            "decision[end_date]"    : $('#decision_end_date').val() ? end_date.getFullYear() + '-' + (end_date.getMonth() + 1) + '-' + end_date.getDate() : '',
            "decision[color]"       : $('#decision_color > option:selected').val(),
            "decision[status]"      : $('#decision_status').val(),
            "tags"                  : JSON.stringify($(".tags_input").tagsinput('items'))
          },
          success : function (response) {
            if (_.has(response, 'status') && response.status === 'error'){
              // Check if ckeditor was created and destroy it
              var editor = CKEDITOR.instances['decision_objective'];
              if (editor) { editor.destroy(true); }

              $('#editRowContent').html(response.html);

              applyActionsForDecision();
            }else{
              window.location.reload();
            }
          }
        });
      });
    };

    $('.edit-decision').on('click', function(){
      var $this = $(this);

      $.get($this.data('edit-url'), function(response) {
        $('#editRowModal').modal('show');

        // Check if ckeditor was created and destroy it
        $.each([ 'alternative_additional_info', 'alternative_notes', 'criterion_description', 'decision_objective' ], function( index, value ) {
          var editor = CKEDITOR.instances[value];
          if (editor) { editor.destroy(true); }
        });

        $('#editRowContent').html(response);

        applyActionsForDecision($this);
      });
    });
  });
</script>
