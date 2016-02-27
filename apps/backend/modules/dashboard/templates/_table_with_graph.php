<?php
/**
 * @var Dashboard $dashboard
 * @var sfWebResponse $sf_response
 * @var sfWebRequest $sf_request
 * @var Decision $decision
 * @var StackedBarChart $stackedBarChart
 * @var CumulativeGainChart $cumulativeChart
 *
 */

$dashboard = $dashboard->getRawValue();

$data_table_header = array(array('title' => 'Name'));
foreach ($dashboard->getCriteria() as $criterion){
  $orderDataType = null;

  if ($criterion['measurement'] == 'direct rating' || $criterion['measurement'] == 'direct float') {
    $orderDataType = "dom-text-numeric";
  }elseif ($criterion['measurement'] == 'five point scale' || $criterion['measurement'] == 'ten point scale') {
    $orderDataType = "dom-stars";
  }

  $data_table_header[] = array(
    'title' => '<a href="javascript: void(0);" data-edit-url="'. url_for('criterion/edit?id='.$criterion['id']) . '" data-delete-url="' . url_for('criterion/delete?id='.$criterion['id']) . '" class="criterion_edit">' . esc_js($criterion['name']) . '</a>',
    'orderDataType' => $orderDataType,
    'type' => $orderDataType ? "numeric" : null,
  );
}

$data_table_body = array();
foreach ($dashboard->getBodyData() as $alternative_id => $row) {
  $data_table_row = array();
  foreach ($row as $criterion_id => $cell) {
    if (is_object($cell)) {
      $data_table_row[] = get_partial($cell->measurement, array(
        'alternative_id' => $alternative_id,
        'criterion_id' => $criterion_id,
        'value' => $cell->value
      ));
    } else{
      $data_table_row[] = '<a href="javascript: void(0);" data-edit-url="'.url_for('alternative/edit?id='.$alternative_id).'" data-delete-url="'.url_for('alternative/delete?id='.$alternative_id).'" class="alternative_edit">' . esc_js($cell) . "</a>";
    }
  }
  $data_table_body[] = $data_table_row;
}
?>

<div id="content-area">
  <div class="panel panel-default">
    <div style="padding-bottom:10px" class="panel-heading">
      <table class="header-table" style="width:100%;">
        <tr class="folder">
          <td class="small">
            <div class="row">
              <div class="col-md-6 mr-top-10">
                <a class="folder-icon-wrapper" href="javascript:void(0)">
                  <span class="fa fa-minus-square-o folder-icon"></span>
                </a>&nbsp;
                <span class="folder-name">Showing <?php echo count($data_table_body); ?> Item<?php echo count($data_table_body) > 1 ? 's' : ''; ?></span>
              </div>
              <div class="col-md-6">
                <div class="table-view-search text-right">
                  <input type="text" > <button class="btn">Search</button>
                </div>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
    <div class="panel-body" style="padding: 0;">
      <div class="table-view-holder-wrapper">
        <div id="table-view-holder">
          <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="dashboard-table" style="padding-bottom: 15px; min-width: 100%;"></table>
        </div>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div style="padding-bottom:10px" class="panel-heading">
      <table class="header-table" style="width:100%;">
        <tr class="folder">
          <td class="small">
            <a class="folder-icon-wrapper" href="javascript:void(0)">
              <span class="fa fa-minus-square-o folder-icon"></span>
            </a>&nbsp;
            <span class="folder-name">Graph</span>
          </td>
        </tr>
      </table>
    </div>
    <div class="panel-body" style="padding: 0;">
      <?php $stackedBarChart->render(); ?>

      <?php $cumulativeChart->render(); ?>
    </div>
  </div>
</div>

