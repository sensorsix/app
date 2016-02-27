<td>
  <div style="margin-bottom: 0" class="form-group">
    <div class="col-xs-12">
      <div class="field-wrapper">
        <?php echo $form['name']->renderError() ?>
        <?php echo $form['name']->render(array('class' => 'form-control autosave', 'data-default' => $form['name']->getValue())) ?>
        <?php echo $form->renderHiddenFields(false) ?>
      </div>
    </div>
  </div>
</td>
<td colspan="4"></td>
<td>
  <?php if ($form->getObject()->deletable): ?>
    <a href="javascript:void(0)" class="delete glyphicon glyphicon-remove-circle btn btn-danger btn-small"></a>
  <?php endif ?>
  <span id="edit-url" data-url="<?php echo url_for('@roadmap\editFolder?id=' . $form->getObject()->id) ?>"></span>
  <script>
    $(function () {
      var default_text = '<?php echo 'New ' . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::FOLDER_TYPE) ?>';

      $('#folder_name').on('change', function () {
        if (!$(this).val()) {
          $(this).val(default_text);
        }
      }).on('focus', function () {
        if ($(this).val() == default_text) {
          $(this).val('');
        }
      });

      $(".autosave").autosave({
        url     : "<?php echo url_for('@roadmap\updateFolder?id=' . $form->getObject()->id) ?>",
        method  : "post",
        grouped : true,
        success : function (response) {
          // Server validation failed
          if (response) {
            $('#form').html(response);
          }
        },
        dataType: "html"
      });
    });
  </script>
</td>

 