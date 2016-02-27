<?php use_helper('I18N', 'Date') ?>

<?php $sf_response->setSlot('menu_black_list_active', true) ?>
<?php include_partial('global/menu') ?>

<div id="sf_admin_container">
  <h1><?php echo __('New Domain', array(), 'messages') ?></h1>

  <?php include_partial('domain/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('domain/form_header', array('domain' => $domain, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('domain/form', array('domain' => $domain, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('domain/form_footer', array('domain' => $domain, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>
</div>
