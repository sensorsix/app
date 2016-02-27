<!--[if lte IE 8]>
  <?php use_javascript('/libs/excanvas/excanvas.compiled.js?v=1'); ?>
<![endif]-->
<?php
use_javascript('/libs/chart/Chart.min.js');
/** @var RadarChart $chart */
$chart = $chart->getRawValue();
?>
<div class="row">
  <div class="col-xs-3">
    <h4>Criteria</h4>
    <div id="radar-criteria" class="checkboxes-list">
      <input id="select-all-criteria" type="checkbox" checked/> <strong>Select all</strong> <br />
      <?php foreach ($chart->getCriteriaNames() as $key => $criterion_name) : ?>
        <input class="criterion-filter" type="checkbox" checked value="<?php echo $key ?>" /> <?php echo $criterion_name ?> <br />
      <?php endforeach ?>
    </div>
  </div>

  <div class="col-xs-6" style="position: relative">
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
    <canvas id="radar-chart" height="450" width="450"></canvas>
  </div>

  <div class="col-xs-3">
    <h4><?php echo $chart->getAlternativesLabel() ?></h4>
    <div id="radar-alternatives" class="checkboxes-list">
      <input id="select-all-alternatives" type="checkbox" checked/> <strong>Select all</strong> <br />
      <?php foreach ($chart->getAlternativeNames() as $key => $alternative_name) : ?>
        <input class="alternative-filter" type="checkbox" checked value="<?php echo $key ?>" /> <?php echo $alternative_name ?> <br />
      <?php endforeach ?>
    </div>
  </div>
</div>

<a class="btn btn-default" id="radar-pin-to-wall"  href="javascript:void(0)">Paste snapshot to wall</a>
<a class="btn btn-default" id="radar-active-pin-to-wall"  href="javascript:void(0)">Display graph on wall</a>

<script>
$(function () {
  var radarChartData = {
      labels : <?php echo $chart->getCriteriaJson() ?>,
      datasets : <?php echo $chart->getJsonData() ?>
    },
    $criteria = $('.criterion-filter', '#radar-criteria'),
    $alternatives = $('.alternative-filter', '#radar-alternatives'),
    remove_criteria_indexes = [],
    remove_alternatives_indexes = [],
    $select_all_criteria = $('#select-all-criteria'),
    $select_all_alternatives = $('#select-all-alternatives');

  var filterRadarData = function () {
    var filteredData = $.extend(true, {}, radarChartData);
      remove_criteria_indexes = [];
      remove_alternatives_indexes = [];

    // Grab remove indexes
    $criteria.filter(':not(:checked)').each(function () {
      remove_criteria_indexes.push(parseInt($(this).val(), 10));
    });

    $alternatives.filter(':not(:checked)').each(function () {
      remove_alternatives_indexes.push(parseInt($(this).val(), 10));
    });

    // Removes filtered data
    filteredData.datasets = $.grep(filteredData.datasets, function(n, i) { return $.inArray(i, remove_alternatives_indexes) == -1; });

    filteredData.labels = $.grep(filteredData.labels, function(n, i) { return $.inArray(i, remove_criteria_indexes) == -1; });
    for (var index in filteredData.datasets) {
      filteredData.datasets[index].data = $.grep(filteredData.datasets[index].data, function(n, i) { return $.inArray(i, remove_criteria_indexes) == -1; });
    }

    myRadar.Radar(filteredData, {scaleShowLabels : false, pointLabelFontSize : 10});
  };

  $select_all_criteria.on('change', function () {
    if ($(this).is(':checked')) {
      $criteria.filter(':not(:checked)').attr('checked', true);
      filterRadarData();
    }
  });

  $select_all_alternatives.on('change', function () {
    if ($(this).is(':checked')) {
      $alternatives.filter(':not(:checked)').attr('checked', true);
      filterRadarData();
    }
  });

  $criteria.on('change', function () {
    filterRadarData();
    if ($criteria.length == $criteria.filter(':checked').length) {
      $select_all_criteria.attr('checked', true);
    } else {
      $select_all_criteria.removeAttr('checked');
    }
  });

  $alternatives.on('change', function () {
    filterRadarData();
    if ($alternatives.length == $alternatives.filter(':checked').length) {
      $select_all_alternatives.attr('checked', true);
    } else {
      $select_all_alternatives.removeAttr('checked');
    }
  });

  $('#radar-pin-to-wall').on('click', function() {
    var data = {},
      filter = { criteria_indexes: remove_criteria_indexes, alternatives_indexes: remove_alternatives_indexes };
    if ($('#criteria-chart').length) {
      data = $('#criteria-chart').editableBars('getData');
    }

    $.post('<?php echo url_for('@analyze\pinToWall?type=radar') ?>', { graph: data, filter: filter });
  });

  $('#radar-active-pin-to-wall').click(function () {
    var data = {},
      filter = { criteria_indexes: remove_criteria_indexes, alternatives_indexes: remove_alternatives_indexes };
    if ($('#criteria-chart').length) {
      data = $('#criteria-chart').editableBars('getData');
    }

    $.post('<?php echo url_for('@analyze\activePinToWall?type=radar') ?>', { params: {graph: data, filter: filter } });
  });

  var myRadar = new Chart(document.getElementById("radar-chart").getContext("2d"));
  myRadar.Radar(radarChartData, {scaleShowLabels : false, pointLabelFontSize : 10});

  $('#radar-chart').on('radarChart.update', function (event, response) {
    radarChartData.datasets = response;
    filterRadarData();
  });
});
</script>