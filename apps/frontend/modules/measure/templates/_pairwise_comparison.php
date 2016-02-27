<?php
use_javascript('/libs/jquery-ui/jquery-ui.min.js?v=1.11.2');
use_stylesheet('/libs/jquery-ui/jquery-ui.min.css?v=1.11.2');

$matrix = array(50 => 2, 33 => 3, 25 => 4, 20 => 5, 16 => 6, 14 => 7, 12 => 8, 11 => 9);
?>

<div class="form-group well-large">
  <div  class="pagination-right col-xs-2">
    <div id="tooltip-<?php echo $head->id ?>" class="hidden"><?php echo $head->getRawValue()->getTooltip() ?></div>
    <span data-tooltip_id="<?php echo $head->id ?>" class="bootstrap-tooltip pull-right"><?php echo $head->name ?></span>
  </div>
  <div class="col-xs-8" style="position: relative;">
    <div style="position: absolute; background-color: #aaaaaa; left: 52.9412%; height: 25px; width: 2px; top: 20px;" ></div>
    <div id="slider"></div>
  </div>
  <div class="bootstrap-tooltip col-xs-2">
    <div id="tooltip-<?php echo $tail->id ?>" class="hidden"><?php echo $tail->getRawValue()->getTooltip() ?></div>
    <span data-tooltip_id="<?php echo $tail->id ?>" class="bootstrap-tooltip"><?php echo $tail->name ?></span>
  </div>

  <input id="score" type="hidden" name="measurement" value="<?php echo $value ?>">
</div>

<script type="text/javascript">
$(function() {
  var score = $('#score');
  $( "#slider" ).slider({
    value: <?php echo $value >= 1 ?  $value + 9 : $matrix[intval($value * 100)] ?>,
    step: 1,
    min: 1,
    max: 18,
    slide: function(event, ui) {
      if (parseInt(ui.value / 10, 10)) {
        score.val(ui.value - 9);
      } else {
        score.val(1 / (11 - ui.value))
      }
    }
	});
});
</script>
