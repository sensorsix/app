<?php
/**
 * @var sfWebResponse $sf_response
 * @var Alternative[] $items
 */

$sf_response->setSlot('menu_decision_active', true);
decorate_with('steps_layout');

$form = new AlternativeForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array());?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
<p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?></p> list
<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>
<div style="height: 31px;"></div>
<?php end_slot(); ?>

<div class="row mr-top-25">
  <div class="col-xs-4">

    <div class="panel panel-default">
      <div class="panel-heading">Search</div>
      <div class="panel-body"  style="margin-right: 15px;">
        <div class="row">
          <div class="col-sm-9 mr-top-10" style="margin-bottom: 40px;">
            <input type="text" id="search-field" class="form-control" placeholder="Text" value="<?php echo $sf_request->getParameter('text', ''); ?>">
          </div>
          <div class="col-sm-3 mr-top-10">
            <a id="filter-apply-search" class="btn btn-primary pull-right" href="javascript:void(0)">Apply</a>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="col-xs-8">

    <div class="panel panel-default">
      <div class="panel-heading">Filter</div>
      <div class="panel-body"  style="margin-right: 15px;">
        <div class="row">
          <div class="col-sm-8 col-md-4 mr-top-10">
            <select class="form-control" id="filter-field-type">
              <option value="project_name"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?> name</option>
              <option value="item_name"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?> name</option>
<!--              <option value="item_name">Tags</option>-->
<!--              <option value="description">Description</option>-->
              <option value="created">Created</option>
              <option value="created_by">Created by</option>
            </select>
          </div>
          <div class="col-sm-4 col-md-2 mr-top-10">
            <select class="form-control" id="filter-field-compare">
              <option value="==">=</option>
              <option value=">">></option>
              <option value="<"><</option>
            </select>
          </div>
          <div class="col-sm-8 col-md-4 mr-top-10">
            <input type="text" class="form-control" id="filter-field-value" placeholder="Text">
          </div>
          <div class="col-sm-4 col-md-2 mr-top-10">
            <a id="filter-add-button" class="btn btn-primary pull-right" href="javascript:void(0)">Add</a>
          </div>
        </div>

        <div class="row mr-top-20">
          <div class="col-xs-12">
            <ul class="list-group" id="filter-list">

            </ul>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<h3>Found <span id="found-results"><?php echo count($items); ?></span> results</h3>

<table cellspacing="0" class="table table-striped">
  <thead>
  <tr>
    <th class="sf_admin_text">ID</th>
    <th class="sf_admin_date"><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?></p> name</th>
    <th class="sf_admin_date"><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?></p> name</th>
    <th class="sf_admin_date">Tags</th>
<!--    <th class="sf_admin_date">Description</th>-->
    <th class="sf_admin_date">Created</th>
    <th class="sf_admin_date">Created by</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($items as $item): ?>
    <?php
    $tags = array();
    foreach ($item->getTagAlternative() as $tag){
      $tags[] = '<span class="tag label label-info">' . $tag->Tag->name . '</span>';
    }
    ?>

    <tr class="sf_admin_row odd">
      <td class="sf_admin_text">
        <a href="javascript: void(0);" data-edit-url="<?php echo url_for('alternative/edit?id='.$item->getId()); ?>" data-delete-url="<?php echo url_for('alternative/delete?id='.$item->getId()); ?>" class="alternative_edit">
          <?php echo $item->getId() ?>
        </a>
      </td>
      <td class="sf_admin_text table_project_name" style="<?php echo ($item->getDueDate() && time() > strtotime($item->getDueDate()))? "color: red;" : ""; ?>"><?php echo $item->getDecision()->getName() ?></td>
      <td class="sf_admin_text table_item_name"><?php echo strip_tags($item->getName()) ?></td>
      <td class="sf_admin_text table_item_tags" data-native="<?php echo addslashes(strip_tags(implode('|', $tags))) ?>"><?php echo implode(' ', $tags) ?></td>
<!--      <td class="sf_admin_text table_item_description" style="width: 25%;">--><?php //echo $item->getDescription() ?><!--</td>-->
      <td class="sf_admin_text table_item_created_at"><?php echo $item->getCreatedAt() ?></td>
      <td class="sf_admin_text table_item_created_by"><?php echo $item->getCreatedBy() ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<!-- Modal -->
<div class="modal" id="editRowModal" tabindex="-1" role="dialog" aria-labelledby="editRowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="editRowContent">

    </div>
  </div>
</div>

<style>
  .sf_admin_date {
    width: 18%;
  }
  .sf_admin_text.table_item_name {
    word-break: break-all;
  }
</style>

