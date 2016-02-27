<?php use_helper('sfCombine') ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <link rel="shortcut icon" href="/favicon.ico"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_combined_stylesheets() ?>
  <?php include_combined_javascripts() ?>
  <?php echo Doctrine::getTable("Scripts")->getSingleton()->backend_top; ?>
</head>
<body>
<!--[if lt IE 9]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div id="ajax-indicator"></div>
<header class="navbar navbar-inverse" role="navigation">
  <a class="navbar-brand" href="<?php echo url_for('@decision'); ?>"><img src="<?php echo image_path('app-logo.gif'); ?>"/></a>
  <?php
  if ($sf_user->isAuthenticated()){
    if ($sf_context->getModuleName() == 'roadmap'){
      include_partial('global/roadmaps_menu');
    }else{
      include_partial('global/projects_menu');
    }

    include_partial('global/user_panel');
  }
  ?>
</header>
<div id="overall-wrapper">


  <div id="page">
    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="alert alert-danger">
        <?php echo __($sf_user->getFlash('error')) ?>
      </div>
    <?php endif ?>
    <?php if ($sf_user->hasFlash('notice')): ?>
      <div class="alert alert-info">
        <?php echo __($sf_user->getFlash('notice')) ?>
      </div>
    <?php endif ?>

    <div id="page-content">
      <?php echo $sf_content ?>
    </div>
  </div>
  <div id="footer-spacer"></div>
</div>

<footer class="specific-footer">
  <?php echo Doctrine::getTable("Scripts")->getSingleton()->backend_bottom; ?>
</footer>
<?php if (!has_slot('disable_support')) : ?>

<?php endif ?>
</body>
</html>
