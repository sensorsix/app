<select class="form-control" name="batch_action">
  <option value=""><?php echo __('Choose an action', array(), 'sf_admin') ?></option>
  <option value="batchDelete"><?php echo __('Delete', array(), 'sf_admin') ?></option>
</select>
<?php $form = new BaseForm(); if ($form->isCSRFProtected()): ?>
  <input type="hidden" name="<?php echo $form->getCSRFFieldName() ?>" value="<?php echo $form->getCSRFToken() ?>" />
<?php endif; ?>
<input class="btn btn-primary" type="submit" value="<?php echo __('go', array(), 'sf_admin') ?>" />