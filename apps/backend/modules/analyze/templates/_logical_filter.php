<?php include_partial('logical_filter_form', array('form' => $form)) ?>

<ul id="logical-conditions">
  <?php foreach ($data as $item) : ?>
    <?php include_partial('logical_filter_item', array('item' => $item)) ?>
  <?php endforeach ?>
</ul>

<script type="text/javascript">
$(function () {
  var form = $('#logical-filter-form'),
    list = $('#logical-conditions'),
    form_container = $('#logical-filter-form-container'),
    criterion = $('#logical_filter_criterion_id'),
    operator = $('#logical_filter_logical_operator'),
    value = $('#logical_filter_value');

  function deleteLogicalFilter() {
    if (confirm('<?php echo __('You are about to delete this item. Press Ok to continue.', array(), 'sf_admin') ?>')) {
      var item = $(this).parent();
      item.remove();
      $.post('<?php echo url_for('@analyze\deleteLogicalFilter') ?>', { id: item.data('id') }, function (response) {
        triggerChartsUpdate(response);
      });
    }
  }

  function triggerChartsUpdate(response) {
    $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
    $('#point-chart').trigger('pointChart.update', [response.pointChart]);
    $('#bubble-chart').trigger('bubbleChart.update', [response.bubbleChart]);
    $('#cost-estimate-table').trigger('costUpdate', [response.costOrder]);
    $('#cumulative-chart').trigger('cumulativeChart.update', [response.cumulativeChart]);
  }

  $('#logical-filter-add-button').click(function (e) {
    if (value.val()) {
      $.post('<?php echo url_for('@analyze\newLogicalFilter') ?>', $('#logical-filter-form').serialize(), function (response) {
        // Resets form
        value.val('');
        operator.val('');
        criterion.val('');
        if (typeof response == "object") {
          list.append(response.logicalFilter);

          $('.logical-filter-delete', list).click(function () {
            deleteLogicalFilter.call(this);
          });

          triggerChartsUpdate(response);
        } else {
          form_container.html(response);
        }
      });
    }
  });

  $('.logical-filter-delete', list).click(function () {
    deleteLogicalFilter.call(this);
  });
});
</script>