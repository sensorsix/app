<?php
/**
 * @var Role $role
 */
include_component('measure', 'logo');
?>
<div class="row-fluid">
  <p>
    <?php if ($comment) : ?>
      <?php echo $sf_data->getRaw('comment') ?>
    <?php endif ?>
  </p>
</div>

<?php if ($files->count()) : ?>
  <div class="row-fluid">
    <ul class="nav nav-tabs nav-stacked">
      <?php foreach ($files as $file) : ?>
      <li>
        <a href="<?php echo url_for('@measure\download?id=' . $file->id) ?>"><?php echo $file->name ?></a>
      </li>
      <?php endforeach ?>
    </ul>
  </div>
<?php endif ?>

<div class="row-fluid">
  <div class="offset11">
    <a class="btn btn-primary" href="<?php echo url_for($role->collect_items ? '@measure\collectItems' : '@measure\measure') ?>">Next</a>
  </div>
</div>