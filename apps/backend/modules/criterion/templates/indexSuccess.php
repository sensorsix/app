<?php
foreach ($upload_widget->getStylesheets() as $stylesheet => $media)
{
  use_stylesheet($stylesheet);
}
foreach ($upload_widget->getJavaScripts() as $script)
{
  use_javascript($script);
}
$form = new CriterionForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

$sf_response->setTitle('Criteria');
decorate_with('steps_layout');
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array('decision_id' => $decision->getId()));?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
Overview / Criterias
<?php end_slot(); ?>

<?php slot('project_name'); ?>
<?php echo $decision->name ?>
<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>
<a id="add-row" href="javascript:void(0)"  class="btn btn-primary dropdown-toggle"><i class="fa fa-plus"></i>  Add New</a>

<div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
    Add from Template <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <?php foreach ($popularCriteria as $popularCriterion) : ?>
      <li >
        <a href="<?php echo url_for('@criterion\createPopular?id=' . $popularCriterion->id) ?>" class="popular-criterion">
          <?php echo $popularCriterion->name ?>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
</div>
<?php // not working commented out by JP 14/7/14 $upload_widget->render('import') ?>

<?php end_slot(); ?>

<?php slot('app_menu'); ?>
<ul id="menu" class="nav nav-pills small">
  <li<?php echo has_slot('menu_alternative_active') ? ' class="active"' : ($sf_request->hasParameter('decision_id') ? '' : ' class="disabled"') ?>>
    <a id="menu-link-alternative" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@alternative?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa-tasks"></i> <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?></p></a>
  </li>
  <li class="active">
    <a id="menu-link-criterion" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@criterion?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa-filter"></i> Criteria</a>
  </li>
</ul>
<?php end_slot(); ?>

<!-- Modal -->
<div class="modal" id="editRowModal" tabindex="-1" role="dialog" aria-labelledby="editRowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="editRowContent">

    </div>
  </div>
</div>

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
      url            : '<?php echo url_for('@criterion\import') ?>',
      autoUpload     : true
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
      {"title": "Criteria name", "data": 'name'},
      {"title": "Description", "data": 'description'},
      {"title": "Type", "data": 'type'},
      {"title": "Measure to", "data": 'measure'}
    ];
    tableView.render();
    rowCollection.reset(<?php echo $sf_data->getRaw('collection_json') ?>);

    $('#add-row').on('click', function() {
      $.get('<?php echo url_for('criterion\new') ?>', { title: 'New criterion' }, function (response) {
        tableView.addNew(response);
      });
    });

    $('.popular-criterion').on('click', function () {
      $.get(this.href, function (response) {
        tableView.addNew(response);
      });
      return false;
    });
  });
</script>