<?php
/**
 * @var sfWebResponse $sf_response
 * @var Log[] $logs
 * @var int $page
 * @var int $logs_count
 */
?>
<?php use_helper('I18N', 'Date') ?>
<?php $sf_response->setSlot('menu_logs_active', true) ?>
<?php include_partial('global/menu') ?>

<h1>Logs List</h1>

<table cellspacing="0" class="table table-striped">
  <thead>
  <tr>
    <th class="sf_admin_text">Id</th>
    <th class="sf_admin_date">User</th>
    <th class="sf_admin_date">Created at</th>
    <th class="sf_admin_date">Action</th>
    <th class="sf_admin_date">Information</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($logs as $log): ?>
    <tr class="sf_admin_row odd">
      <td><?php echo $log->getId() ?></td>
      <td class="sf_admin_text"><?php echo $log->getUser()->getUserName() ?></td>
      <td class="sf_admin_date"><?php echo $log->getCreatedAt() ?></td>
      <td class="sf_admin_date"><?php echo $log->actionToStr($log->getAction()) ?></td>
      <td class="sf_admin_date">
        <?php if ($log->getInformation()): ?>
          <?php foreach(json_decode(str_replace('&quot;', '"', $log->getInformation())) as $k => $v): ?>
            <p><?php echo $k . ': ' . $v; ?></p>
          <?php endforeach; ?>
        <?php endif;?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<div class="row text-center">
  <ul class="pagination">
    <li <?php echo ($page == 1)? 'class="disabled"':''; ?>><a href="<?php echo ($page == 1)? '#':url_for("@logs?page=" . ($page-1)); ?>">&laquo;</a></li>
    <?php for($i = 1; $i <= ceil($logs_count / 10) ?: 0; $i++): ?>
      <li <?php echo ($page == $i)? 'class="active"':''; ?>><a href="<?php echo url_for("@logs?page=" . $i); ?>"><?php echo $i; ?></a></li>
    <?php endfor; ?>
    <li <?php echo (!ceil($logs_count / 10) || $page == ceil($logs_count / 10))? 'class="disabled"':''; ?>><a href="<?php echo ($page == ceil($logs_count / 10))? '#':url_for("@logs?page=" . ($page+1)); ?>">&raquo;</a></li>
  </ul>
</div>