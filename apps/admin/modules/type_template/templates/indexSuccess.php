<?php
/**
 * @var sfWebResponse $sf_response
 * @var TypeTemplate[] $type_templates
 * @var int $page
 * @var int $element_count
 */
?>
<?php $sf_response->setSlot('menu_type_template_active', true) ?>
<?php include_partial('global/menu') ?>

<h1>Type templates List</h1>

<table cellspacing="0" class="table table-striped">
  <thead>
  <tr>
    <th class="sf_admin_text">Id</th>
    <th class="sf_admin_date">Name</th>
    <th class="sf_admin_date">Type Name</th>
    <th class="sf_admin_date">Alternative alias</th>
    <th class="sf_admin_date">Alternative plural alias</th>
    <th class="sf_admin_date">Partitioner alias</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($type_templates as $type_template): ?>
    <tr class="sf_admin_row odd">
      <td class="sf_admin_text"><a href="<?php echo url_for('type_template/edit?id='.$type_template->getId()) ?>"><?php echo $type_template->getId() ?></a></td>
      <td class="sf_admin_text"><?php echo $type_template->getName() ?></td>
      <td class="sf_admin_text"><?php echo $type_template->Type->getName() ?></td>
      <td class="sf_admin_text"><?php echo $type_template->getAlternativeAlias() ?></td>
      <td class="sf_admin_text"><?php echo $type_template->getAlternativePluralAlias() ?></td>
      <td class="sf_admin_text"><?php echo $type_template->getPartitionerAlias() ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<div class="row text-center">
  <ul class="pagination">
    <li <?php echo ($page == 1)? 'class="disabled"':''; ?>><a href="<?php echo ($page == 1)? '#':url_for("@type_template?page=" . ($page-1)); ?>">&laquo;</a></li>
    <?php for($i = 1; $i <= ceil($element_count / 10) ?: 0; $i++): ?>
      <li <?php echo ($page == $i)? 'class="active"':''; ?>><a href="<?php echo url_for("@type_template?page=" . $i); ?>"><?php echo $i; ?></a></li>
    <?php endfor; ?>
    <li <?php echo (!ceil($element_count / 10) || $page == ceil($element_count / 10))? 'class="disabled"':''; ?>><a href="<?php echo ($page == ceil($element_count / 10))? '#':url_for("@type_template?page=" . ($page+1)); ?>">&raquo;</a></li>
  </ul>
</div>

<a class="btn btn-default" href="<?php echo url_for('type_template/new') ?>">New</a>