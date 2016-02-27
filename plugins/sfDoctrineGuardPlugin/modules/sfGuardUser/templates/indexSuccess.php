<?php use_helper('I18N', 'Date') ?>

<?php $sf_response->setSlot('menu_users_active', true) ?>
<?php include_partial('global/menu') ?>

<h1 class="title"><?php echo __('User list', array(), 'messages') ?></h1>

<?php include_partial('sfGuardUser/flashes') ?>

<div id="sf_admin_header">
  <?php include_partial('sfGuardUser/list_header', array('pager' => $pager)) ?>
</div>

<!--  --><?php //if ($this->configuration->hasFilterForm()): ?>
<div id="sf_admin_bar">
  <?php include_partial('sfGuardUser/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
</div>
<!--  --><?php //endif; ?>

<div id="sf_admin_content">
  <form class="form-inline" action="<?php echo url_for('sf_guard_user_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('sfGuardUser/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <div class="form-group">
      <?php include_partial('sfGuardUser/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('sfGuardUser/list_actions', array('helper' => $helper)) ?>
    </div>
  </form>
</div>

<div id="sf_admin_footer">
  <?php include_partial('sfGuardUser/list_footer', array('pager' => $pager)) ?>
</div>

<style>
  #sf_admin_list_th_actions{
    width: 180px;
  }
</style>