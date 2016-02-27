<?php
/**
 * @var $decision Decision
 * @var $sf_request sfWebRequest
 * @var $sf_response sfWebResponse
 * @var $upload_widget laWidgetFileUpload
 */

foreach ($upload_widget->getStylesheets() as $stylesheet => $media)
{
  use_stylesheet($stylesheet);
}
foreach ($upload_widget->getJavaScripts() as $script)
{
  use_javascript($script);
}

decorate_with('steps_layout');
$sf_response->setTitle('Collaborate');
$form = new RoleForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array('decision_id' => $decision->getId()));?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
  Collaborate
<?php end_slot(); ?>

<?php slot('project_name'); ?>
<?php echo $decision->name ?>
<?php end_slot(); ?>

<?php slot('app_menu'); ?>
<ul id="menu" class="nav nav-pills small">
  <li<?php echo has_slot('menu_alternative_active') ? ' class="active"' : ($sf_request->hasParameter('decision_id') ? '' : ' class="disabled"') ?>>
    <a id="menu-link-response" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@response?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa fa-comment-o"></i> Responses</a>
  </li>
</ul>
<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>
  <?php if ($sf_user->getGuardUser()->account_type !== 'Light'): ?>
    <a id="add-row" href="javascript:void(0)" class="btn btn-primary"><i class="fa fa-plus"></i>  Add new</a>
  <?php endif; ?>
<?php end_slot(); ?>

<?php slot('navigation_links_left'); ?>
  <a class="btn btn-primary" href="<?php echo url_for('@criterion') ?>"><?php echo __('Back') ?></a>
  <a class="btn btn-primary" href="<?php echo url_for('@analyze') ?>"><?php echo __('Next') ?></a>
<?php end_slot();?>

<?php slot('navigation_links'); ?>
  <a class="steps-navigation step-prev" href="<?php echo url_for('@criterion') ?>">&lt;</a>
  <a class="steps-navigation step-next" href="<?php echo url_for('@analyze') ?>">&gt;</a>
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

    var rowCollection = new RowCollection,
        tableView     = new DataTableView({ collection: rowCollection }),
        client;

    $('#table-view-holder').empty().append(tableView.$el);
    tableView.columns = [
      {"title": "Name", "data": 'name'},
      {"title": "Activity", "data": 'responses_count'},
      {"title": "Last update", "data": 'updated_at'},
      {"title": "Workspace link", "data": 'workspace_link'}
    ];
    tableView.render();
    rowCollection.reset(<?php echo $sf_data->getRaw('collection_json') ?>);

    $('#add-row').on('click', function() {
      $.get('<?php echo url_for('role\new') ?>', { title: 'New role' }, function (response) {
        tableView.addNew(response);
      });
    });

    var drawGoButton = function(){
      $('.survey-link-cell').hover(function() {
        $(this).find('.go-button').css( "visibility", "visible" );
      }, function() {
        $(this).find('.go-button').css( "visibility", "hidden" );
      });
    };

    drawGoButton();

    $('#editRowModal').on('hidden.bs.modal', function (e) {
      client = new ZeroClipboard($(".copy-button"));
    }).on('shown.bs.modal', function (e) {
      if (typeof oTable === 'object'){
        oTable.fnAdjustColumnSizing();
      }
      drawGoButton();
    }).on('hidden.bs.modal', function (e) {
      drawGoButton();
    });

    client = new ZeroClipboard($(".copy-button"));

    $('.modal-content').css({height: $(window).height() - 60, 'margin-top': '30px', 'margin-bottom': '30px'});

    $( window ).resize(function() {
      $('.modal-content').css({height: $(window).height() - 60, 'margin-top': '30px', 'margin-bottom': '30px'});
      var tab_height = $(window).height() - 250;
      if ($('#disabled-hint').is(':visible')){tab_height -= 66; }
      $('.modal-tab').css({height: tab_height });
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
</style>
