<?php
/**
 * @var $data sfOutputEscaperArrayDecorator
 * @var $decision_id int
 */
?>
<div class="modal-body">
  <div class="row" id="excel-import-table">
    <div class="col-sm-12">
      <h2 class="grey-header">Import from Excel file</h2>

      <h5 class="grey-header mr-top-25">Now let's match the columns in your uploaded file SensorSix data.</h5>

      <h5 class="mr-top-15" id="excel-import-error" style="color: red; height: 15px;"></h5>

      <div class="mr-top-25" style="max-height: 355px; overflow: auto;">
        <table class="table table-striped table-bordered">

        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-success pull-right ladda-button" style="margin-left: 15px;" data-style="zoom-in" id="submit-excel-import-2">Next</button>
  <button type="button" class="btn btn-default pull-right" data-dismiss="modal" id="submit-excel-close-2">Cancel</button>
</div>

<script type="text/template" id="excel-import-header-template">
  <% _.each(alternative, function(value, key){ %>
    <th>
      <%- key %>

      <% if (key !== 'id') { %>
        <select class="form-control" data-key="<%- key %>">
          <option value="">Make a Selection</option>
          <option value="_skip">Skip</option>
          <optgroup label="Create a New Column">
            <option value="_new"><%- key %></option>
          </optgroup>
          <optgroup label="Available Data Fields">
            <% _.each(['name', 'status', 'work progress', 'tags', 'additional info', 'notes', 'due date', 'notify date'], function(option_value){ %>
              <option value="<%- option_value %>" <% if (option_value == value) { %> selected <% } %> ><%- _(option_value).capitalize() %></option>
            <% }, this); %>
          </optgroup>
        </select>
      <% } %>
    </th>
  <% }); %>
</script>

<script>
  $(function() {
    var
      alternativeCollection     = new AlternativeCollection(),
      alternativeCollectionView = new AlternativeCollectionView({
        collection:     alternativeCollection,
        el:             $('#excel-import-table').closest('.dynamic-content'),
        headerTemplate: $('#excel-import-header-template'),
        importError:    $('#excel-import-error'),
        urlFor:         '<?php echo url_for('dashboard\importFromCustomFields', array('decision_id' => $decision_id)); ?>'
      });

    alternativeCollection.reset(<?php echo json_encode($data->getRawValue()); ?>);
  });
</script>