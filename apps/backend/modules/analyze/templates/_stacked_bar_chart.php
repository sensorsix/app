<?php
use_javascript('/libs/jquery-jqplot/jquery.jqplot.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.categoryAxisRenderer.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.barRenderer.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.pointLabels.min.js');
use_stylesheet('/libs/jquery-jqplot/jquery.jqplot.css');

/** @var AlternativesAnalyze $analyze */
$analyze = $analyze->getRawValue();
$alternative_numb = count($analyze->getAlternativesNames());
?>

<div class="row-fluid">
  <div class="span12">
    <div id="chart" style="height: <?php echo $alternative_numb > 10 ? $alternative_numb * 45 : 450 ?>px; position: relative;"></div>
    <a class="btn btn-default" id="alternatives-pin-to-wall"  href="javascript:void(0)">Paste snapshot to wall</a>
    <a class="btn btn-default" id="alternatives-active-pin-to-wall"  href="javascript:void(0)">Display graph on wall</a>
    <a id="stack-bar-export" class="btn btn-success" href="<?php echo url_for('@analyze\export') ?>">Export</a>
  </div>
</div>

<script type="text/javascript">
$(function () {

  <?php if (false && $sf_user->getGuardUser()->account_type != 'Enterprise') : ?>
  $('#stack-bar-export').on('click', function () {
    alert('Export is an Enterprise function. Please upgrade');
    return false;
  });
  <?php endif ?>

  $('#alternatives-pin-to-wall').click(function () {
    var data = {};
    if ($('#criteria-chart').length) {
      data = $('#criteria-chart').editableBars('getData');
    }
    $.post('<?php echo url_for('@analyze\pinToWall?type=alternatives') ?>', { graph: data });
  });

  $('#alternatives-active-pin-to-wall').click(function () {
    var data = {};
    if ($('#criteria-chart').length) {
      data = $('#criteria-chart').editableBars('getData');
    }

    $.post('<?php echo url_for('@analyze\activePinToWall?type=alternatives') ?>', { params: {graph: data } });
  });

  $('#chart').bind('stackBarChart.update', function (event, response) {
    // Rebuild jqPlot
    if (plot) {
      plot.destroy();
    }
    if (response.data.length) {
      plot = $.jqplot('chart', response.data, {
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

  var plot = $.jqplot('chart', <?php echo $analyze->getJsonData() ?>, {
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
});
</script>
