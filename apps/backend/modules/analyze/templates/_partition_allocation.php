<?php use_javascript('/libs/jquery-ui/jquery-ui.min.js?v=1.11.2'); ?>
<?php use_javascript('/libs/jquery-autosave/jquery.autosave.js?v=1.0'); ?>
<div class="form-group">
  <div class="col-xs-6">
    <h4>Available items</h4>
    <ul class="list-release list-group"  id="release-available-alternatives"></ul>
  </div>
  <div class="col-xs-6">
    <div id="releases-container">


      <?php foreach ($releases as $release) : ?>
        <?php include_partial('release', array('release' => $release)) ?>
      <?php endforeach ?>


      <div class="">
        <a href="javascript:void(0)" class="btn btn-primary" id="new-release"><?php echo __('New ' . strtolower($decision->getPartitionerAlias())) ?></a>
      </div>
      
    </div>
  </div>
</div>
<div>
  <a class="btn btn-default" id="partition-pin-to-wall"  href="javascript:void(0)">Snapshot <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE, true) ?></p></a>
  <a class="btn btn-default" id="partition-active-pin-to-wall"  href="javascript:void(0)">Display <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE, true) ?></p></a>
  <a class="btn btn-success" id="excel-export" href="<?php echo url_for('@analyze\excelExport') ?>"><?php echo __('Export') ?></a>
</div>

<script type="text/javascript">
/*<![CDATA[*/
  $(function () {
    var $available_alternatives = $('#release-available-alternatives'),
      $cost_criteria = $('#cost-criteria'),
      $excel_export = $('#excel-export'),
      used_alternatives = {},
      fetchedData;

    <?php if (false && $sf_user->getGuardUser()->account_type != 'Enterprise') : ?>
    $excel_export.on('click', function() {
      alert('Export is an Enterprise function. Please upgrade');
      return false;
    });
    <?php endif ?>

    $available_alternatives.droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      drop: function (event, ui) {
        $(ui.draggable).appendTo(this);
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
          item = $('<li class="list-group-item"/>').text(data[i].name + ' ' + (new Number(data[i].value).toFixed(2))).data('alternative_id', alternative_id).data('value', data[i].value);
          item.prepend('<i class="fa fa-align-justify"></i> ');
          $available_alternatives.append(item);
          item.draggable({ appendTo: "body", helper: "clone" });
        }
      }
    });

    function moveItem(item, release_id, alternative_id, criterion_id) {
      $.post('<?php echo url_for('@analyze\removeReleaseItem') ?>', { release_id: release_id, alternative_id: alternative_id }, function () {
        used_alternatives[criterion_id].splice($.inArray(alternative_id, used_alternatives[criterion_id]), 1);
        var total = $('#release-total-' + release_id);
        // Sets "Total"
        total.text(parseInt(total.text(), 10) - parseInt(item.data('value'), 10));
      });
    }

    function addRelease(release_list) {
      // Auto save for release name
      $(".autosave", release_list).autosave({
        url: "<?php echo url_for('@analyze\updateRelease' ) ?>?id=" + $('ol', release_list).data('release_id'),
        method: "post",
        grouped: true,
        dataType: "html"
      });

      // Deletes release
      $('.release-delete', release_list).click(function () {
        if (confirm('<?php echo __('You are about to delete this item. Press Ok to continue.', array(), 'sf_admin') ?>')) {
          var release_id = $(this).data('release_id');
          var release_item =  $('#release-' + release_id),
            criterion_id = $('ol', release_item).data('criterion_id');
          $.post('<?php echo url_for('@analyze\deleteRelease') ?>', { id: release_id });
          // Removes alternatives ids
          $('li', release_item).each(function () {
            used_alternatives[criterion_id].splice($.inArray($(this).data('alternative_id'), used_alternatives[criterion_id]), 1);
          });
          if (fetchedData) {
            $('#cost-estimate-table').trigger('costAnalyze.update', [fetchedData]);
          }
          release_item.remove();
        }
      });

      var list = $('ol', release_list);

      list.droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        drop: function (event, ui) {
          // If item was moved to another list
          if (ui.draggable.parent()[0] != this) {
            $(ui.draggable).appendTo(this);

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

            var total = $('#release-total-' + data.release_id);
            // Sets "Total"
            total.text(parseInt(total.text(), 10) + parseInt(data.value, 10));

            $.post('<?php echo url_for('@analyze\addReleaseItem') ?>', data);
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
    $('#new-release').click(function () {
      $.get('<?php echo url_for('@analyze\newRelease') ?>', { criterion_id: $cost_criteria.val() }, function (response) {
        var release_list =  $(response);

        $('#releases-container').append(release_list);

        addRelease(release_list);
      });
    });
  });
/*]]>*/
</script>
