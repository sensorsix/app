<?php
/**
 * @var Criterion $criterion
 */
?>
<div class="form-group text-center">
  <div class="col-xs-12 col-md-8 col-md-offset-2">
    <h2 style="margin-top: 0"><?php echo $criterion->name ?></h2>
  </div>
</div>
<?php if ($criterion->description) : ?>
  <style>
    ul, li { list-style: disc }
    ol > li { list-style: decimal }
  </style>
<div class="form-group">
  <div class="col-xs-12 col-md-8 col-md-offset-2"><?php echo $criterion->getRawValue()->description ?></div>
</div>
<?php endif; ?>
