<?php
use_javascript('/libs/jquery-jqplot/jquery.jqplot.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.bubbleRenderer.min.js');
use_stylesheet('/libs/jquery-jqplot/jquery.jqplot.css');
/** @var BubbleChart $chart */
$chart = $chart->getRawValue();
$rand = uniqid(rand(1, 1000));
?>

<div class="row-fluid">
  <div style="margin-left: 20px; width: 1350px; height: 300px;">
     <strong>X:</strong> <?php echo $chart->getXLabel() ?> <strong>Y:</strong> <?php echo $chart->getYLabel() ?> <strong>Size:</strong> <?php echo $chart->getZLabel() ?>
    <div id="bubble-chart-<?php echo $rand; ?>" style="position: relative;"></div>
  </div>
</div>

<script type="text/javascript">
$(function () {
  $.jqplot('bubble-chart-<?php echo $rand; ?>', [<?php echo $chart->getJsonData() ?>], {
    seriesDefaults:{
      renderer: $.jqplot.BubbleRenderer,
      rendererOptions: {
          bubbleAlpha: 0.6,
          highlightAlpha: 0.8
      }
    }
  });
});
</script>

