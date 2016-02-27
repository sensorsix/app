<?php use_helper('I18N', 'Date') ?>

<?php $sf_response->setSlot('menu_black_list_active', true) ?>
<?php include_partial('global/menu') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Black list', array(), 'messages') ?></h1>

  <?php include_partial('domain/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('domain/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('domain/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form class="form-inline" action="<?php echo url_for('domain_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('domain/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <div class="form-group">
      <?php include_partial('domain/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('domain/list_actions', array('helper' => $helper)) ?>
    </div>
    </form>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('domain/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
