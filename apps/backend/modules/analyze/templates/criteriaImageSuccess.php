<?php
/** @var CriteriaAnalyze $analyze */

$analyze = $analyze->getRawValue();
?>

<div id="criteria-chart"></div>

<script type="text/javascript">
$(function () {
  $('#criteria-chart').editableBars({
    data: <?php echo $analyze->getJsonData() ?>,
    width: <?php echo $analyze->getGraphWidth() ?>,
    height: <?php echo $analyze->getGraphHeight() ?>,
    bottom_delta: <?php echo $analyze->getGraphBottomDelta() ?>
  });
});
</script>