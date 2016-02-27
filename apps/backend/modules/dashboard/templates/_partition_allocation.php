<?php use_javascript('/libs/jquery-ui/jquery-ui.min.js?v=1.11.2'); ?>
<?php use_javascript('/libs/jquery-autosave/jquery.autosave.js?v=1.0'); ?>
<div class="form-group">
  <div class="col-xs-6">
    <ul class="list-release" id="release-available-alternatives"></ul>
  </div>
  <div class="col-xs-6">
    <div id="releases-container">
      <div class="">
        <a href="javascript:void(0)" class="btn btn-primary" id="new-release"><?php echo __('New ' . strtolower($decision->getPartitionerAlias())) ?></a>
      </div>

      <?php foreach ($releases as $release) : ?>
        <?php include_partial('release', array('release' => $release)) ?>
      <?php endforeach ?>
    </div>
  </div>
</div>

<script type="text/javascript">
/*<![CDATA[*/
  $(function () {
    var $available_alternatives = $('#release-available-alternatives'),
      used_alternatives = [],
      data = <?php echo $sf_data->getRaw('alternatives_json'); ?>;

    $available_alternatives.droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      drop: function (event, ui) {
        $(ui.draggable).appendTo(this);
        if (ui.draggable.data('release_id')) {
          moveItem(ui.draggable.data('release_id'), ui.draggable.data('alternative_id'));
          ui.draggable.data('release_id', false);
        }
      }
    });

    // Pin to wall
    $('#partition-pin-to-wall').click(function () {
      $.post('<?php echo url_for('@analyze\pinToWall?type=partition') ?>');
    });

    // Fetches alternatives list
    var item, alternative_id;
    $available_alternatives.empty();

    // Adds items into the list of available alternatives
    for (var i = 0; i < data.length; i++) {
      alternative_id = parseInt(data[i].alternative_id, 10);
      // Adds only items which are not in releases
      if ($.inArray(alternative_id, used_alternatives) < 0) {
        item = $('<li/>').text(data[i].name).data('alternative_id', alternative_id);
        $available_alternatives.append(item);
        item.draggable({ appendTo: "body", helper: "clone" });
      }
    }

    function moveItem(release_id, alternative_id) {
      $.post('<?php echo url_for('@analyze\removeReleaseItem') ?>', { release_id: release_id, alternative_id: alternative_id }, function () {
        used_alternatives.splice($.inArray(alternative_id, used_alternatives), 1);
      });
    }

    function addRelease(release_list) {
      // Auto save for release name
      $(".autosave", release_list).autosave({
        url: "<?php echo url_for('@analyze\updateRelease') ?>?id=" + $('ol', release_list).data('release_id'),
        method: "post",
        grouped: true,
        dataType: "html"
      });

      // Deletes release
      $('.release-delete', release_list).click(function () {
        if (confirm('<?php echo __('You are about to delete this item. Press Ok to continue.', array(), 'sf_admin') ?>')) {
          var release_id = $(this).data('release_id');
          var release_item =  $('#release-' + release_id);
          $.post('<?php echo url_for('@analyze\deleteRelease') ?>', { id: release_id });
          // Removes alternatives ids
          $('li', release_item).each(function () {
            used_alternatives.splice($.inArray($(this).data('alternative_id'), used_alternatives), 1);
          });
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

            var data = {};
            data.alternative_id = ui.draggable.data('alternative_id');
            data.release_id = $(this).data('release_id');

            if (ui.draggable.data('release_id')) {
              moveItem(ui.draggable.data('release_id'), data.alternative_id);
            }
            ui.draggable.data('release_id', data.release_id);

            used_alternatives.push(data.alternative_id);

            $.post('<?php echo url_for('@analyze\addReleaseItem') ?>', data);
          }
        }
      });

      // Adds loaded alternatives ids as used
      if (list.children().length) {
        list.children().each(function () {
          $(this).draggable({ appendTo: "body", helper: "clone" });
          $(this).data('release_id', list.data('release_id'));
          used_alternatives.push($(this).data('alternative_id'));
        });
      }
    }

    $('.release').each(function () {
      addRelease($(this));
    });

    // Adds new release
    $('#new-release').click(function () {
      $.get('<?php echo url_for('@analyze\newRelease') ?>', function (response) {
        var release_list =  $(response);

        $('#releases-container').append(release_list);

        addRelease(release_list);
      });
    });
  });
/*]]>*/
</script>