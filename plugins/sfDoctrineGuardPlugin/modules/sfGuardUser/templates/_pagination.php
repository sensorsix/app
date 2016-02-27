<div class="sf_admin_pagination" style="text-align: center;">
  <ul class="pagination">
    <li><a href="<?php echo url_for('@sf_guard_user') ?>?page=1">&laquo;</a></li>
    <li><a href="<?php echo url_for('@sf_guard_user') ?>?page=<?php echo $pager->getPreviousPage() ?>">&#8249;</a></li>
      <?php foreach ($pager->getLinks() as $page): ?>
      <li>
      <?php if ($page == $pager->getPage()): ?>
        <a style="color: #000000" href="javascript:void(0)"><?php echo $page ?></a>
      <?php else: ?>
        <a href="<?php echo url_for('@sf_guard_user') ?>?page=<?php echo $page ?>"><?php echo $page ?></a>
      <?php endif; ?>
      </li>
      <?php endforeach; ?>
    <li><a href="<?php echo url_for('@sf_guard_user') ?>?page=<?php echo $pager->getNextPage() ?>">&#8250;</a></li>
    <li><a href="<?php echo url_for('@sf_guard_user') ?>?page=<?php echo $pager->getLastPage() ?>">&raquo;</a></li>
  </ul>
</div>