<script>
  $(document).ready(function(){
    var search_text = '',
        count_results,
        filers
        $found_results = $('#found-results');

    function filter_items_list(){
      filers = [];
      count_results = 0;

      // collect data about filters
      $("#filter-list").find('li').each(function(){
        var $el = $(this);

        filers.push({
          field_type:    $el.data('field_type'),
          field_compare: $el.data('field_compare'),
          field_value:   $el.data('field_value')
        })
      });

      $("tr.sf_admin_row").each(function( i, val ) {
        var $el = $(val);
        $el.show();
        count_results++;

        $.each(filers, function( i, val ) {
          switch (val.field_type){
            case 'project_name': compare_data = $el.find('td.table_project_name').html(); break;
            case 'item_name':    compare_data = $el.find('td.table_item_name').html(); break;
//            case 'description':  compare_data = $el.find('td.table_item_description').html(); break;
            case 'created':      compare_data = $el.find('td.table_item_created_at').html(); break;
            case 'created_by':   compare_data = $el.find('td.table_item_created_by').html(); break;
          }

          try{
            eval('result = "' + compare_data.replace(/\n/g,"").replace(/\"/g,"\'") + '" ' + val.field_compare + ' "' + val.field_value.replace(/\"/g,"\'") + '";');

            if (!result){
              $el.hide();
              count_results--;
              return false;
            }
          } catch(e){
//            console.log(compare_data.replace(/\n/g,"").replace(/\"/g,"\'") + ' ' + val.field_compare + ' ' + val.field_value.replace(/\"/g,"\'"));
          }
        });
      });

      if (search_text) {
        $("tr.sf_admin_row:visible").each(function (i, val) {
          var $el = $(val);

          if (
            $el.find('td.table_project_name').html().toUpperCase().search(search_text) === -1 &&
            $el.find('td.table_item_name').html().toUpperCase().search(search_text) === -1 &&
//            $el.find('td.table_item_description').html().toUpperCase().search(search_text) === -1 &&
            $el.find('td.table_item_tags').data('native').toUpperCase().search(search_text) === -1 &&
            $el.find('td.table_item_created_at').html().toUpperCase().search(search_text) === -1 &&
            $el.find('td.table_item_created_by').html().toUpperCase().search(search_text) === -1
          ){
            $el.hide();
            count_results--;
          }
        });
      }

      $found_results.html(count_results);
    }

    $("#filter-add-button").on("click", function(){
      var $field_type    = $("#filter-field-type").find("option:selected"),
          $field_compare = $("#filter-field-compare").find("option:selected"),
          $field_value   = $("#filter-field-value");

      if ($field_value.val()){
        $("#filter-list")
          .append(
            $( document.createElement("li") )
              .addClass('list-group-item')
              .data({field_type: $field_type.val(), field_compare: $field_compare.val(), field_value: $field_value.val()})
              .html($field_type.val() + ' ' + $field_compare.val().replace(/==/g,"=") + ' ' + $field_value.val())
              .append(
                $(document.createElement("span")).css('cursor', 'pointer').addClass('glyphicon glyphicon-remove pull-right remove-filter-item').on("click", function(){
                  $(this).closest("li").remove();
                  filter_items_list();
                })
              )
          );

        $field_type.attr('selected', false);
        $field_compare.attr('selected', false);
        $field_value.val('');

        filter_items_list();
      }
    });

    $('#filter-apply-search').on('click', function(){
      search_text = $('#search-field').val().toUpperCase();
      filter_items_list();
    }).click();

    var applyActionsForAlternative = function($button){
      $('.edit-delete').on('click', function(){
        if (confirm('Are you sure?')) {
          $.ajax({
            url : $button.data('delete-url'),
            type: "POST",
            success : function (response) {
              window.location.reload();
            },
            error: function(response){

            }
          });
        }
      });

      $('.save_alternative').on('click', function(){
        var
          links_post            = [],
          related_alternatives  = [],
          due_date              = new Date($('#alternative_due_date').val()),
          notify_date           = new Date($('#alternative_notify_date').val());

        $('#links').children().each(function(){
          links_post.push({ 'link': $('.link-input', $(this)).val(), 'id': $('.link-input', $(this)).data('link-id') });
        });

        $('#alternative_related_alternatives option:selected').each(function(){
          related_alternatives.push($(this).val());
        });

        $.ajax({
          url : $('#save_url').val(),
          type: "POST",
          data: {
            "alternative[name]"             : $('#alternative_name').val(),
            "alternative[status]"           : $('#alternative_status').val(),
            "alternative[additional_info]"  : CKEDITOR.instances['alternative_additional_info'].getData(),
            "alternative[notes]"            : CKEDITOR.instances['alternative_notes'].getData(),
            "alternative[assigned_to]"      : $('#alternative_assigned_to').find('option:selected').val(),
            "alternative[external_id]"      : $('#alternative_external_id').val(),
            "alternative[work_progress]"    : $('#alternative_work_progress').val(),
            "alternative[due_date]"         : $('#alternative_due_date').val() ? due_date.getFullYear() + '-' + (due_date.getMonth() + 1) + '-' + due_date.getDate() : '',
            "alternative[notify_date]"      : $('#alternative_notify_date').val() ? notify_date.getFullYear() + '-' + (notify_date.getMonth() + 1) + '-' + notify_date.getDate() : '',
            "tags"                          : JSON.stringify($(".tags_input").tagsinput('items')),
            "links"                         : JSON.stringify(links_post),
            "related_alternatives"          : JSON.stringify(related_alternatives)
          },
          success : function (response) {
            try{
              response = jQuery.parseJSON( response );
            }catch(e){}

            if (typeof response === 'object'){
              window.location.reload();
            }else{
              editor = CKEDITOR.instances['alternative_additional_info'];
              if (editor) { editor.destroy(true); }
              editor = CKEDITOR.instances['alternative_notes'];
              if (editor) { editor.destroy(true); }

              $('#editRowContent').html(response);

              applyActionsForAlternative($button);
            }
          },
          error: function(response){
            $('#editRowModal').modal('hide');
          }
        });
      });
    };

    $('.alternative_edit').on('click', function(){
      var $this = $(this);

      $.get($this.data('edit-url'), function(response) {
        $('#editRowModal').modal('show');

        // Check if ckeditor was created and destroy it
        $.each([ 'alternative_additional_info', 'alternative_notes' ], function( index, value ) {
          var editor = CKEDITOR.instances[value];
          if (editor) { editor.destroy(true); }
        });

        $('#editRowContent').html(response);

        applyActionsForAlternative($this);

        $('.modal-tab').css('overflow-y', 'scroll').css('overflow-x', 'hidden');
        $('.modal-footer').css('position', 'absolute').css('left', 0).css('right', 0).css('bottom', 0);
      });
    });

  });
</script>