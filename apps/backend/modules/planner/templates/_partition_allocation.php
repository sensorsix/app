<?php
$form = new AlternativeForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);
?>

<style>

  .rp-container {
    display:flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: flex-start;
    align-content: stretch;
    align-items: stretch;

  }

  .col-release{
    width:300px;
    background: #eee;
    border:1px solid #999;
    display: block;
    justify-content: top;
    align-items: top;
    position: relative;
    padding: 1.5em 1em;
    border-radius: 5px;
    text-align: left;
    margin: 1.5em 1em;
  }
  .col-release header{
    min-height:50px;
  }
  .col-add-release{
    width:300px;
    background: #fff;
    border:1px solid #fff;
    display: block;
    justify-content: flex-start;
    align-items: flex-start;
    position: relative;
    padding: 1.5em 1em;
    border-radius: 5px;
    text-align: left;
    margin: 1.5em 1em;

  }

  .rp-container  li.item{
    background: #fff;
    padding: .5em;
    margin-top: 0;
    border:0px solid #999;
    border-radius: 5px;
    font-weight:bold;
    font-size:12px;
  }

  .col-backlog{
    background: #999;
    width:300px;

  }

  .rp-container   h3{
    margin:0;
    display: block;
    font-size:18px;
    padding-top:-30;
  }


  .row-release{
    width: auto;
    display: table-row;
    max-width:800px;


  }
  .row-release .col-release{
    width: 350px;
    display: table-cell;
    background: #eee;
    border-left:1px solid #ccc;
    min-height:200px;
    padding:10px;
  }


  ul.items{
    list-style: none;
    margin: 0;
    padding: 0;
  }

  li.item{
    position:relative;
    margin:0 0 20px 0;
    background: #fff;
    padding: .5em;
    margin-top: 10px;
    border:1px solid #000;
    border-radius: 5px;
    cursor: move;
  }

  .rp-container li.item a{position:relative; display:inline-block; padding-left:20px;}
  .rp-container li.item i{position:absolute; padding-right:15px;padding-top: 3px; color:#999;}

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

<div class="rp">
  <div class="rp-container">

    <div class="col-release col-backlog">
      <header>
        <h3><?php echo __('Backlog') ?></h3>
        <small><?php echo __('Available') ?> <span id="backlog-total">0</span> <?php echo __('Items') ?>. </small>
      </header>
      <ul class="items"  id="release-available-alternatives"></ul>
    </div>

    <?php foreach ($releases as $release) : ?>
      <?php include_partial('release', array('release' => $release)) ?>
    <?php endforeach ?>

    <div class="col-add-release"><div class="item"><a href="javascript:void(0);" class="add-release">Add a release...</a></div></div>

  </div>
</div>

<div>
  <a class="btn btn-default" id="partition-pin-to-wall"  href="javascript:void(0)">Snapshot <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE, true) ?></p></a>
  <a class="btn btn-default" id="partition-active-pin-to-wall"  href="javascript:void(0)">Display <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE, true) ?></p></a>
  <a class="btn btn-success" id="excel-export" href="<?php echo url_for('@analyze\excelExport') ?>"><?php echo __('Export') ?></a>
</div>

<!-- Modal -->
<div class="modal" id="editRowModal" tabindex="-1" role="dialog" aria-labelledby="editRowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="editRowContent">

    </div>
  </div>
</div>

