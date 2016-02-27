<?php
  $id = $sf_data->getRaw('id');
  $values = $sf_data->getRaw('values');
  $name = 'measurement[' . $id . ']';
  $value = isset($values[$id]) ? $values[$id] : null;
?>
<input name="<?php echo $name ?>"<?php echo $value == 1 ? ' checked="checked"' : '' ?> type="radio" class="star required" value="1"/>
<?php for ($i = 1; $i < $stars; $i++) : ?>
<input name="<?php echo $name ?>"<?php echo $value == $i + 1 ? ' checked="checked"' : '' ?> type="radio" class="star" value="<?php echo $i + 1 ?>"/>
<?php endfor ?>
