<?php $analyze = $analyze->getRawValue() ?>

<div class="row form-group">
  <div class="col-md-3">
    <label class="control-label" for="cost-criteria">Cost variable</label>
    <span class="glyphicon glyphicon-info-sign help-info"
          data-toggle="tooltip" title="Select the cost variable you want to allocate"
          data-placement="right"></span>
    <?php
      $widget = new sfWidgetFormChoice(array('choices' => $analyze->getCriteria()), array('class' => 'form-control'));
      echo $widget->render('cost-criteria');
    ?>
  </div>

  <div class="col-md-2">
    <label class="control-label">Pool</label>
    <span class="glyphicon glyphicon-info-sign help-info" data-toggle="tooltip" title="Specify the total sum of resources available for the selected cost variable" data-placement="right"></span>
    <input id="cost-pool" name="pool" type="text" class="form-control">
  </div>

  <div class="col-md-1" style="padding-top:7px;">
    <br>
    <a id="allocate" class="btn btn-primary" href="javascript:void(0)">Allocate</a>
  </div>

  <div class="col-md-2">
    <label class="control-label">Unallocated</label>
    <span class="glyphicon glyphicon-info-sign help-info" data-toggle="tooltip" title="What is left after the current allocation" data-placement="right"></span>
    <input id="unallocated" name="unallocated" type="text" class="form-control" readonly="readonly">
  </div>

  <div class="col-md-2">
    <h3 class="text-info"><?php echo __('Total&nbsp;benefit') ?>&nbsp;<span id="b-score" style="font-size:20px" class="label label-info"></span></h3>
  </div>
</div>