<script>
  $(function () {
    var saveValue = function() {
      var params = {};
      params[this.name] = this.value;
      $.post('<?php echo url_for('@dashboard\save') ?>', params, function (response) {
        $('#chart').trigger('stackBarChart.update', [response.stackedBarChart]);
        $('#cumulative-chart').trigger('cumulativeChart.update', [response.cumulativeChart]);
      });
    };

    var applyActionsForAlternative = function($button){
      if ($button.data('delete-url')) {
        $('.edit-delete').on('click', function () {
          if (confirm('Are you sure?')) {
            $.ajax({
              url    : $button.data('delete-url'),
              type   : "POST",
              success: function (response) {
                window.location.reload();
              },
              error  : function (response) {

              }
            });
          }
        });
      }

      $('.save_alternative').on('click', function(){
        var
          links_post            = [],
          related_alternatives  = [],
          due_date              = new Date($('#alternative_due_date').val()),
          notify_date           = new Date($('#alternative_notify_date').val());

        $('#links').children().each(function(){
          links_post.push({ 'link': $('.link-input', $(this)).val(), 'id': $('.link-input', $(this)).data('link-id') });
        });

        $('#alternative_related_alternatives option:selected').each(function(){
          related_alternatives.push($(this).val());
        });

        $.ajax({
          url : $('#save_url').val(),
          type: "POST",
          data: {
            "alternative[name]"             : $('#alternative_name').val(),
            "alternative[status]"           : $('#alternative_status').val(),
            "alternative[additional_info]"  : CKEDITOR.instances['alternative_additional_info'].getData(),
            "alternative[notes]"            : CKEDITOR.instances['alternative_notes'].getData(),
            "alternative[assigned_to]"      : $('#alternative_assigned_to').find('option:selected').val(),
            "alternative[external_id]"      : $('#alternative_external_id').val(),
            "alternative[work_progress]"    : $('#alternative_work_progress').val(),
            "alternative[due_date]"         : $('#alternative_due_date').val() ? due_date.getFullYear() + '-' + (due_date.getMonth() + 1) + '-' + due_date.getDate() : '',
            "alternative[notify_date]"      : $('#alternative_notify_date').val() ? notify_date.getFullYear() + '-' + (notify_date.getMonth() + 1) + '-' + notify_date.getDate() : '',
            "tags"                          : JSON.stringify($(".tags_input").tagsinput('items')),
            "links"                         : JSON.stringify(links_post),
            "related_alternatives"          : JSON.stringify(related_alternatives)
          },
          success : function (response) {
            try{
              response = jQuery.parseJSON( response );
            }catch(e){}

            if (typeof response === 'object'){
              window.location.reload();
            }else{
              editor = CKEDITOR.instances['alternative_additional_info'];
              if (editor) { editor.destroy(true); }
              editor = CKEDITOR.instances['alternative_notes'];
              if (editor) { editor.destroy(true); }

              $('#editRowContent').html(response);

              applyActionsForAlternative($button);
            }
          },
          error: function(response){
            $('#editRowModal').modal('hide');
          }
        });
      });
    };

    var applyActionsForCriterion = function($button){
      $('.edit-delete').on('click', function(){
        if (confirm('Are you sure?')) {
          $.ajax({
            url : $button.data('delete-url'),
            type: "POST",
            success : function (response) {
              window.location.reload();
            },
            error: function(response){

            }
          });
        }
      });

      $('.save_criterion').on('click', function(){
        $.ajax({
          url : $('#save_url').val(),
          type: "POST",
          data: {
            "criterion[name]"           : $('#criterion_name').val(),
            "criterion[description]"    : CKEDITOR.instances['criterion_description'].getData(),
            "criterion[variable_type]"  : $('#criterion_variable_type').val(),
            "criterion[measurement]"    : $('#criterion_measurement').val()
          },
          success : function (response) {
            try{
              response = jQuery.parseJSON( response );
            }catch(e){}

            if (typeof response === 'object'){
              window.location.reload();
            }else{
              editor = CKEDITOR.instances['criterion_description'];
              if (editor) { editor.destroy(true); }

              $('#editRowContent').html(response);
            }
          }
        });
      });
    };

    var applyActionsForDecision = function($button){
      $('.edit-delete').on('click', function(){
        if (confirm('Are you sure?')) {
          $.ajax({
            url : $button.data('delete-url'),
            type: "POST",
            success : function (response) {
              window.location.href = '<?php echo url_for('@decision'); ?>';
            },
            error: function(response){

            }
          });
        }
      });

      $('.save_project').on('click', function(){
        var start_date  = new Date($('#decision_start_date').val()),
          end_date    = new Date($('#decision_end_date').val());

        $.ajax({
          url : $('#save_url').val(),
          type: "POST",
          dataType: 'json',
          data: {
            "decision[name]"        : $('#decision_name').val(),
            "decision[assigned_to]" : $('#decision_assigned_to').val(),
            "decision[objective]"   : CKEDITOR.instances['decision_objective'].getData(),
            "decision[start_date]"  : $('#decision_start_date').val() ? start_date.getFullYear() + '-' + (start_date.getMonth() + 1) + '-' + start_date.getDate() : '',
            "decision[end_date]"    : $('#decision_end_date').val() ? end_date.getFullYear() + '-' + (end_date.getMonth() + 1) + '-' + end_date.getDate() : '',
            "decision[color]"       : $('#decision_color > option:selected').val(),
            "decision[status]"      : $('#decision_status').val(),
            "tags"                  : JSON.stringify($(".tags_input").tagsinput('items'))
          },
          success : function (response) {
            if (_.has(response, 'status') && response.status === 'error'){
              // Check if ckeditor was created and destroy it
              var editor = CKEDITOR.instances['decision_objective'];
              if (editor) { editor.destroy(true); }

              $('#editRowContent').html(response.html);

              applyActionsForDecision();
            }else{
              window.location.reload();
            }
          }
        });
      });
    };

    var dataTableLoaded = false;

    var oTable = $('#dashboard-table').DataTable({
      "sScrollY"        : "296px",
      "sScrollX"        : "100%",
      "sScrollXInner"   : "<?php echo 250 + count($dashboard->getCriteria()) * 220 ?>px",
      "bScrollCollapse" : true,
      "bSort"           : true,
      "bFilter"         : false,
      "bInfo"           : false,
      "bPaginate"       : false,
      "searching"       : true,
      "oLanguage"       : {
        "sInfo"           : "",
        "sInfoEmpty"      : "",
        "sInfoFiltered"   : ""
      },
      columnDefs        : [
        { targets: 0, width: 250, orderable: true }
        <?php for ($i = 1; $i < count($data_table_header); $i++): ?>
        ,{ targets: <?php echo $i; ?>, orderable: true }
        <?php endfor; ?>
      ],
      "columns"         : <?php echo json_encode($data_table_header); ?>,
      "data"            :  <?php echo json_encode($data_table_body); ?>,
      "drawCallback"    : function( settings ) {
        if (!dataTableLoaded){
          dataTableLoaded = true;

          $(".numeric").numeric({ decimal: false });
          $(".numeric-float").numeric({ decimal: "," });

          $('.dashboard-star').rating({ callback: function() {
            saveValue.call(this);
          }});

          $('.autosave').on('change', function () {
            saveValue.call(this);
          });

          $('.alternative_edit').on('click', function(){
            var $this = $(this);

            $.get($this.data('edit-url'), function(response) {
              $('#editRowModal').modal('show');

              // Check if ckeditor was created and destroy it
              $.each([ 'alternative_additional_info', 'alternative_notes', 'criterion_description', 'decision_objective' ], function( index, value ) {
                var editor = CKEDITOR.instances[value];
                if (editor) { editor.destroy(true); }
              });

              $('#editRowContent').html(response);

              applyActionsForAlternative($this);

              $('.modal-tab').css('overflow-y', 'scroll').css('overflow-x', 'hidden');
              $('.modal-footer').css('position', 'absolute').css('left', 0).css('right', 0).css('bottom', 0);
            });
          });

          $('.criterion_edit').on('click', function(){
            var $this = $(this);

            $.get($this.data('edit-url'), function(response) {
              $('#editRowModal').modal('show');

              // Check if ckeditor was created and destroy it
              $.each([ 'alternative_additional_info', 'alternative_notes', 'criterion_description', 'decision_objective' ], function( index, value ) {
                var editor = CKEDITOR.instances[value];
                if (editor) { editor.destroy(true); }
              });

              $('#editRowContent').html(response);

              applyActionsForCriterion($this);
            });
          });

          $('.edit-decision').on('click', function(){
            var $this = $(this);

            $.get($this.data('edit-url'), function(response) {
              $('#editRowModal').modal('show');

              // Check if ckeditor was created and destroy it
              $.each([ 'alternative_additional_info', 'alternative_notes', 'criterion_description', 'decision_objective' ], function( index, value ) {
                var editor = CKEDITOR.instances[value];
                if (editor) { editor.destroy(true); }
              });

              $('#editRowContent').html(response);

              applyActionsForDecision($this);
            });
          });
        }
      }
    });

    $('a.folder-icon-wrapper').click(function(){
      var $self = $(this),
        $icon = $self.find('.folder-icon');
      if ($icon.hasClass('fa-minus-square-o')) {
        $icon.removeClass('fa-minus-square-o');
        $icon.addClass('fa-plus-square-o');
        $self.closest('.panel').find('.panel-body').slideUp();
      } else {
        $icon.removeClass('fa-plus-square-o');
        $icon.addClass('fa-minus-square-o');
        $self.closest('.panel').find('.panel-body').slideDown(undefined, undefined, function() {
          if (typeof stackBarChart == 'object') {
            stackBarChart.replot();
          }
          if (typeof cumulativeChart == 'object') {
            cumulativeChart.replot();
          }
        });
      }
    });

    $('.table-view-search > .btn').click(function(){
      oTable
        .search( $('.table-view-search > input').val() )
        .draw();
    });

    new $.fn.dataTable.FixedColumns( oTable, {
      "iLeftColumns": 1,
      "iLeftWidth"  : 250
    } );

    $.fn.dataTable.ext.order['dom-text-numeric'] = function  ( settings, col )
    {
      return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).val() * 1;
      } );
    };

    $.fn.dataTable.ext.order['dom-stars'] = function  ( settings, col )
    {
      return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input:checked', td).val() * 1;
      } );
    };

    $('#add-row').on('click', function(){
      var $this = $(this);

      $.get('<?php echo url_for('alternative\new') ?>', { title: 'New <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?>' }, function(response) {
        $('#editRowModal').modal('show');

        // Check if ckeditor was created and destroy it
        $.each([ 'alternative_additional_info', 'alternative_notes', 'criterion_description', 'decision_objective' ], function( index, value ) {
          var editor = CKEDITOR.instances[value];
          if (editor) { editor.destroy(true); }
        });

        $('#editRowContent').html(response);

        applyActionsForAlternative($this);

        $('.modal-tab').css('overflow-y', 'scroll').css('overflow-x', 'hidden');
        $('.modal-footer').css('position', 'absolute').css('left', 0).css('right', 0).css('bottom', 0);
      });
    });
  });
</script>
