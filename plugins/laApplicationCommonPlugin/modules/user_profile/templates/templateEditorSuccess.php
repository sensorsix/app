<?php
/**
 *
 */
?>
<div class="row">
  <div class="col-md-3">
    <?php include_partial("tabs"); ?>
  </div>
  <div class="col-md-9">
    <h1 class="title"><?php echo __('Template editor') ?></h1>
    <div class="form-group">
      <a id="add-row" data-toggle="tooltip" title="New item" data-placement="bottom" href="javascript:void(0)" class="glyphicon glyphicon-plus btn btn-default help-info"></a>
      <table id="table-view" class="table">
        <thead>
        <tr>
          <th><?php echo __('Name') ?></th>
          <th><?php echo __('Type') ?></th>
          <th></th>
        </tr>
        </thead>
        <tbody class="main"></tbody>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(function() {
    var $add_team = $('#add-team-member');
    $add_team.on('click', function() {
      var $hidden_team_forms = $('.team-member-form:hidden');
      if ($hidden_team_forms.length == 1) {
        $(this).hide();
      }
      $('.team-member-form:hidden').first().show();
    });
    if ($('.team-member-form:hidden').length == 0) {
      $add_team.hide();
    }
  });
</script>

<script type="text/template" id="rowTemplate">
  <td><%- name %></td>
  <td><%- type %></td>
  <td class="middle alignright">
    <% if (user_id){ %>
      <a href="javascript:void(0)" class="edit glyphicon glyphicon-plus btn btn-primary btn-small"></a>
    <a href="javascript:void(0)" class="delete glyphicon glyphicon-remove-circle btn btn-danger btn-small"></a>
    <% } %>
  </td>
</script>

<script>
  $(function () {
    var rowCollection = new RowCollection();

    var tableView = new TableViewOld({ collection: rowCollection, el: $('#table-view') });
    rowCollection.reset(<?php echo $sf_data->getRaw('collection_json') ?>);

    $('#add-row').on('click', function() {
      $.get('<?php echo url_for('@user_profile\templateNew') ?>', function (response) {
        var object = new RowObject();
        object.set('id', $('#alternative_id', response).val());
        object.set('fetch_url', $('#fetch-url', response).data('url'));
        object.set('delete_url', '<?php echo url_for('@user_profile\templateDelete') ?>');
        tableView.addNew(object, response);
      });
    });
  });
</script>
