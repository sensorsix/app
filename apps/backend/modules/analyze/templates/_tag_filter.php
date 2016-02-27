<?php $data = $data->getRawValue() ?>
<div class="control-group">
  <?php foreach ($tags as $k => $tag) : ?>
    <label style="width: 200px;" class="control-label control-label">
      <input style="margin: -2px 5px 0 0" class="tag-filter" <?php if (!in_array($tag, $data)) echo 'checked="checked"' ?> data-tag_id="<?php echo $k ?>" data-tag_name="<?php echo $tag ?>" type="checkbox" value="1"/><?php echo $tag ?>
    </label>
  <?php endforeach ?>
</div>

<script type="text/javascript">
$(function () {
  var xhr;

  $('.tag-filter').click(function () {
    var action = $(this).is(':checked') ? 'delete' : 'add';

    if (xhr) {
      xhr.abort();
    }

    xhr = $.post('<?php echo url_for('@analyze\updateTagFilter') ?>', { filter_action: action, tag_id: $(this).data('tag_id'), tag_name: $(this).data('tag_name') }, function (response) {
      xhr = null;
      $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
      $('#point-chart').trigger('pointChart.update', [response.pointChart]);
      $('#bubble-chart').trigger('bubbleChart.update', [response.bubbleChart]);
      $('#cost-estimate-table').trigger('costUpdate', [response.costOrder]);
    });
  });
});
</script>