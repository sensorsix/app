<?php
/**
 * @var sfWebResponse $sf_response
 * @var PromoCode[] $promocodes
 * @var int $page
 * @var int $promocodes_count
 */
?>
<?php $sf_response->setSlot('menu_promo_active', true) ?>
<?php include_partial('global/menu') ?>

<h1>Promo Codes List</h1>

<table cellspacing="0" class="table table-striped">
  <thead>
  <tr>
    <th class="sf_admin_text">Id</th>
    <th class="sf_admin_date">Code</th>
    <th class="sf_admin_date">Account type</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($promocodes as $promocode): ?>
    <tr class="sf_admin_row odd">
      <td class="sf_admin_text"><a href="<?php echo url_for('promo/edit?id='.$promocode->getId()) ?>"><?php echo $promocode->getId() ?></a></td>
      <td class="sf_admin_text"><?php echo $promocode->getCode() ?></td>
      <td class="sf_admin_text"><?php echo $promocode->getAccountType() ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<div class="row text-center">
  <ul class="pagination">
    <li <?php echo ($page == 1)? 'class="disabled"':''; ?>><a href="<?php echo ($page == 1)? '#':url_for("@promo?page=" . ($page-1)); ?>">&laquo;</a></li>
    <?php for($i = 1; $i <= ceil($promocodes_count / 10) ?: 0; $i++): ?>
      <li <?php echo ($page == $i)? 'class="active"':''; ?>><a href="<?php echo url_for("@promo?page=" . $i); ?>"><?php echo $i; ?></a></li>
    <?php endfor; ?>
    <li <?php echo (!ceil($promocodes_count / 10) || $page == ceil($promocodes_count / 10))? 'class="disabled"':''; ?>><a href="<?php echo ($page == ceil($promocodes_count / 10))? '#':url_for("@promo?page=" . ($page+1)); ?>">&raquo;</a></li>
  </ul>
</div>

<a class="btn btn-default" href="<?php echo url_for('promo/new') ?>">New</a>