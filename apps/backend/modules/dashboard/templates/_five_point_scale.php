<?php
  $name = 'measurement[' . $alternative_id . '][' . $criterion_id . ']';
?>
<input name="<?php echo $name ?>"<?php echo $value == 1 ? ' checked="checked"' : '' ?> type="radio" class="dashboard-star required" value="1"/>
<?php for ($i = 1; $i < 5; $i++) : ?>
<input name="<?php echo $name ?>"<?php echo $value == $i + 1 ? ' checked="checked"' : '' ?> type="radio" class="dashboard-star" value="<?php echo $i + 1 ?>"/>
<?php endfor ?>
