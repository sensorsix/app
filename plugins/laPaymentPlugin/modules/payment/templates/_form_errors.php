<?php if($form->hasErrors()){ ?>
<ul class="error">
  <?php foreach ($form->getErrorSchema() as $name => $error){?>
  <li><?php echo $error ?></li>
  <?php } ?>
</ul>
<?php } ?>
