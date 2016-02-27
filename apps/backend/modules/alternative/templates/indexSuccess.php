<?php
/**
 * @var sfWebResponse $sf_response
 * @var sfWebRequest $sf_request
 * @var Decision $decision
 */
$form = new AlternativeForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

$sf_response->setTitle(InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true));
decorate_with('steps_layout');
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array('decision_id' => $decision->getId()));?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
  Overview / <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?></p>
<?php end_slot(); ?>

<?php slot('project_name'); ?>
  <?php echo $decision->name ?>
<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>
  <a id="add-row" data-toggle="tooltip" title="New <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?>" data-placement="bottom" href="javascript:void(0)"  class="btn btn-primary"><i class="fa fa-plus"></i>  Add New</a>
  <a class="btn btn-success" id="excel-export" href="<?php echo url_for('@alternative\excelExport') ?>"><?php echo __('Export') ?></a>
<?php $upload_widget->render('import') ?>
<?php end_slot(); ?>

<?php slot('app_menu'); ?>
  <ul id="menu" class="nav nav-pills small">
    <li class="active">
      <a id="menu-link-alternative" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@alternative?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa-tasks"></i> <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?></p></a>
    </li>
    <?php if ($sf_user->getGuardUser()->account_type !== 'Light'): ?>
      <li<?php echo has_slot('menu_criterion_active') ? ' class="active"' : ($sf_request->hasParameter('decision_id') ? '' : ' class="disabled"') ?>>
        <a id="menu-link-criterion" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@criterion?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa-filter"></i> Criteria</a>
      </li>
    <?php endif; ?>
  </ul>
<?php end_slot(); ?>

<!-- Modal -->
<div class="modal" id="editRowModal" tabindex="-1" role="dialog" aria-labelledby="editRowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="editRowContent">

    </div>
  </div>
</div>

<?php include_component('decision', 'bulkActions') ?>

<div class="table-view-holder-wrapper">
  <div class="table-view-search text-right">
    <input type="text" > <button class="btn">Search</button>
  </div>
  <div id="table-view-holder"></div>
</div>

<script>
$(function () {
  $('.help-info').tooltip();
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

  var rowCollection = new RowCollection,
      tableView     = new DataTableView({ collection: rowCollection });

  $('#table-view-holder').empty().append(tableView.$el);
  tableView.columns = [
    {
      'sTitle': '<input type="checkbox" id="check-all">',
      'mData': 'id',
      'mRender': function(id) {
        return '<input class="check" type="checkbox" name="check['+id+']" value="'+id+'">';
      },
      'sWidth': '15px',
      'bSortable': false,
      'aDataSort': false
    },
    {"title": "Item Name", "data": 'name'},
    {"title": "Description", "data": 'description'},
    {"title": "Tags", "data": 'tags'},
    {"title": "Assigned to", "data": 'assigned_to'},
    {"title": "Working progress", "data": 'work_progress'},
    {"title": "Status", "data": 'status'},
    {"title": "Votes", "data": 'votes'}
  ];
  tableView.render();
  rowCollection.reset(<?php echo $sf_data->getRaw('collection_json') ?>);

  $('#add-row').on('click', function() {
    $.get('<?php echo url_for('alternative\new') ?>', { title: 'New <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?>' }, function (response) {
      tableView.addNew(response);
    });
  });
});
</script>

<style>
  .modal-footer {
    position: absolute;
    left:     0;
    right:    0;
    bottom:   0;
  }

  .modal-tab{
    overflow-y: scroll;
    overflow-x: hidden;
  }

  #table-view .alternative-name {
    word-break: break-all;
    width: 15%;
  }

  #table-view .alternative-description {
    word-break: break-all;
    width: 25%;
  }
</style>