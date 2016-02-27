<td>
  <?php echo $form->renderGlobalErrors() ?>
  <div class="mb10">
    <?php echo $form['name']->renderLabel(null,array('style'=>'font-weight:normal')) ?>
    <?php echo $form['name']->render() ?>
  </div>
  <?php echo $form['type_id']->renderLabel(null,array('style'=>'font-weight:normal')) ?>
  <?php echo $form['type_id']->render() ?>
  <?php echo $form->renderHiddenFields(false) ?>
  <span id="fetch-url" data-url="<?php echo url_for('@user_profile\templateFetch?id=' . $form->getObject()->id) ?>"></span>
</td>
<td>
  <a id="add-template" data-toggle="tooltip" title="New item" data-placement="bottom" href="javascript:void(0)" class="glyphicon glyphicon-plus btn btn-default"></a>
  <table id="criteria-view" class="table table-bordered">
    <tbody class="main"></tbody>
  </table>
</td>
<td style="width: 105px" class="alignright">
  <a href="javascript:void(0)" title="Collapse" class="collapse glyphicon glyphicon-resize-small btn btn-default btn-small"></a>
  <a href="javascript:void(0)" title="Delete" class="delete glyphicon glyphicon-remove-circle btn btn-danger btn-small edit-delete"></a>

  <script type="text/template" id="criterionTemplate">
   <td><%= name %></td>
   <td><%= type %></td>
   <td><%= measurement %></td>

   <td style="width: 105px" class="middle alignright">
     <a href="javascript:void(0)" class="edit glyphicon glyphicon-plus btn btn-primary btn-small"></a>
     <a href="javascript:void(0)" class="delete glyphicon glyphicon-remove-circle btn btn-danger btn-small"></a>
   </td>
  </script>

  <script>
  $(function () {
    var rowCollection = new RowCollection();

    var tableView = new TableViewOld({ collection: rowCollection, el: $('#criteria-view'), rowTemplate: '#criterionTemplate' });
    rowCollection.reset(<?php echo $sf_data->getRaw('collection_json') ?>);

    $('#add-template').on('click', function() {
      $.get('<?php echo url_for('@user_profile\criteriaTemplateNew?template_id=' . $form->getObject()->id) ?>', function (response) {
        var object = new RowObject();
        object.set('id', $('#alternative_id', response).val());
        object.set('fetch_url', $('#criterion-fetch-url', response).data('url'));
        object.set('delete_url', '<?php echo url_for('@user_profile\templateDelete') ?>');
        tableView.addNew(object, response);
      });
    });

    $(".autosave").autosave({
      url     : "<?php echo url_for('@user_profile\templateUpdate?id=' . $form->getObject()->id) ?>",
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

