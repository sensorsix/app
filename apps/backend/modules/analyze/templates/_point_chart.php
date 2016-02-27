<?php
use_javascript('/libs/jquery-jqplot/jquery.jqplot.min.js');
use_javascript('/libs/jquery-jqplot/jqplot.pointLabels.min.js');
use_stylesheet('/libs/jquery-jqplot/jquery.jqplot.css');

/** @var PointChart $chart */
$chart = $chart->getRawValue();
?>

<div class="form-inline">
  <div class="control-group">
    <div class="span4">
      <label class="control-label" for="point-chart-x">X</label>
      <select name="point_chart_x" id="point-chart-x">
        <?php foreach ($chart->getCriteriaNames() as $criterion_id => $name) : ?>
          <option value="<?php echo $criterion_id ?>"><?php echo $name ?></option>
        <?php endforeach ?>
      </select>
    </div>
    <div class="span4">
      <label class="control-label" for="point-chart-y">Y</label>
      <select name="point_chart_y" id="point-chart-y">
        <?php foreach ($chart->getCriteriaNames() as $criterion_id => $name) : ?>
          <option value="<?php echo $criterion_id ?>"><?php echo $name ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </div>
</div>

<div class="row-fluid">
  <div class="span12">
    <div id="point-chart" style="position: relative;"></div>
    <a class="btn btn-default" id="point-pin-to-wall"  href="javascript:void(0)">Paste snapshot to wall</a>
    <a class="btn btn-default" id="point-active-pin-to-wall"  href="javascript:void(0)">Display graph on wall</a>
  </div>
</div>

<script type="text/javascript">
$(function () {
  var point_chart,
    chart_y = $('#point-chart-y'),
    chart_x = $('#point-chart-x'),
    data = <?php echo $chart->getJsonData() ?>,
    labels = <?php echo $chart->getAlternativesJson() ?>,
    wall_data;

  $('#point-pin-to-wall').click(function () {
    $.post('<?php echo url_for('@analyze\pinToWall?type=xy') ?>', {
        data: wall_data,
        x: chart_x.val(),
        y: chart_y.val()
      }
    );
  });

  $('#point-active-pin-to-wall').click(function () {
    $.post('<?php echo url_for('@analyze\activePinToWall?type=xy') ?>', {params: {
      data: wall_data,
      x: chart_x.val(),
      y: chart_y.val()
    }});
  });

  $('#point-chart').bind('pointChart.update', function (event, response) {
    data = response.data;

    chart_x.find('option').remove();
    chart_y.find('option').remove();
    var criteria_names = response.criteria, option;
    for (var criterion_id in criteria_names) {
      option = '<option value="' + criterion_id + '">' + criteria_names[criterion_id] + '</option>';
      chart_x.append(option);
      chart_y.append(option);
    }
    chart_y.find('option:eq(1)').attr('selected', 'selected');

    buildChart(data[chart_x.val()], data[chart_y.val()]);
  });

  function buildChart(x_data, y_data) {
    var chart_data = [];

    for (var i in x_data) {
      chart_data.push([x_data[i], y_data[i], labels[i]]);
    }
    wall_data = chart_data;

    if (point_chart) {
      point_chart.destroy();
    }

    if (chart_data.length) {
      point_chart = $.jqplot('point-chart', [chart_data],
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
    }
  }

  chart_x.find('option:eq(1)').attr('selected', 'selected');

  // Select for "X"
  chart_x.change(function () { buildChart(data[$(this).val()], data[chart_y.val()]); }).change();

  // Select for "Y"
  chart_y.change(function () { buildChart(data[chart_x.val()], data[$(this).val()]); });
});
</script>
