<?php
/**
 * @var sfWidgetFormDoctrineChoice $widget
 */
?>
<div class="form-inline">
  <select class="form-control" name="bulk_actions" id="bulk-actions">
    <option value="move_to">Move to</option>
    <option value="delete">Delete</option>
    <option value="copy_to">Copy to</option>
  </select>
  <?php echo $widget->getRawValue()->render('move_to', null) ?>
  <a id="apply-bulk-action" class="btn btn-success" href="javascript:void(0)">Go</a>
</div>

<script>
$(function () {
  var $move_to = $('#move_to'),
      $bulk_actions = $('#bulk-actions');

  $bulk_actions.on('change', function () {
    this.value == 'move_to' || this.value == 'copy_to' ? $move_to.show() : $move_to.hide();
  });

  $('#apply-bulk-action').on('click', function () {
    var ids = [];

    $('.check:checked').each(function () {
      ids.push(this.value);
    });

    if (ids.length){
      if ($bulk_actions.val() == 'delete') {
        if (confirm('Do you want to permanently delete these <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?>?')) {
          $.post('<?php echo url_for('@alternative\bulkDelete') ?>', { ids: ids }, function() { window.location.reload(); } );
        }
      } else if ($bulk_actions.val() == 'move_to') {
        $.post('<?php echo url_for('@alternative\bulkMove') ?>', { ids: ids, decision_id: $move_to.val() }, function() { window.location.reload(); } );
      } else if ($bulk_actions.val() == 'copy_to') {
        $.post('<?php echo url_for('@alternative\bulkCopy') ?>', { ids: ids, decision_id: $move_to.val() }, function() { window.location.reload(); } );
      }
    }
  });
});
</script>


 