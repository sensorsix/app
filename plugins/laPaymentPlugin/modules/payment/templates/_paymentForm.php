<?php
/** @var sfGuardUser $user */
$user = $sf_user->getGuardUser();
?>
<?php if ($widget) : ?>
<form method="post" action="<?php echo $gtw->getSubmitUrl() ?>" >
  <fieldset>
    <input type="hidden" value="pay" name="cmd">
    <div class="form-group">
      <?php echo $widget->getRawValue()->render('payment_type') ?>
      <input type="submit" class="btn btn-defautl" border="0" name="submit" value="Buy now">
    </div>
  </fieldset>
</form>
<?php endif ?>

<div class="form-group">
  Current account
  <ul class="col-xs-4 nav nav-pills nav-stacked">
    <li<?php echo $user->account_type == 'Trial' ? ' class="active"' : '' ?>><a href="javascript:void(0)">Trial</a></li>
    <li<?php echo $user->account_type == 'Basic' ? ' class="active"' : '' ?>><a href="javascript:void(0)">Basic</a></li>
    <li<?php echo $user->account_type == 'Pro' ? ' class="active"' : '' ?>><a href="javascript:void(0)">Pro</a></li>
    <li<?php echo $user->account_type == 'Enterprise' ? ' class="active"' : '' ?>><a href="javascript:void(0)">Enterprise</a></li>
  </ul>
</div>