<?php use_javascript('/libs/jquery-rating/jquery.rating.js') ?>
<?php use_stylesheet('/libs/jquery-rating/jquery.rating.css') ?>

<h3>Summary</h3>

<?php if ($criteria->count() && $alternatives->count()) : ?>
  <table id="planned-measurement" class="table table-striped table-bordered">
    <thead>
      <tr>
        <th><?php echo __('Alternative') ?></th>
        <?php foreach ($criteria as $criterion) : ?>
          <th><?php echo $criterion->name ?></th>
        <?php endforeach ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($alternatives as $alternative) : ?>
        <tr id="alternative-<?php echo $alternative->id ?>">
          <td><?php echo $alternative->name ?></td>
          <?php foreach ($criteria as $criterion) : ?>
            <td id="criterion-<?php echo $criterion->id ?>">
              <?php if (isset($measurement[$criterion->id][$alternative->id])) : ?>
                <?php include_partial('stars', array('id' => $measurement[$criterion->id][$alternative->id], 'values' => $values, 'stars' => 5)) ?>
              <?php endif ?>
            </td>
          <?php endforeach ?>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
<?php endif ?>