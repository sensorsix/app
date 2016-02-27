<div class="box-login jumbotron" style="padding: 48px 30px;">
  <h2 class="title">Sign up</h2>

  <form method="post">
    <?php echo $form->renderHiddenFields(false) ?>
    <?php echo $form->renderGlobalErrors() ?>
    <div class="form-group">
      <?php echo $form['email_address']->renderError() ?>
      <?php echo $form['email_address']->render(array('placeholder' => 'Email', 'style' => 'text-align:center', 'class' => 'form-control')) ?>
    </div>
    <div class="form-group">
      <?php echo $form['password']->renderError() ?>
      <?php echo $form['password']->render(array('placeholder' => 'Password', 'style' => 'text-align:center', 'class' => 'form-control')) ?>
    </div>
    <div class="form-group">
      <?php echo $form['promo_code']->renderError() ?>
      <?php echo $form['promo_code']->render(array('placeholder' => 'Promotion code', 'style' => 'text-align:center', 'class' => 'form-control')) ?>
    </div>
    <div class="form-group">
      <input class="btn btn-success btn-block" type="submit" value="<?php echo __('Register', null, 'sf_guard') ?>"/>
    </div>
    <div class="additional-links">
      <p><a href="mailto:<?php echo sfConfig::get('app_info_email')?>">Contact</a></p>
    </div>
  </form>

  <style type="text/css">
    #page {
      text-align: center;
    }

    .logo {
      border: 0 !important;
    }

    .contentWrapper {
      margin-top: 50px;
    }

    .box-login {
      width: 333px;
      margin: 0 auto;
    }

    #login table th,
    #login table td {
      border: 0 !important;
    }
  </style>
</div>