<script type="text/javascript">
/*<![CDATA[*/
  $(function () {
    var $available_alternatives = $('#release-available-alternatives'),
      $backlog                  = $('.col-backlog'),
      $backlog_total            = $('#backlog-total'),
      $cost_criteria            = $('#cost-criteria'),
      $excel_export             = $('#excel-export'),
      used_alternatives         = {},
      alternative_url           = '<?php echo url_for('@alternative\edit?id=0') ?>',
      release_url               = '<?php echo url_for('@planner2\editRelease?id=0') ?>',
      currentEditView           = null,
      fetchedData;

    <?php if (false && $sf_user->getGuardUser()->account_type != 'Enterprise') : ?>
    $excel_export.on('click', function() {
      alert('Export is an Enterprise function. Please upgrade');
      return false;
    });
    <?php endif ?>

    $backlog.droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      drop: function (event, ui) {
        $(ui.draggable).appendTo($(this).find('ul').get(0));
        if (ui.draggable.data('release_id')) {
          moveItem(ui.draggable, ui.draggable.data('release_id'), ui.draggable.data('alternative_id'), $cost_criteria.val());
          ui.draggable.data('release_id', false);
        }
      }
    });

    // Pin to wall
    $('#partition-pin-to-wall').click(function () {
      $.post('<?php echo url_for('@analyze\pinToWall?type=partition') ?>', { criterion_id: $cost_criteria.val() });
    });

    $('#partition-active-pin-to-wall').click(function () {
      $.post('<?php echo url_for('@analyze\activePinToWall?type=partition') ?>', { params: {criterion_id: $cost_criteria.val() }});
    });

    // Fetches alternatives list
    $('#cost-estimate-table').bind('costAnalyze.update', function(event, data) {
      var item, alternative_id;
      fetchedData = data;
      $available_alternatives.empty();

      // Adds items into the list of available alternatives
      for (var i = 0; i < data.length; i++) {
        alternative_id = parseInt(data[i].alternative_id, 10);
        // Adds only items which are not in releases
        if ($.inArray(alternative_id, used_alternatives[$cost_criteria.val()]) < 0) {
          item = $('<li class="item"/>').data('alternative_id', alternative_id).data('value', data[i].value);
          item.prepend('<i class="fa fa-align-justify help-tip"></i> ' + '<a class="small edit_row" data-alternative_id="' + alternative_id + '"><span class="name"> ' + data[i].name + '</span> ' + (data[i].value.toFixed(2)) + '</a>');
          $available_alternatives.append(item);
          item.draggable({ appendTo: "body", helper: "clone" });
          $backlog_total.text(parseInt($backlog_total.text(), 10) + 1);
        }
      }
    });

    function moveItem(item, release_id, alternative_id, criterion_id) {
      $.post('<?php echo url_for('@planner2\removeReleaseItem') ?>', { release_id: release_id, alternative_id: alternative_id }, function () {
        used_alternatives[criterion_id].splice($.inArray(alternative_id, used_alternatives[criterion_id]), 1);
        // Sets "Total"
        var total = $('#release-total-' + release_id);
        total.text(parseInt(total.text(), 10) - 1);
        $backlog_total.text(parseInt($backlog_total.text(), 10) + 1);
      });
    }

    function addRelease(release_container) {

      var list = $('ul', release_container);

      $(release_container).droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        drop: function (event, ui) {
          // If item was moved to another list
          if (ui.draggable.parent()[0] != this) {
            $(ui.draggable).appendTo($(this).find('ul').get(0));

            var data = {}, criterion_id = $(this).data('criterion_id');
            data.alternative_id = ui.draggable.data('alternative_id');
            data.value = ui.draggable.data('value');
            data.release_id = $(this).data('release_id');

            if (ui.draggable.data('release_id')) {
              moveItem(ui.draggable, ui.draggable.data('release_id'), data.alternative_id, criterion_id);
            }
            ui.draggable.data('release_id', data.release_id);

            if (!used_alternatives[criterion_id]) {
              used_alternatives[criterion_id] = [];
            }
            used_alternatives[criterion_id].push(data.alternative_id);

            // Sets "Total"
            var total = $('#release-total-' + data.release_id);
            total.text(parseInt(total.text(), 10) + 1);
            $backlog_total.text(parseInt($backlog_total.text(), 10) - 1);

            $.post('<?php echo url_for('@planner2\addReleaseItem') ?>', data);
          }
        }
      });

      // Adds loaded alternatives ids as used
      if (list.children().length) {
        list.children().each(function () {
          if (!used_alternatives[list.data('criterion_id')]) {
            used_alternatives[list.data('criterion_id')] = [];
          }
          $(this).draggable({ appendTo: "body", helper: "clone" });
          $(this).data('release_id', list.data('release_id'));
          used_alternatives[list.data('criterion_id')].push($(this).data('alternative_id'));
        });
      }
    }

    $('.release').each(function () {
      addRelease($(this));
    });

    // On "Cost variable" change
    $cost_criteria.on('change', function () {
      $('.release').hide();
      $('.release-criterion-' + $(this).val()).show();
      $available_alternatives.empty();
      $excel_export.attr('href', $excel_export.attr('href') + '?criterion_id=' + $(this).val());
    }).trigger('change');

    // Adds new release
    $('.add-release').on('click', function() {
      $.get('<?php echo url_for('planner2\newRelease') ?>', { criterion_id: $cost_criteria.val() }, function (response) {
        // if some row is already opened to edit
        if (currentEditView) {
          currentEditView.updateData({
            model   : null,
            element : this,
            mode    : 'create',
            only_edit: false
          });
        } else {
          currentEditView = new RowEditView({
            el      : $('#editRowContent'),
            model   : null,
            element : this,
            mode    : 'create',
            only_edit: false
          });
        }
        $('#editRowContent').html(response);
        $('#editRowModal').modal('show');
      });
    });

    // add release functional
    // @see RowEditView class
    $('#editRowModal').on('release:added', function(e, release_id) {
      addRelease($('#release-' + release_id));
    });

    // Edit release
    $('.rp-container').on('click', '.edit_release', function() {
      var _self = $(this);

      var Model = Backbone.Model.extend({
        defaults: {
          edit_url: release_url.replace('/0/', '/' + _self.data('release_id') + '/')
        }
      });

      // change the name of alternative in the html
      var alternative = new Model();
      alternative.on('change', function(model) {
        _self.find('.name').text(model.get('name'));
        $($('#cost-alternative-' + model.get('id')).find('td').get(1)).text(model.get('name'));
      });

      if (currentEditView) {
        currentEditView.updateData({
          model   : alternative,
          element : this,
          mode    : 'edit',
          only_edit: false
        })
      } else {
        currentEditView = new RowEditView({
          el      : $('#editRowContent'),
          model   : alternative,
          element : this,
          mode    : 'edit',
          only_edit: false
        });
      }
      currentEditView.render();
    });

    // Deletes release
    $('#editRowModal').on('click', '.release-delete', function(e) {
      e.preventDefault();
      if (confirm('<?php echo __('You are about to delete this item. Press Ok to continue.', array(), 'sf_admin') ?>')) {
        var
            release_id    = $(this).data('release_id'),
            release_item  = $('#release-' + release_id),
            criterion_id  = $(this).data('criterion_id')
            ;

        $.post('<?php echo url_for('@planner2\deleteRelease') ?>', { id: release_id });
        $('li', release_item).each(function () {
          used_alternatives[criterion_id].splice($.inArray($(this).data('alternative_id'), used_alternatives[criterion_id]), 1);
        });
        if (fetchedData) {
          $('#cost-estimate-table').trigger('costAnalyze.update', [fetchedData]);
        }
        release_item.remove();
        $('#editRowModal').modal('hide');
      }
    });

    // Edit alternatives
    $('.rp-container').on('click', '.edit_row', function() {
      var _self = $(this);

      var Model = Backbone.Model.extend({
        defaults: {
          edit_url: alternative_url.replace('/0/', '/' + _self.data('alternative_id') + '/')
        }
      });

      // change the name of alternative in the html
      var alternative = new Model();
      alternative.on('change', function(model) {
        _self.find('.name').text(model.get('name'));
        $($('#cost-alternative-' + model.get('id')).find('td').get(1)).text(model.get('name'));
      });

      if (currentEditView) {
        currentEditView.updateData({
          model   : alternative,
          element : this,
          mode    : 'edit',
          only_edit: true
        })
      } else {
        currentEditView = new RowEditView({
          el      : $('#editRowContent'),
          model   : alternative,
          element : this,
          mode    : 'edit',
          only_edit: true
        });
      }
      currentEditView.render();
    });

  });
/*]]>*/
</script>
