<?php use_helper('I18N') ?>
<!--[if IE]>
<script type="text/javascript" src="/libs/jquery-placeholder/jquery.placeholder.js?v=2.0.8"></script>
<script type="text/javascript">
  jQuery(function($){ $('input[placeholder], textarea[placeholder]').placeholder(); })
</script>
<![endif]-->
<div class="box-login jumbotron" style="padding: 48px 30px; width: 350px; margin: 0 auto;">
  <div class="row">
    <div class="col-md-12 sign-in-form-wrapper">
      <?php echo get_partial('sfGuardAuth/signin_form', array('form' => $form)) ?>
    </div>
  </div>
</div>

