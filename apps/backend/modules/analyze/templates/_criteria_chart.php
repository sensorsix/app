<?php /** @var CriteriaAnalyze $analyze */ ?>
<?php /** @var Decision $decision */ ?>
<?php $analyze = $analyze->getRawValue(); ?>
<?php $decision = $decision->getRawValue(); ?>
<?php use_javascript('/libs/jquery-editablebars/jquery.editablebars.js?v=1.0') ?>

<div class="row-fluid">
  <div class="col-xs-12">
  <p class="lead">Drag bars to change criteria weights</p>
      <p>
    You can drag bars to personalize criteria which then affects reporting. 
    By using automated weighting you let SensorSix' algorithm calculate weighting based on the input.
    </p>

   <div class="row">
     <div class="col-xs-9">
     <div id="criteria-chart"></div>
      </div>
     <div class="col-xs-3">
     <br><br>
     <p> 
     <div class="checkbox">
     <label for="show-changed">
    <input type="checkbox" id="show-changed" <?php if ($decision->save_graph_weight) echo 'checked="checked"' ?>> Save criteria weights</label>
    </div>
    <a class="btn btn-info" id="revert" href="javascript:void(0)">Reset to autoupdated weights</a>
    </p>
      </div>

   </div>
    
   
    
    
    
    
    
    <a class="btn btn-default" id="criteria-pin-to-wall"  href="javascript:void(0)" style="margin-left:50px;">Paste snapshot to wall</a>
    <a class="btn btn-default" id="criteria-active-pin-to-wall"  href="javascript:void(0)">Display graph on wall</a>
  </div>
</div>

<script type="text/javascript">
/**
 * This variable will be initialized in the "_alternatives_chart.php"
 * @type {jqPlot}
 */
var plot;

$(function () {
  var graph = $('#criteria-chart');

  $('#criteria-pin-to-wall').click(function () {
    var data = graph.editableBars('getData');
    $.post('<?php echo url_for('@analyze\pinToWall?type=criteria') ?>', { graph: data });
  });

  $('#criteria-active-pin-to-wall').click(function () {
    var data = graph.editableBars('getData');
    $.post('<?php echo url_for('@analyze\activePinToWall?type=criteria') ?>', {params: {graph: data}});
  });

  // Reverts criteria chart to original state
  $('#revert').click(function () {
   $.get('<?php echo url_for('@analyze\revert') ?>', {}, function (response) {
     graph.editableBars('setData', response.criteriaData);
     graph.editableBars('updateBars');

     $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
     $('#point-chart').trigger('pointChart.update', [response.pointChart]);
     $('#cumulative-chart').trigger('cumulativeChart.update', [response.cumulativeChart]);
     $('#bubble-chart').trigger('bubbleChart.update', [response.bubbleChart]);
     $('#cost-estimate-table').trigger('costUpdate', [response.costOrder]);
     $('#radar-chart').trigger('radarChart.update', [response.radarChart]);
   });
  });

  // The method builds criteria chart
  graph.editableBars({
    width: <?php echo $analyze->getGraphWidth() ?>,
    height: <?php echo $analyze->getGraphHeight() ?>,
    bottom_delta: <?php echo $analyze->getGraphBottomDelta() ?>,
    data: <?php echo $analyze->getJsonData() ?>,
    onChange: function (data) {

      var params = {};
      for (var i = 0; i < data.length; i++) {
        params['graph[' + i + '][id]'] = data[i].id;
        params['graph[' + i + '][value]'] = data[i].value / 10;
      }
      params['save'] = +$('#show-changed').prop('checked');
      $.post('<?php echo url_for('analyze\update') ?>', params, function(response) {
        $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
        $('#point-chart').trigger('pointChart.update', [response.pointChart]);
        $('#cumulative-chart').trigger('cumulativeChart.update', [response.cumulativeChart]);
        $('#bubble-chart').trigger('bubbleChart.update', [response.bubbleChart]);
        $('#cost-estimate-table').trigger('costUpdate', [response.costOrder]);
        $('#radar-chart').trigger('radarChart.update', [response.radarChart]);
      });
    }
  });

  // show chart created from responses (original) or chart changed
  // and saved by user
  $('#show-changed').on('change', function() {

    var _self = $(this);
    if (_self.prop('checked')) {
      var data = graph.editableBars('getData');
      var params = {};
      for (var i = 0; i < data.length; i++) {
        params['graph[' + i + '][id]'] = data[i].id;
        params['graph[' + i + '][value]'] = data[i].value / 10;
      }
      params['editable'] = true;
      $.post('<?php echo url_for('analyze\update') ?>', params, function(response) {
        graph.editableBars('setData', response.criteriaData);
        graph.editableBars('updateBars');
        $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
        $('#point-chart').trigger('pointChart.update', [response.pointChart]);
        $('#cumulative-chart').trigger('cumulativeChart.update', [response.cumulativeChart]);
        $('#bubble-chart').trigger('bubbleChart.update', [response.bubbleChart]);
        $('#cost-estimate-table').trigger('costUpdate', [response.costOrder]);
        $('#radar-chart').trigger('radarChart.update', [response.radarChart]);
      });
      // show original data
    } else {
      $('#revert').click();
    }

    $.get('<?php echo url_for('analyze\save_criteria_weight_state') ?>', {
      decision_id : <?php echo $decision->id ?>,
      state       : +_self.prop('checked')
    });
  });
});
</script>
