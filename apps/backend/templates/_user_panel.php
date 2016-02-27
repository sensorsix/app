<ul class="nav navbar-nav navbar-right user-panel" id="userPanel" data-userid="<?php echo $sf_user->getGuardUser()->getId();?>" data-userName="<?php echo $sf_user->getGuardUser()->getUserName();  ?>">
  <li class="dropdown">
    <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#" ><i class="glyphicon glyphicon-user"></i> <?php echo $sf_user->getGuardUser()->__toString();  ?>
      <span class="caret"></span></a>
    <ul class="dropdown-menu">
      <?php if ($sf_user->hasCredential('admin')) : ?>
        <li><?php echo link_to('<i class="glyphicon glyphicon-cog"></i> Administration', '/administration') ?></li>
        <li class="divider"></li>
      <?php endif ?>
      <li><?php echo link_to('<i class="glyphicon glyphicon-th-list"></i> My ' . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE, true), '@decision') ?></li>
      <?php if ($sf_user->hasRoadmapAccess()) : ?>
        <li><?php echo link_to('<i class="glyphicon glyphicon-road"></i> My roadmaps', '@roadmap') ?></li>
      <?php endif ?>
      <li><?php echo link_to('<i class="glyphicon glyphicon-user"></i> My account', '@user_profile') ?></li>
      <li><?php echo link_to('<i class="glyphicon glyphicon-tags"></i> Tags', '@tag') ?></li>
      <li class="divider"></li>
      <li><a href="<?php echo url_for('@sf_guard_signout') ?>"><i class="glyphicon glyphicon-log-out"></i>  <?php echo __('Log out') ?></a></li>
    </ul>
  </li>
</ul>
