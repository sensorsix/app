<?php
/**
 * @var sfContext $sf_context
 * @var sfUser $sf_user
 */

?>

<ul class="nav nav-pills nav-stacked user-nav">
  <li class="<?php if ($sf_context->getActionName() == 'index') echo "active"; ?>"><a href="<?php echo url_for('@user_profile'); ?>"><?php echo __("My profile");?></a></li>

  <?php if ($sf_user->isSuperAdmin() || in_array($sf_user->getGuardUser()->account_type, array('Trial', 'Enterprise'))): ?>
    <li class="<?php if ($sf_context->getActionName() == 'members') echo "active"; ?>"><a href="<?php echo url_for('@user_profile\members'); ?>"><?php echo __("Members");?></a></li>
    <li class="<?php if ($sf_context->getActionName() == 'design') echo "active"; ?>"><a href="<?php echo url_for('@user_profile\design'); ?>"><?php echo __("Design");?></a></li>
    <li class="<?php if ($sf_context->getActionName() == 'templateEditor') echo "active"; ?>"><a href="<?php echo url_for('@user_profile\templateEditor'); ?>"><?php echo __("Template editor");?></a></li>
  <?php endif; ?>

  <?php if ($sf_user->getGuardUser()->hasAPIAccess()): ?>
    <li class="<?php if ($sf_context->getActionName() == 'api') echo "active"; ?>"><a href="<?php echo url_for('@user_profile\api'); ?>"><?php echo __("API");?></a></li>
  <?php endif; ?>

  <li class="<?php if ($sf_context->getActionName() == 'labels') echo "active"; ?>"><a href="<?php echo url_for('@user_profile\labels'); ?>"><?php echo __("Labels");?></a></li>
</ul>
