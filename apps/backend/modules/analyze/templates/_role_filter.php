<?php $data = $data->getRawValue() ?>
<div class="control-group">
  <?php foreach ($roles as $role) : ?>
    <label style="width: 200px;" class="control-label control-label">
      <input style="margin: -2px 5px 0 0" class="role-filter" <?php if (in_array($role->id, $data)) echo 'checked="checked"' ?> data-role_id="<?php echo $role->id ?>" type="checkbox" value="1"/><?php echo $role->name ?>
    </label>
  <?php endforeach ?>
</div>

<script type="text/javascript">
$(function () {
  var xhr;

  $('.role-filter').click(function () {
    var action = $(this).is(':checked') ? 'add' : 'delete';

    if (xhr) {
      xhr.abort();
    }

    xhr = $.post('<?php echo url_for('@analyze\updateRoleFilter') ?>', { filter_action: action, role_id: $(this).data('role_id') }, function (response) {
      xhr = null;
      $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
      $('#point-chart').trigger('pointChart.update', [response.pointChart]);
      $('#bubble-chart').trigger('bubbleChart.update', [response.bubbleChart]);
      $('#cost-estimate-table').trigger('costUpdate', [response.costOrder]);
    });
  });
});
</script>