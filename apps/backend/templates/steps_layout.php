<?php
/**
 * @var sfComponent $sf_context
 * @var sfGuardSecurityUser $sf_user
 */

use_helper('sfCombine');
?>
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
<!--[if lt IE 9]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<header class="navbar navbar-inverse" role="navigation">
  <a class="navbar-brand" href="<?php echo url_for('@homepage'); ?>"><img src="<?php echo image_path('app-logo.gif'); ?>"/></a>
  <?php
  if ($sf_user->isAuthenticated()){
    if ($sf_context->getModuleName() == 'roadmap'){
      include_partial('global/roadmaps_menu');
    }else{
      include_partial('global/projects_menu');
    }

    include_partial('global/user_panel');

    include_partial('global/search');
  }
  ?>
</header>

<!-- TODO make this to sidebar-->
<?php include_slot('sidebar'); ?>

<div id="page">
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
    <section class="content-header">
      <div class="row">
        <div class="col-md-6">
          <h1><?php include_slot('app_name'); ?></h1>
        </div>
        <div class="col-md-6">
          <div class="pull-right">
            <h4><?php include_slot('project_name'); ?></h4>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 appbar1">
          <?php include_slot('app_menu'); ?>
        </div>
        <div class="col-md-6">
          <div class="pull-right toolbar">
            <?php include_slot('app_toolbar'); ?>
          </div>
        </div>
      </div>
    </section>
    <hr>
    <?php include_slot('section1'); ?>
    <?php echo $sf_content; ?>
    <?php include_slot('closure'); ?>
  </div>
</div>

<footer>
  <?php echo Doctrine::getTable("Scripts")->getSingleton()->backend_bottom; ?>
</footer>
<script>
  $(function () {
    <?php if ($sf_user->isAuthenticated() && $sf_user->getGuardUser()->isExpired()) :  ?>
    alert('Your subscription is expired');
    <?php endif ?>
  });
</script>

<?php  /*if (!has_slot('disable_support')) : ?>

<?php endif */?>
</body>
</html>
