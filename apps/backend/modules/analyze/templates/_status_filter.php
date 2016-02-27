<?php $data = $data->getRawValue() ?>
<div class="control-group">
  <?php foreach ($statuses as $status) : ?>
    <label style="width: 200px;" class="control-label control-label">
      <input style="margin: -2px 5px 0 0" class="status-filter" <?php if (!in_array($status, $data)) echo 'checked="checked"' ?> data-status="<?php echo $status ?>" type="checkbox" value="1"/><?php echo $status ?>
    </label>
  <?php endforeach ?>
</div>

<script type="text/javascript">
$(function () {
  var xhr;

  $('.status-filter').click(function () {
    var action = $(this).is(':checked') ? 'delete' : 'add';

    if (xhr) {
      xhr.abort();
    }

    xhr = $.post('<?php echo url_for('@analyze\updateStatusFilter') ?>', { filter_action: action, status: $(this).data('status') }, function (response) {
      xhr = null;
      $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
      $('#point-chart').trigger('pointChart.update', [response.pointChart]);
      $('#bubble-chart').trigger('bubbleChart.update', [response.bubbleChart]);
      $('#cost-estimate-table').trigger('costUpdate', [response.costOrder]);
    });
  });
});
</script>