<?php use_helper('sfCombine') ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <link rel="shortcut icon" href="/favicon.ico"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
  <?php include_combined_stylesheets() ?>
  <?php include_combined_javascripts() ?>
  <?php echo Doctrine::getTable("Scripts")->getSingleton()->backend_top; ?>
</head>
<body>

<header class="navbar navbar-inverse" role="navigation">
  <a class="navbar-brand" href="<?php echo url_for('@decision'); ?>"><img src="<?php echo image_path('app-logo.gif'); ?>"/></a>
  <?php
    if ($sf_user->isAuthenticated()){
      include_partial('global/projects_menu');
    }
    include_partial('global/user_panel');
  ?>
</header>

<div id="page" class="width-locker">
  <div id="ajax-indicator"></div>

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
    <?php include_slot('navigation_links') ?>
    <div class="content-wrapper">
      <?php echo $sf_content ?>
    </div>
  </div>
</div>
<div id="footer-spacer"></div>

<footer>
  <?php echo Doctrine::getTable("Scripts")->getSingleton()->backend_bottom; ?>
</footer>
<style type="text/css">
  .step-next{
    z-index: 5;
    position:absolute;
    right:10px;
    top:0;
    line-height:300px;
    font-size: 60px;
    color:#5aa659;
  }

  .step-prev{
    z-index: 5;
    position:absolute;
    left:10px;
    top:0;
    line-height:300px;
    font-size: 60px;
    color:#5aa659;
  }
</style>
</body>
</html>
