<?php
// TODO: Consider showing last X number sorted by activity? Maybe show stats instend of description? 
?>

<ul class="nav navbar-nav">
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="roadmaps-dropdown-link">Roadmaps <b class="caret"></b></a>
    <ul class="dropdown-menu" id="roadmaps-dropdown">
      <?php include_component('roadmap', 'topMenu') ?>
      <li><a href="<?php echo url_for('/roadmap'); ?>">Roadmaps overview</a></li>
    </ul>
  </li>
</ul>