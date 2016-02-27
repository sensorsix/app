<?php
use_javascript('/libs/jquery-jqplot/jqplot.bubbleRenderer.min.js');
use_stylesheet('/libs/jquery-jqplot/jquery.jqplot.css');
/** @var BubbleChart $chart */
$chart = $chart->getRawValue();
?>

<div class="row">
  <div class="form-group">
    <div class="col-xs-4">
      <label class="control-label" for="bubble-chart-x">X</label>
    </div>
    <div class="col-xs-4">
      <label class="control-label" for="bubble-chart-y">Y</label>
    </div>
    <div class="col-xs-4">
      <label class="control-label" for="bubble-chart-z"><?php echo __('Size') ?></label>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-4">
      <select class="form-control" name="bubble_chart_x" id="bubble-chart-x">
        <?php foreach ($chart->getCriteriaNames() as $criterion_id => $name) : ?>
          <option value="<?php echo $criterion_id ?>"><?php echo $name ?></option>
        <?php endforeach ?>
      </select>
    </div>
    <div class="col-xs-4">
      <select class="form-control" name="bubble_chart_y" id="bubble-chart-y">
        <?php foreach ($chart->getCriteriaNames() as $criterion_id => $name) : ?>
          <option value="<?php echo $criterion_id ?>"><?php echo $name ?></option>
        <?php endforeach ?>
      </select>
    </div>
    <div class="col-xs-4">
      <select class="form-control" name="bubble_chart_z" id="bubble-chart-z">
        <?php foreach ($chart->getCriteriaNames() as $criterion_id => $name) : ?>
          <option value="<?php echo $criterion_id ?>"><?php echo $name ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </div>
</div>

<div>
  <div id="bubble-chart" style="position: relative;"></div>
</div>
<div>
  <a class="btn btn-default" id="bubble-pin-to-wall"  href="javascript:void(0)">Paste snapshot to wall</a>
  <a class="btn btn-default" id="bubble-active-pin-to-wall"  href="javascript:void(0)">Display graph on wall</a>
</div>

<script type="text/javascript">
$(function () {
  var bubble_chart,
    chart_x = $('#bubble-chart-x'),
    chart_y = $('#bubble-chart-y'),
    chart_z = $('#bubble-chart-z'),
    data = <?php echo $chart->getJsonData() ?>,
    labels = <?php echo $chart->getAlternativesJson() ?>,
    wall_data;

  $('#bubble-chart').bind('bubbleChart.update', function (event, response) {
    data = response.data;

    chart_x.find('option').remove();
    chart_y.find('option').remove();
    chart_z.find('option').remove();

    var criteria_names = response.criteria, option;
    for (var criterion_id in criteria_names) {
      option = '<option value="' + criterion_id + '">' + criteria_names[criterion_id] + '</option>';
      chart_x.append(option);
      chart_y.append(option);
      chart_z.append(option);
    }
    chart_y.find('option:eq(1)').attr('selected', 'selected');
    chart_z.find('option:eq(2)').attr('selected', 'selected');

    buildChart(data[chart_x.val()], data[chart_y.val()], data[chart_z.val()]);
  });

  $('#bubble-pin-to-wall').click(function () {
    $.post('<?php echo url_for('@analyze\pinToWall?type=bubble') ?>', {
        data: wall_data,
        x: chart_x.val(),
        y: chart_y.val(),
        z: chart_z.val()
      }
    );
  });

  $('#bubble-active-pin-to-wall').click(function () {
    $.post('<?php echo url_for('@analyze\activePinToWall?type=bubble') ?>', {params: {
      data: wall_data,
      x: chart_x.val(),
      y: chart_y.val(),
      z: chart_z.val()
    }});
  });

  function buildChart(x_data, y_data, z_data) {
    var chart_data = [];

    for (var i in x_data) {
      chart_data.push([x_data[i], y_data[i], z_data[i], labels[i]]);
    }
    wall_data = chart_data;

    if (bubble_chart) {
      bubble_chart.destroy();
    }

    if (chart_data.length) {
      bubble_chart = $.jqplot('bubble-chart', [chart_data], {
        axes: {
           xaxis: {
             padMin: 0,
             rendererOptions: { forceTickAt0: true }
           }
        },
        seriesDefaults:{
          renderer: $.jqplot.BubbleRenderer,
          rendererOptions: {
              bubbleAlpha: 0.6,
              highlightAlpha: 0.8
          }
        }
      });
    }
  }

  chart_x.find('option:eq(1)').attr('selected', 'selected');
  chart_z.find('option:eq(2)').attr('selected', 'selected');

  buildChart(data[chart_x.val()], data[chart_y.val()], data[chart_z.val()]);

  chart_x.change(function () {
    buildChart(data[$(this).val()], data[chart_y.val()], data[chart_z.val()]);
  });

  chart_y.change(function () {
    buildChart(data[chart_x.val()], data[$(this).val()], data[chart_y.val()]);
  });

  chart_z.change(function () {
    buildChart(data[chart_x.val()], data[chart_y.val()], data[$(this).val()]);
  });
});
</script>
