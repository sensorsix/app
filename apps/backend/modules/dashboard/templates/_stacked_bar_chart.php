<?php
/** @var StackedBarChart $analyze */

use_javascript('/libs/jquery-jqplot/jquery.jqplot.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.categoryAxisRenderer.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.barRenderer.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.pointLabels.min.js');
use_stylesheet('/libs/jquery-jqplot/jquery.jqplot.css');

$analyze = $analyze->getRawValue();
$alternative_numb = count($analyze->getAlternativesNames());
?>

<div class="row">
  <div class="col-xs-12">
    <div id="chart" style="display: none; height: <?php echo $alternative_numb > 10 ? $alternative_numb * 45 : 450 ?>px; position: relative;"></div>
  </div>
</div>

<script type="text/javascript">
$(function () {
  var $chart = $('#chart');
  stackBarChart = null;
  $chart.bind('stackBarChart.update', function (event, response) {
    // Rebuild jqPlot
    if (stackBarChart) {
      stackBarChart.destroy();
    }
    if (response.data.length) {
      $chart.show();
      $chart.width('100%');
      stackBarChart = $.jqplot('chart', response.data, {
        // Tell the plot to stack the bars.
        stackSeries: true,
        captureRightClick: true,
        seriesDefaults:{ renderer:$.jqplot.BarRenderer, rendererOptions: { barMargin: 30, highlightMouseDown: true, barDirection: 'horizontal' }, pointLabels: { show: false} },
        axes: { xaxis: { padMin: 0, min: 0 }, yaxis: { renderer: $.jqplot.CategoryAxisRenderer, ticks: response.alternatives} },
        series: response.criteria,
        legend: { show: true, placement: 'outsideGrid' }
      });
    }
  });

  var data = <?php echo $analyze->getJsonData() ?>;
  if (data.length) {
    $chart.show();
    stackBarChart = $.jqplot('chart', data, {
        // Tell the plot to stack the bars.
        axesDefaults: { min: 0 },
        stackSeries: true,
        captureRightClick: true,
        seriesDefaults:{
          renderer:$.jqplot.BarRenderer,
          rendererOptions: {
              // Put a 30 pixel margin between bars.
              barMargin: 30,
              // Highlight bars when mouse button pressed.
              // Disables default highlighting on mouse over.
              highlightMouseDown: true,
              barDirection: 'horizontal'
          },
          pointLabels: {show: false}
        },
        axes: {
          xaxis: {
            min: 0,
            // Don't pad out the bottom of the data range.  By default,
            // axes scaled as if data extended 10% above and below the
            // actual range to prevent data points right on grid boundaries.
            // Don't want to do that here.
            padMin: 0
          },
          yaxis: {
            renderer: $.jqplot.CategoryAxisRenderer,
            ticks: <?php echo $analyze->getAlternativesJson() ?>
          }
        },
        // Custom labels for the series are specified with the "label"
        // option on the series option.  Here a series option object
        // is specified for each series.
        series: <?php echo $analyze->getCriteriaJson() ?>,
        legend: {
            show: true,
            placement: 'outsideGrid'
        }
      });
  }
});
</script>
