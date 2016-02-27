<?php /** @var sfFormFieldSchema $form */ ?>
<!--[if IE]>
<script type="text/javascript" src="/libs/jquery-placeholder/jquery.placeholder.js?v=2.0.8"></script>
<script type="text/javascript">
  jQuery(function($){ $('input[placeholder], textarea[placeholder]').placeholder(); })
</script>
<![endif]-->
<div class="search-form">
  <form action="<?php echo url_for('@sf_guard_register\quick') ?>" method="post">
    <?php foreach ($form->getGlobalErrors() as $error) : ?>
      <div class="errorLabel"><?php echo $error ?></div>
    <?php endforeach ?>
    <div class="error-message">
      <?php if ($form['email_address']->hasError()) : ?>
        <div class="errorLabel email-address">
          <?php echo $form['email_address']->getError() ?>
        </div>
      <?php endif ?>
      <?php if ($form['password']->hasError()) : ?>
        <div class="errorLabel password">
          <?php echo $form['password']->getError() ?>
        </div>
      <?php endif ?>
    </div>
    <div class="left formField">
      <?php echo $form['email_address']->render(array('class' => 'email', 'placeholder' => 'Email')); ?>
    </div>
    <div class="left formField">
      <?php echo $form['password']->render(array('class' => 'password', 'placeholder' => 'Password')); ?>
    </div>
    <?php echo $form->renderHiddenFields() ?>
    <button type="submit" class="btn btn-success btn-lg btn-submit-form">Start for free now</button>
  </form>
</div>