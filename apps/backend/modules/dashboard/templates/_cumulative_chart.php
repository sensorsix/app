<?php
use_javascript('/libs/jquery-jqplot/jqplot.canvasAxisLabelRenderer.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.canvasAxisTickRenderer.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.canvasTextRenderer.min.js');
/** @var CumulativeGainChart $chart */
$chart = $chart->getRawValue();
?>

<div id="cumulative-wrapper" style="display: none" >
  <div class="row">
    <div class="form-group">
      <div class="col-xs-12">
        <label class="control-label" for="bubble-chart-x">X</label>
      </div>
    </div>
    <div class="form-group">
      <div class="col-xs-4">
        <select class="form-control" name="point_chart_x" id="cumulative-chart-x">
          <?php foreach ($chart->getCriteriaNames() as $criterion_id => $name) : ?>
            <option value="<?php echo $criterion_id ?>"><?php echo $name ?></option>
          <?php endforeach ?>
        </select>
      </div>
    </div>
  </div>
  <div>
    <div id="cumulative-chart" style="position: relative;"></div>
  </div>
</div>

<script type="text/javascript">
$(function () {
  var
    criterion = $('#cumulative-chart-x'),
    y_data = <?php echo $chart->getJsonData() ?>,
    x_data = <?php echo $chart->getJsonCostData() ?>,
    labels = <?php echo $chart->getAlternativesJson() ?>,
    wall_data,
    $cumulative_wrapper = $('#cumulative-wrapper');

  cumulativeChart = null;

  $('#cumulative-chart').bind('cumulativeChart.update', function (event, response) {
    x_data = response.costData;
    y_data = response.benefitData;
    labels = response.alternatives;

    var criteria_names = response.criteria, option;
    criterion.empty();
    for (var criterion_id in criteria_names) {
      option = '<option value="' + criterion_id + '">' + criteria_names[criterion_id] + '</option>';
      criterion.append(option);
    }

    buildChart(x_data[criterion.val()], y_data[criterion.val()]);
  });

  function buildChart(x_data, y_data) {
    var chart_data = [];

    for (var i in x_data) {
      chart_data.push([
        x_data[i],
        typeof y_data == 'undefined' ? 0 : y_data[i],
        labels[i]
      ]);
    }
    wall_data = chart_data;

    if (cumulativeChart) {
      cumulativeChart.destroy();
      cumulativeChart = null;
    }

    if (chart_data.length) {
      $cumulative_wrapper.show();
      $('#cumulative-chart').width('100%');
      cumulativeChart = $.jqplot('cumulative-chart', [chart_data],
        {
          axesDefaults: {
            labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
            tickRenderer: $.jqplot.CanvasAxisTickRenderer
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
               label: "Cost",
               padMin: 0,
               tickOptions: {
                 angle: -30,
                 fontSize: '10pt'
               }
             },
             yaxis: {
               padMin: 0,
               label: "Benefit"
             }
          }
        });
    }
  }

  // Select for "X"
  criterion.change(function () { buildChart(x_data[$(this).val()], y_data[$(this).val()]); }).change();
});
</script>
