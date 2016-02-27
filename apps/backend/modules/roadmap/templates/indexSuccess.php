<?php
/**
 * @var sfWebResponse $sf_response
 * @var sfWebRequest $sf_request
 */

$form = new RoadmapForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

$sf_response->setTitle('Roadmap');
decorate_with('steps_layout');
use_helper('Escaping');
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array());?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
  Roadmaps overview
<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>
  <a id="add-row" data-toggle="tooltip" title="New <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?>" data-placement="bottom" href="javascript:void(0)"  class="btn btn-primary"><i class="fa fa-plus"></i>  Add Roadmap</a>
  <a id="add-folder" href="javascript:void(0)" class="btn btn-default"><i class="fa fa-plus"></i>  Add <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::FOLDER_TYPE) ?></p></a>
<?php end_slot(); ?>

<table id="roadmap-table" class="table">
  <tbody class="main"></tbody>
</table>

<script type="text/template" id="folderTemplate">
  <td colspan="3">
    <div class="panel panel-default">
      <div style="padding-bottom:10px" class="panel-heading">
        <table class="header-table" style="width:100%;">
          <tr class="folder">
            <td class="small">
              <a class="folder-icon-wrapper" href="javascript:void(0)">
                <span class="fa fa-minus-square-o folder-icon"></span>
              </a>&nbsp;
              <span class="folder-name"><%- name %></span>
              <a href="javascript:void(0)" title="Edit" class="edit">Edit</a>
            </td>
          </tr>
        </table>
      </div>
      <div class="panel-body" style="padding: 0;">
        <div class="table-view-holder-wrapper">
          <div class="table-view-holder" data-id="<%- id %>"></div>
        </div>
      </div>
    </div>
  </td>
</script>

<!-- Modal -->
<div class="modal" id="editRowModal" tabindex="-1" role="dialog" aria-labelledby="editRowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="editRowContent">

    </div>
  </div>
</div>

<script>
  $(function () {
    //////////////////////////////////////////////
    var folderCollection  = new RowCollection,
        tableView         = new TableView({el: $("#roadmap-table"), type: 'roadmap', addToFolderURL: '<?php echo url_for('roadmap\addToFolder') ?>' });
    folderCollection.reset(<?php echo $sf_data->getRaw('folders_json') ?>);
    tableView.addFolders(folderCollection);
    //////////////////////////////////////////////

    var client;

    function update_checkers(){
      client = new ZeroClipboard($(".copy-button"));

      $('.roadmap-link-cell').hover(function() {
        $(this).find('.go-button').css( "visibility", "visible" );
      }, function() {
        $(this).find('.go-button').css( "visibility", "hidden" );
      });
    }

    $('#add-folder').on('click', function () {
      $.get('<?php echo url_for('roadmap\newFolder') ?>', function (response) {
        tableView.addNewFolder(response);
      }, 'json');
    });

    $('#add-row').on('click', function() {
      $.get('<?php echo url_for('roadmap\new') ?>', function (response) {
        tableView.addNewRoadmap(response);
      });
    });

    $('#editRowModal').on('hidden.bs.modal', function (e) {
      update_checkers();
    });

    update_checkers();
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
