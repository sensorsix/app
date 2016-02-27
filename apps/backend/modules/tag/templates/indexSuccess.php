<h1>Tags List</h1>

<table cellspacing="0" class="table table-striped">
  <thead>
  <tr>
    <th class="sf_admin_text">Id</th>
    <th class="sf_admin_date">User</th>
    <th class="sf_admin_date">Name</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($tags as $tag): ?>
    <tr class="sf_admin_row odd">
      <td><a href="<?php echo url_for('tag/edit?id='.$tag->getId()) ?>"><?php echo $tag->getId() ?></a></td>
      <td class="sf_admin_text"><?php echo $tag->getUser()->getUserName() ?></td>
      <td class="sf_admin_text"><?php echo $tag->getName() ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<div class="row text-center">
  <ul class="pagination">
    <li <?php echo ($page == 1)? 'class="disabled"':''; ?>><a href="<?php echo ($page == 1)? '#':url_for("@tag?page=" . ($page-1)); ?>">&laquo;</a></li>
    <?php for($i = 1; $i <= ceil($tags_count / 10) ?: 0; $i++): ?>
      <li <?php echo ($page == $i)? 'class="active"':''; ?>><a href="<?php echo url_for("@tag?page=" . $i); ?>"><?php echo $i; ?></a></li>
    <?php endfor; ?>
    <li <?php echo (!ceil($tags_count / 10) || $page == ceil($tags_count / 10))? 'class="disabled"':''; ?>><a href="<?php echo ($page == ceil($tags_count / 10))? '#':url_for("@tag?page=" . ($page+1)); ?>">&raquo;</a></li>
  </ul>
</div>

<a class="btn btn-primary" href="<?php echo url_for('tag/new') ?>">New</a>
