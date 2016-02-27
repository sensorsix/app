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
  <script src="//cdn.optimizely.com/js/554202023.js"></script>
  <?php include_combined_javascripts() ?>
</head>
<body>

<div id="overall-wrapper">
  <header>
    <div class="width-locker">
      <a class="logo" href="/"><img src="<?php echo image_path('logo.png'); ?>" alt=""/></a>
      <?php if ($sf_user->isAuthenticated()) include_partial('global/user_panel') ?>
    </div>
  </header>

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

<footer></footer>
</body>
</html>
