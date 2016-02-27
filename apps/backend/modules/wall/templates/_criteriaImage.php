<?php
/** @var CriteriaAnalyze $analyze */
$analyze = $analyze->getRawValue();
use_javascript('/libs/jquery-editablebars/jquery.editablebars.js?v=1.0');
$rand = uniqid(rand(1, 1000));
?>

<div id="criteria-chart-<?php echo $rand; ?>"></div>

<script type="text/javascript">
$(function () {
  $('#criteria-chart-<?php echo $rand; ?>').editableBars({
    data: <?php echo $analyze->getJsonData() ?>,
    width: <?php echo $analyze->getGraphWidth() ?>,
    height: <?php echo $analyze->getGraphHeight() ?>,
    bottom_delta: <?php echo $analyze->getGraphBottomDelta() ?>
  });
});
</script>