<?php
/** @var RadarChart $chart */
$chart = $analyze->getRawValue();
?>
<div style="position: relative; width: 1350px">
  <table class="jqplot-table-legend" style="right: 0px; top: 10px;">
    <tbody>
    <?php foreach ($chart->getLegend() as $item) : ?>
    <tr class="jqplot-table-legend">
      <td class="jqplot-table-legend jqplot-table-legend-swatch" style="text-align: center; padding-top: 0px;">
        <div class="jqplot-table-legend-swatch-outline">
          <div class="jqplot-table-legend-swatch" style="background-color: <?php echo $item['color'] ?>; border-color: <?php echo $item['color'] ?>;"></div>
        </div>
      </td>
      <td class="jqplot-table-legend jqplot-table-legend-label" style="padding: 0px;"><?php echo $item['name'] ?></td>
    </tr>
    <?php endforeach ?>
    </tbody>
  </table>
  <canvas id="radar-chart" height="450" width="1300"></canvas>
</div>

<script>
$(function () {
  var radarChartData = {
      labels : <?php echo $chart->getCriteriaJson() ?>,
      datasets : <?php echo $chart->getJsonData() ?>
  },
  filter = <?php echo $chart->getFilterJson() ?>;

  if (filter) {
    radarChartData.datasets = $.grep(radarChartData.datasets, function(n, i) { return $.inArray(i, filter.alternatives_indexes) == -1; });

    radarChartData.labels = $.grep(radarChartData.labels, function(n, i) { return $.inArray(i, filter.criteria_indexes) == -1; });
    for (var index in radarChartData.datasets) {
      radarChartData.datasets[index].data = $.grep(radarChartData.datasets[index].data, function(n, i) { return $.inArray(i, filter.criteria_indexes) == -1; });
    }

    $('tr', '.jqplot-table-legend').each(function (i) {
      if ($.inArray(i, filter.alternatives_indexes) >= 0) {
        $(this).hide();
      }
    });
  }

  var myRadar = new Chart(document.getElementById("radar-chart").getContext("2d"));
  myRadar.Radar(radarChartData,{scaleShowLabels : false, pointLabelFontSize : 10});
});
</script>