<?php
/** @var AlternativesAnalyze $analyze */
$analyze = $analyze->getRawValue();
?>

<div id="chart" style="margin-left: 20px; width: 1350px; height: 500px; position: relative;"></div>

<script type="text/javascript">
$(function () {
  $('#alternatives-pin-to-wall').click(function () {
   $.get('<?php echo url_for('@analyze\pinToWall?type=alternatives') ?>');
  });

  var plot = $.jqplot('chart', <?php echo $analyze->getJsonData() ?>, {
    // Tell the plot to stack the bars.
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
      pointLabels: { show: false }
    },
    axes: {
      xaxis: {
        // Don't pad out the bottom of the data range.  By default,
        // axes scaled as if data extended 10% above and below the
        // actual range to prevent data points right on grid boundaries.
        // Don't want to do that here.
        padMin: 0,
        min: 0
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
        location: 's',
        placement: 'outsideGrid'
    }
  });
});
</script>
