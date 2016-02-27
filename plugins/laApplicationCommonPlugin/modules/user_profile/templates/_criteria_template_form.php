<td>
  <?php echo $form->renderGlobalErrors() ?>
  <?php echo $form['name']->renderLabel(null,array('style'=>'font-weight:normal')) ?>
  <?php echo $form['name']->render() ?>
  <?php echo $form->renderHiddenFields(false) ?>
  <span id="criterion-fetch-url" data-url="<?php echo url_for('@user_profile\criteriaTemplateFetch?id=' . $form->getObject()->id) ?>"></span>
</td>
<td>
  <?php echo $form['variable_type']->renderLabel(null,array('style'=>'font-weight:normal')) ?>
  <?php echo $form['variable_type']->render() ?>
</td>
<td>
  <?php echo $form['measurement']->renderLabel(null,array('style'=>'font-weight:normal')) ?>
  <?php echo $form['measurement']->render() ?>
</td>
<td class="middle">
  <a href="javascript:void(0)" title="Delete" class="delete glyphicon glyphicon-remove-circle btn btn-danger btn-small"></a>
  <a href="javascript:void(0)" title="Collapse" class="collapse glyphicon glyphicon-resize-small btn btn-default btn-small"></a>
  <script>
  $(function () {
    $(".c-autosave").autosave({
      url     : "<?php echo url_for('@user_profile\criteriaTemplateUpdate?id=' . $form->getObject()->id) ?>",
      method  : "post",
      grouped : true,
      success : function (response) {
        // Server validation failed
        if (response) {
          //$('#form').html(response);
        }
      },
      dataType: "html"
    });
  });
  </script>
</td>