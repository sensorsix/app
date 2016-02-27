<div class="btn-toolbar pull-right">
  <div class="btn-group">
    <button data-toggle="dropdown" class="btn btn-success btn-lg dropdown-toggle"><?php echo $sf_user->getGuardUser();  ?> <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <?php if ($sf_user->hasCredential('admin')) : ?>
        <li><?php echo link_to('Administration', '/administration') ?></li>
      <?php endif ?>
      <li><?php echo link_to('My projects', '/project') ?></li>
      <?php if ($sf_user->hasRoadmapAccess()) : ?>
        <li><?php echo link_to('My roadmaps', '/roadmap') ?></li>
      <?php endif ?>
      <li><?php echo link_to('My account', '/project/user/account') ?></li>
      <li class="divider"></li>
      <li><a href="<?php echo url_for('@sf_guard_signout') ?>"><?php echo __('Log out') ?></a></li>
    </ul>
  </div>
</div>
