<?php
/** @var CumulativeGainChart $chart */
$chart = $chart->getRawValue();
?>

<div style="margin-left: 20px; width:1350px; height: 300px;">
  <strong>X:</strong> <?php echo $chart->getXLabel() ?>
  <div id="cumulative-chart" style="position: relative;"></div>
</div>

<script type="text/javascript">
$(function () {
  $.jqplot('cumulative-chart', [<?php echo $chart->getJsonData() ?>],
    {
      axesDefaults: {
        labelRenderer: $.jqplot.CanvasAxisLabelRenderer
      },
      // Series options are specified as an array of objects, one object
      // for each series.
      series:[
        {
          // Change our line width and use a diamond shaped marker.
          lineWidth:5,
          markerOptions: { style:"filledSquare", size:10 }
        }
      ],
      seriesDefaults: {
        showMarker:true,
        pointLabels:{ show:true }
      },
      axes: {
         xaxis: {
           padMin: 0,
           label: "Cost"
         },
         yaxis: {
           padMin: 0,
           label: "Benefit"
         }
      }
    });
});
</script>

