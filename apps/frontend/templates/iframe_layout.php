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

  <style>
    .width-locker {
      width: 100%;
    }
  </style>
</head>
<body>
<!--[if lt IE 9]>
<p class="browsehappy">
  You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.
</p>
<![endif]-->
<div id="overall-wrapper">
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
</body>
</html>
