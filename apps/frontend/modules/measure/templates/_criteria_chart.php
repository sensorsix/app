<?php /** @var CriteriaAnalyze $analyze */ ?>
<?php $analyze = $analyze->getRawValue(); ?>
<?php use_javascript('/libs/jquery-editablebars/jquery.editablebars.js?v=1.0') ?>

<div class="row-fluid">
  <div class="span12">
    <div id="criteria-chart"></div>
    <a class="btn" id="revert" href="javascript:void(0)">Revert to original</a>
  </div>
</div>

<script type="text/javascript">
/**
 * This variable will be initialized in the "_alternatives_chart.php"
 * @type {jqPlot}
 */
var plot;

$(function () {
  var graph = $('#criteria-chart');

  // Reverts criteria chart to original state
  $('#revert').click(function () {
   $.get('<?php echo url_for('@measure\chartRevert') ?>', {}, function (response) {
     graph.editableBars('setData', response.criteriaData);
     graph.editableBars('updateBars');

     $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
   });
  });

  // The method builds criteria chart
  graph.editableBars({
    width: <?php echo $analyze->getGraphWidth() ?>,
    height: <?php echo $analyze->getGraphHeight() ?>,
    bottom_delta: <?php echo $analyze->getGraphBottomDelta() ?>,
    data: <?php echo $analyze->getJsonData() ?>,
    onChange: function (data) {
      var params = {};
      for (var i = 0; i < data.length; i++) {
        params['graph[' + i + '][id]'] = data[i].id;
        params['graph[' + i + '][value]'] = data[i].value / 10;
      }
      $.post('<?php echo url_for('@measure\chartUpdate') ?>', params, function(response) {
        $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
      });
    }
  });
});
</script>