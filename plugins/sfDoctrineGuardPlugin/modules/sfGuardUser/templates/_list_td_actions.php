<td>
  <a class="form-link btn btn-primary" href="<?php echo url_for('@sf_guard_user_edit?id=' . $sf_guard_user->id) ?>"><span class="glyphicon glyphicon-edit"></span> <?php echo __('Edit') ?></a>
  <a class="form-link btn btn-danger" onclick="return confirm('<?php echo __('Are you sure?') ?>')" href="<?php echo url_for('@user_delete?id=' . $sf_guard_user->id . '&_csrf_token=' . $csrf)?>"><span class="glyphicon glyphicon-remove"></span> <?php echo __('Delete') ?></a>
</td>
