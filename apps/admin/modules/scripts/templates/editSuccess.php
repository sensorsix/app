<?php use_helper('I18N', 'Date') ?>

<?php $sf_response->setSlot('menu_scripts_active', true) ?>
<?php include_partial('global/menu') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Edit Scripts', array(), 'messages') ?></h1>

  <?php include_partial('scripts/flashes') ?>


  <div id="sf_admin_header">
    <?php include_partial('scripts/form_header', array('scripts' => $scripts, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('scripts/form', array('scripts' => $scripts, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('scripts/form_footer', array('scripts' => $scripts, 'form' => $form, 'configuration' => $configuration)) ?>
  </div>
</div>

<style type="text/css">
  #scripts_backend_top, #scripts_backend_bottom,
  #scripts_frontend_top, #scripts_frontend_bottom{
    width:100%;
    font-size:12px;
    color:blue;
    height:200px;
    font-family: consolas;
  }
</style>