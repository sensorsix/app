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
  <?php echo Doctrine::getTable("Scripts")->getSingleton()->frontend_top; ?>
</head>
<body>
<!--[if lt IE 9]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<div id="overall-wrapper">
  <?php if (has_slot('logo')) : ?>
    <?php echo get_slot('logo') ?>
  <?php else : ?>
    <header class="site-header">
      <div class="layout-header width-locker">
        <a class="logo" href="/"><img src="<?php echo image_path('logo.png'); ?>" alt=""/></a>
        <?php if (!has_slot('disable_top_menu')) : ?>
          <ul class="main-menu">
            <li><a href="<?php echo url_for('@about') ?>">About</a></li>
            <li><a href="/#pricing">Pricing</a></li>
            <li><a href="<?php echo url_for('@products') ?>">Product tour</a></li>
            <li><a href="<?php echo url_for('@customers') ?>">Customers</a></li>
            <li><a href="<?php echo url_for('@support') ?>">Help</a></li>
          </ul>
        <?php endif; ?>
        <div class="right-nav">
          <?php if ($sf_user->isAuthenticated()): ?>
            <?php include_partial('global/user_panel'); ?>
          <?php else: ?>
            <a href="<?php echo url_for('sf_guard_signin') ?>" class="btn btn-gray btn-login">Login</a>
          <?php endif; ?>
          <?php if (!has_slot('disable_top_menu')) : ?>
            <ul class="page-link">
              <li><a href="/blog">Blog</a></li>
              <li><a href="/contact">Contact</a></li>
            </ul>
          <?php endif ?>
        </div>
      </div>
    </header>
  <?php endif ?>
  <div id="page" class="width-locker">
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
<footer>
  <?php if (has_slot('powered_by')) : ?>
    <?php echo get_slot('powered_by') ?>
  <?php endif ?>
  <?php echo Doctrine::getTable("Scripts")->getSingleton()->frontend_bottom; ?>
</footer>
</body>
</html>
