<?php
/**
 * @var $sf_context SfContext
 * @var $roadmap_id int
 * @var $decision_id int
 */

$module_name = $sf_context->getModuleName();
$action_name = $sf_context->getActionName();
?>

<div id="sidebar">
  <a class="main-menu<?php if (in_array($module_name, array('decision', 'dashboard', 'role', 'analyze', 'wall', 'planner', 'alternative', 'criterion', 'response')) && !in_array($action_name, array('portfolio', 'personas', 'strategy'))) echo '-active'; ?>" href="<?php echo url_for('decision') ?>"><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE, true) ?></p></a>
  <?php if (in_array($module_name, array('dashboard', 'role', 'analyze', 'wall', 'planner', 'alternative', 'criterion', 'response'))): ?>
    <a class="<?php if ($module_name === 'dashboard') echo 'active'; ?>" href="<?php echo url_for('dashboard', array('decision_id' => $decision_id)) ?>">
      <i class="fa fa-tachometer"></i> Overview
    </a>
    <a class="<?php if (in_array($module_name, array('role', 'response'))) echo 'active'; ?>" id="menu-link-collaborate" href="<?php echo url_for('role', array('decision_id' => $decision_id)) ?>">
      <i class="fa fa-comments-o"></i> Collaborate
    </a>

    <?php if ($sf_user->getGuardUser()->account_type !== 'Light'): ?>
      <a class="<?php if ($module_name === 'analyze' && $action_name === 'index') echo 'active'; ?>" href="<?php echo url_for('analyze', array('decision_id' => $decision_id)) ?>">
        <i class="fa fa-bar-chart-o"></i> Analyze
      </a>
      <a class="<?php if ($module_name === 'wall') echo 'active'; ?>" href="<?php echo url_for('wall', array('decision_id' => $decision_id)) ?>">
        <i class="fa fa-clipboard"></i> Wall
      </a>
    <?php endif; ?>

    <a class="<?php if ($module_name === 'analyze' && $action_name === 'planner') echo 'active'; ?>" href="<?php echo url_for('planner', array('decision_id' => $decision_id)) ?>">
      <i class="fa fa-cubes"></i> Planner
    </a>
  <?php endif; ?>
  <?php
  /*
  <?php if ($sf_user->hasRoadmapAccess()) : ?>
    <a class="main-menu<?php if ($module_name === 'roadmap') echo '-active'; ?>" href="<?php echo url_for('roadmap') ?>">Roadmaps</a>
    <?php if ($module_name === 'roadmap' && in_array($action_name, array('timeline', 'dashboard'))): ?>
      <a class="<?php if ($action_name === 'timeline') echo 'active'; ?>" href="<?php echo url_for('roadmap/timeline?id=' . $roadmap_id) ?>">
        <i class="fa fa-road"></i> Timeline
      </a>
      <a class="<?php if ($action_name === 'dashboard') echo 'active'; ?>" href="<?php echo url_for('roadmap/dashboard?id=' . $roadmap_id) ?>">
        <i class="fa fa-list"></i> List view
      </a>
    <?php endif; ?>
  <?php endif; ?>

  <a class="main-menu<?php if ($module_name === 'decision' && $action_name === 'portfolio') echo '-active'; ?>" href="<?php echo url_for('decision\portfolio') ?>">Portfolio</a>
  <a class="main-menu<?php if ($module_name === 'decision' && $action_name === 'personas') echo '-active'; ?>" href="<?php echo url_for('decision\personas') ?>">Personas</a>
  <a class="main-menu<?php if ($module_name === 'decision' && $action_name === 'strategy') echo '-active'; ?>" href="<?php echo url_for('decision\strategy') ?>">Strategy</a>
*/
?>
</div>
