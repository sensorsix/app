<?php
use_stylesheet('/libs/jquery-data-table/dt_bootstrap.css?v=1.10.4');
use_javascript('/libs/jquery-data-table/jquery.dataTables.min.js?v=1.10.4');
use_javascript('/libs/jquery-data-table/dataTables.fixedColumns.min.js?v=3.0.2');
use_javascript('/libs/jquery-data-table/dt_bootstrap.js?v=1.10.4');

/** @var ResponseTableView $table */
?>
<div class="table-view-holder-wrapper">
  <div class="table-view-search text-right">
    <input type="text" > <button class="btn">Search</button>
  </div>
  <div class="table-view-holder-wrapper">
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="response-table" style="padding-bottom: 15px; min-width: 100%;">
      <thead>
      <tr>
        <?php foreach ($table->getHeaderData() as $header) : ?>
          <th><?php echo $header ?></th>
        <?php endforeach ?>
      </tr>
      </thead>
      <tbody>
      <?php $index = 0 ?>
      <?php foreach ($table->getBodyData() as $response_id => $row) : ?>
        <tr>
          <?php $i = 0; ?>
          <?php foreach ($row as $value) : ?>
            <td<?php echo $i == 2 ? ' style="width:140px"' : (!$i ? " title=\"$value\"" : '') ?>>
              <?php if (!$i and $value != 'dashboard') : ?>
                <span><a title="<?php echo __('Delete') ?>" id="delete-response-<?php echo $response_id ?>" class="glyphicon glyphicon-remove" href="javascript:void(0)"></a></span>
              <?php endif ?>
              <?php echo $value ?>
            </td>
            <?php $i++ ?>
          <?php endforeach ?>
        </tr>
      <?php endforeach ?>
      </tbody>
      <tfoot>
      <tr>
        <?php foreach ($table->getFooterData() as $footer) : ?>
          <th><?php echo $footer ?></th>
        <?php endforeach ?>
      </tr>
      </tfoot>
    </table>
  </div>
</div>

<script type="text/javascript">
  $(function () {
    var oTable = $('#response-table').DataTable({
      "sScrollY": "296px",
      "sScrollX": "100%",
      "sScrollXInner": "<?php echo count($table->getHeaderData()) * 220 ?>px",
      "bScrollCollapse": true,
      "aaSorting": [[ 2, "desc" ]],
      "bPaginate": false,
      "oLanguage": {
        "sInfo": "",
        "sInfoEmpty": "",
        "sInfoFiltered": ""
      }
    });
    new $.fn.dataTable.FixedColumns( oTable, {
      "iLeftColumns": 1,
      "iLeftWidth": 250
    } );

    $('.dataTables_scrollFootInner > table').add('.DTFC_LeftFootWrapper > table').removeClass('table-bordered');

    $('.table-view-search > .btn').click(function(){
      oTable
        .search( $('.table-view-search > input').val() )
        .draw();
    });

    // Delete response
    $('a[id^=delete-response]').on('click', function () {
      if (confirm('<?php echo 'You are about to delete this ' . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) . '. Press Ok to continue.' ?>')) {
        oTable
            .rows(parseInt($(this).closest('tr').index(), 10))
            .remove()
            .draw()
        ;
        $.post('<?php echo url_for('@response\delete') ?>', { id: $(this).attr('id').replace('delete-response-', ''), 'sf_method':'delete' });
      }
    });
  });
</script>