<?php
// TODO: Consider showing last X number sorted by activity? Maybe show stats instend of description? 
?>

<ul class="nav navbar-nav">
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="projects-dropdown-link"><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE, true) ?></p> <b class="caret"></b></a>
    <ul class="dropdown-menu" id="projects-dropdown">
      <?php include_component('decision', 'topMenu') ?>
      <li><a href="<?php echo url_for('@homepage'); ?>"><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE, true) ?></p> overview</a></li>
    </ul>
  </li>
</ul>