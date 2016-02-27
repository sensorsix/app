<?php
if (!$form->isNew())
{
  echo link_to(__('Delete', array(), 'sf_admin'), 'sf_guard_user_delete', $form->getObject(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'btn btn-danger'));
  echo link_to(__('Back to list', array(), 'sf_admin'), '@sf_guard_user', array('class' => 'btn'));
}
?>
<input class="btn btn-primary" type="submit" value="<?php echo __('Save', array(), 'sf_admin') ?>" />
<?php if ($form->isNew()) : ?>
  <input class="btn btn-success" type="submit" value="<?php echo __('Save and add', array(), 'sf_admin') ?>" name="_save_and_add" />
<?php endif ?>
