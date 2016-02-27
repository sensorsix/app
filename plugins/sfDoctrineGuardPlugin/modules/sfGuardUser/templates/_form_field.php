<?php if ($field->isPartial()): ?>
  <?php include_partial('sfGuardUser/'.$name, array('form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?>
<?php elseif ($field->isComponent()): ?>
  <?php include_component('sfGuardUser', $name, array('form' => $form, 'attributes' => $attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes)) ?>
<?php else: ?>
  <div class="form-group <?php echo $class ?><?php $form[$name]->hasError() and print ' errors' ?>">
    <?php if ($name == 'types_list'): ?>

      <?php echo $form[$name]->renderLabel($label, array('class' => 'col-sm-3 control-label')) ?>
      <div class="col-sm-6">
        <?php echo $form[$name]->render(array()) ?>
        <p>
          <?php echo $form[$name]->renderError() ?>
        </p>
      </div>
      <?php if ($help): ?>
        <div class="help"><?php echo __($help, array(), 'messages') ?></div>
      <?php elseif ($help = $form[$name]->renderHelp()): ?>
        <div class="help"><?php echo $help ?></div>
      <?php endif; ?>

    <?php else: ?>
      <?php echo $form[$name]->renderLabel($label, array('class' => 'col-sm-3 control-label')) ?>
      <div class="col-sm-6">
        <?php echo $form[$name]->render(array('class' => 'form-control')) ?>
        <p>
          <?php echo $form[$name]->renderError() ?>
        </p>
      </div>
      <?php if ($help): ?>
        <div class="help"><?php echo __($help, array(), 'messages') ?></div>
      <?php elseif ($help = $form[$name]->renderHelp()): ?>
        <div class="help"><?php echo $help ?></div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>
