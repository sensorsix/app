<?php
// jp 16/6 hiding tabs completely if no project is enabled

if ($sf_request->hasParameter('decision_id')): ?>
  <ul id="menu" class="nav nav-pills small">
    <li<?php echo has_slot('menu_alternative_active') ? ' class="active"' : ($sf_request->hasParameter('decision_id') ? '' : ' class="disabled"') ?>>
      <a id="menu-link-alternative" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@alternative?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa-tasks"></i> <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?></p></a>
    </li>
    <li<?php echo has_slot('menu_criterion_active') ? ' class="active"' : ($sf_request->hasParameter('decision_id') ? '' : ' class="disabled"') ?>>
      <a id="menu-link-criterion" href="<?php echo $sf_request->hasParameter('decision_id') ? url_for('@criterion?decision_id=' . $sf_request->getParameter('decision_id')) : 'javascript:void(0)' ?>"><i class="fa fa-filter"></i> Criteria</a>
    </li>
  </ul>
<?php endif; ?>
