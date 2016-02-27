<?php
/** @var PointChart $chart */

$chart = $chart->getRawValue();
?>


<div style="margin-left: 20px; width: 1350px; height: 400px;">
  <strong>X:</strong> <?php echo $chart->getXLabel() ?> <strong>Y:</strong> <?php echo $chart->getYLabel() ?>
  <div id="point-chart" style="position: relative;"></div>
</div>

<script type="text/javascript">
$(function () {
  $.jqplot('point-chart', [<?php echo $chart->getJsonData() ?>],
    {
      axes: {
         xaxis: {
           padMin: 0,
           rendererOptions: { forceTickAt0: true }
         }
      },
      // Series options are specified as an array of objects, one object
      // for each series.
      series:[
        {
          // Change our line width and use a diamond shaped marker.
          showLine:false,
          markerOptions:{ style:'dimaond' }
        }
      ],
      seriesDefaults: {
        showMarker:true,
        pointLabels:{ show:true }
      }
    });
});
</script>

