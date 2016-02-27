<?php /** @var sfGuardUser $user */ ?>
<?php slot('logo') ?>
<header class="site-header" style="background-color: <?php echo $user->header_color ?>">
  <div class="layout-header width-locker">
    <?php if ($user->logo_file) : ?>
      <a class="logo" href="<?php echo $user->logo_url ? $user->logo_url : 'javascript:void(0)' ?>">
        <img style="height: 57px" src='/uploads/logo/<?php echo $user->logo_file ?>' alt="Logo"/>
      </a>
    <?php else : ?>
      <a class="logo" href="/"><img src="<?php echo image_path('logo.png'); ?>" alt=""/></a>
    <?php endif ?>
  </div>
</header>
<?php end_slot() ?>
<?php slot('powered_by') ?>
<div class="col-xs-12" style="padding-top: 40px">
  <div class="pull-right" style="padding-right: 15px;">
    <span style="color: white; font-weight: bold">powered by </span><a href="http://sensorsix.com" target="_blank"><img style="height: 30px" src="<?php echo image_path('logo.png'); ?>" alt=""/></a>
  </div>
</div>
<?php end_slot() ?>
