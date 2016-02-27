<?php
/**
 * @var CriteriaTemplate[] $criteria
 * @var int $template_id
 */
?>

<table cellspacing="0" class="table table-striped">
  <thead>
  <tr>
    <th class="sf_admin_text">Id</th>
    <th class="sf_admin_date">Name</th>
    <th class="sf_admin_date">Variable Type</th>
    <th class="sf_admin_date">Measurement</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($criteria as $criterion): ?>
    <tr class="sf_admin_row odd">
      <td class="sf_admin_text"><a href="<?php echo url_for('type_template/criteriaEdit?id='.$criterion->getId()) ?>"><?php echo $criterion->getId() ?></a></td>
      <td class="sf_admin_text"><?php echo $criterion->getName() ?></td>
      <td class="sf_admin_text"><?php echo $criterion->getVariableType() ?></td>
      <td class="sf_admin_text"><?php echo $criterion->getMeasurement() ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<a class="btn btn-default" href="<?php echo url_for('type_template/criteriaNew?template_id='.$template_id) ?>">New Criteria</a>