<div class="form-group">
  <div class="col-md-12">
    <div id="cost-estimate-wrap">
      <table id="cost-estimate-table" class="table">
        <?php foreach ($analyze->getAlternativeNames() as $alternative_id => $alternative_name) : ?>
          <tr class="cost-alternative" id="cost-alternative-<?php echo $alternative_id ?>">
            <td>
              <a class="force-in" style="display: none" href="javascript:void(0)"></a>
              <a class="force-out" style="display: none" href="javascript:void(0)"></a>
            </td>
            <td><?php echo $alternative_name ?></td>
            <td class="price"></td>
          </tr>
        <?php endforeach ?>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function () {
  $('.help-info').tooltip();

  var data = <?php echo $analyze->getJsonData() ?>,
    b_score_data = <?php echo $analyze->getCumulativeJsonData() ?>,
    b_score = $('#b-score'),
    pool = $('#cost-pool'),
    unallocated = $('#unallocated'),
    cost_criteria = $('#cost-criteria'),
    table = $('#cost-estimate-table'),
    force_in = $('.force-in'),
    force_out = $('.force-out'),
    item_numb = $('.cost-alternative').length,
    red_line_drawn = false,
    order = [];

  $('#cost-pin-to-wall').click(function () {
    var params = {}, red_line = $('.red-line-bottom, .red-line-top'),
      criterion = data[cost_criteria.val()];

    for (var i = 0; i < order.length; i++) {
      order[i].price = criterion[order[i].id];
    }

    params.order = order;
    params.criterion_id = cost_criteria.val();
    params.pool = pool.val();
    params.unallocated = unallocated.val();
    params.b_score = b_score.html();

    if (red_line.length) {
      params.red_line = { top: red_line.hasClass('red-line-top'), alternative_id: red_line.attr('id').replace('cost-alternative-', '') };
    }

    $.post('<?php echo url_for('@analyze\pinToWall?type=cost') ?>', params);
  });

  $('#cost-active-pin-to-wall').click(function () {
    var params = {}, red_line = $('.red-line-bottom, .red-line-top'),
      criterion = data[cost_criteria.val()];

    for (var i = 0; i < order.length; i++) {
      order[i].price = criterion[order[i].id];
    }

    params.order = order;
    params.criterion_id = cost_criteria.val();
    params.pool = pool.val();
    params.unallocated = unallocated.val();
    params.b_score = b_score.html();

    if (red_line.length) {
      params.red_line = { top: red_line.hasClass('red-line-top'), alternative_id: red_line.attr('id').replace('cost-alternative-', '') };
    }

    $.post('<?php echo url_for('@analyze\activePinToWall?type=cost') ?>', {params: params});
  });

  // When criteria chart updated
  table.bind('costUpdate', function (event, response) {
    b_score_data = response.bScoreData;
    data = response.data;

    table.find('tr').remove();
    var alternatives = response.alternatives, row, cell, botton;
    for (var alternative_id in alternatives) {
      row = $('<tr class="cost-alternative"></tr>').attr('id', 'cost-alternative-' + alternative_id);
      botton = $('<a class="force-in" style="display: none" href="javascript:void(0)"></a>');
      botton.click(function () {
        triggerForce.apply(this);
      });
      cell = $('<td></td>');
      cell.append(botton);
      botton = $('<a class="force-out" style="display: none" href="javascript:void(0)"></a>');
      botton.click(function () {
        triggerForce.apply(this);
      });
      cell.append(botton);
      row.append(cell);
      row.append('<td>' + alternatives[alternative_id] + '</td>');
      row.append('<td class="price"></td>');
      table.append(row);
    }

    for (var i = 0; i < order.length; i++) {
      order[i].value = $.inArray(order[i].id, response.order);
    }
    sortAlternatives();
    cost_criteria.change();
    if (red_line_drawn) {
      calculateRedLine();
    }
  });

  // Saves initial item order
  $('.cost-alternative').each(function (index) {
    order.push({ id: $(this).attr('id').replace('cost-alternative-', ''), value: item_numb - index, forced: 1 });
  });

  $('.force-in, .force-out').click(function () {
    triggerForce.apply(this);
  });

  function triggerForce() {
    var row = $(this).parent().parent(),
      id = row.attr('id').replace('cost-alternative-', ''),
      value = 1;

    if ($(this).hasClass('force-info')) {
      $(this).removeClass('force-info');
      row.removeClass($(this).hasClass('force-in') ? 'forced-in' : 'forced-out');
    } else {
      $(this).addClass('force-info');
      row.addClass($(this).hasClass('force-in') ? 'forced-in' : 'forced-out');
      value = $(this).hasClass('force-in') ? 2 : 0;
    }

    for (var item in order) {
      if (order[item].id == id) {
        order[item].forced = value;
        break;
      }
    }
    sortAlternatives();
    calculateRedLine();
  }

  function sortAlternatives() {
    order.sort(function (x, y) {
      var n = x.forced - y.forced;
      if (n != 0) {
        return n;
      }

      return x.value - y.value;
    });

    for (var item in order) {
      table.prepend($('#cost-alternative-' + order[item].id));
    }
    order.reverse();
  }

  cost_criteria.change(function () {
    var criterion = data[cost_criteria.val()],
      alternative_id,
      alternative_element,
      value;

    $('.cost-alternative').each(function () {
      alternative_element = $(this);
      alternative_id = alternative_element.attr('id').replace('cost-alternative-', '');
      value = criterion[alternative_id];
      alternative_element.find('td.price').text(new Number(value).toFixed(2));
    });
  }).change();

  function calculateRedLine() {
    var criterion = data[cost_criteria.val()],
      sum = 0,
      b_score_sum = 0,
      pool_value = pool.val(),
      prev_alternative_id,
      alternative_element,
      cost_alternative = $('.cost-alternative'),
      alternative_id,
      value,
      release_data = [];

    // Removes red line
    red_line_drawn = false;
    cost_alternative.removeClass('red-line-bottom red-line-top');
    // Hides buttons
    force_in.filter(':not(.force-info)').hide();
    force_out.filter(':not(.force-info)').hide();

    cost_alternative.each(function () {
      alternative_element = $(this);
      alternative_id = alternative_element.attr('id').replace('cost-alternative-', '');
      value = criterion[alternative_id];

      // Should red line be drawn?
      if (!red_line_drawn && (sum + criterion[alternative_id]) > pool_value && !alternative_element.hasClass('forced-in')) {
        red_line_drawn = true;
        if (prev_alternative_id) {
          // Skips forced out items
          while (alternative_element.prev().hasClass('forced-out')) {
            alternative_element = alternative_element.prev();
          }

          // Current item is not first
          if (alternative_element.prev().length) {
            alternative_element.prev().addClass('red-line-bottom');
          } else {
            unallocated.val(pool_value);
            alternative_element.addClass('red-line-top');
            return false;
          }

          unallocated.val(new Number(pool_value - sum).toFixed(2));
          b_score.text(new Number(b_score_sum).toFixed(2));
          // No one fits the cost
        } else {
          alternative_element.addClass('red-line-top');
          unallocated.val('');
          b_score.text('');
        }
      }

      if (!alternative_element.hasClass('forced-out')) {
        sum += criterion[alternative_id];
        b_score_sum += b_score_data[alternative_id];
        if (!red_line_drawn) {
          release_data.push({
            alternative_id: alternative_id,
            name          : alternative_element.find('td:eq(1)').text(),
            value         : value
          });
        }
      }
      prev_alternative_id = alternative_id;
    });

    if (red_line_drawn) {
      // Shows "In" and "Out" buttons
      var in_budget = !cost_alternative.first().hasClass('red-line-top');
      cost_alternative.each(function () {
        if (!$(this).hasClass('forced-in') && !$(this).hasClass('forced-out')) {
          in_budget ? $(this).find('.force-out').show() : $(this).find('.force-in').show();
        }
        if ($(this).hasClass('red-line-bottom')) {
          in_budget = false;
        }
      });
    } else { // All alternatives fit to cost
      unallocated.val(new Number(pool_value - sum).toFixed(2));
      b_score.text(new Number(b_score_sum).toFixed(2));
      force_out.filter(':not(.force-info)').show();
      // Red line should be above forced out items
      if (cost_alternative.filter('.forced-out:first').length) {
        if (cost_alternative.filter('.forced-out:first').prev().length) {
          cost_alternative.filter('.forced-out:first').prev().addClass('red-line-bottom');
        } else {
          cost_alternative.filter('.forced-out:first').addClass('red-line-top');
        }
      } else {
        alternative_element.addClass('red-line-bottom');
      }
    }

    $('#cost-estimate-table').trigger('costAnalyze.update', [release_data]);
  }

  $('#allocate').click(function () {
    $.post('<?php echo url_for('@analyze\logPinToWall') ?>', { criterion_id: cost_criteria.val() });

    calculateRedLine();
  });
});
</script>
<a class="btn btn-default" id="cost-pin-to-wall" href="javascript:void(0)">Paste snapshot to wall</a>
<a class="btn btn-default" id="cost-active-pin-to-wall"  href="javascript:void(0)">Display graph on wall</a>
