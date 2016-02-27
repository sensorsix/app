<?php
/**
 * @var $modal_header
 * @var $url_import
 */
?>

<!-- Trello Modal -->
<div id="ExcelModal" class="modal fade modal-wizard" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo $modal_header; ?></h4>
      </div>
      <div class="dynamic-content">
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-6 col-sm-offset-1">
              <h2 class="grey-header">Import from Excel file</h2>

              <h5 class="grey-header mr-top-25">Acceptable file types: XLST</h5>

              <div class="mr-top-50">
                <input type="file" class="form-control" name="excel_file" id="excel_file">
              </div>

              <div class="mr-top-50" id="excel-import-error">

              </div>
            </div>
            <div class="col-sm-1 col-sm-offset-4">

            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success pull-right ladda-button" id="submit-excel-import" data-style="zoom-in" style="margin-left: 15px;">Next</button>
          <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- END Trello Modal -->

<script>
  $(function(){
    var $excelModal         = $('#ExcelModal'),
        $submitExcelImport  = $('#submit-excel-import'),
        $excelImportError   = $('#excel-import-error'),
        ladda;

    $submitExcelImport.click(function(){
      ladda = $submitExcelImport.ladda();
      ladda.ladda('start');

      $excelImportError.text('');

      var formData = new FormData();
      formData.append('file', document.getElementById("excel_file").files[0]);

      $.ajax({
        url: "<?php echo $url_import; ?>",
        type: "POST",
        data: formData,
        enctype: 'multipart/form-data',
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        dataType: 'json'
      }).success(function( data ) {
        if (data.status === 'success') {
          $excelModal.find('.dynamic-content').html(data.html);
        }else{
          $excelImportError.text(data.message);
          ladda.ladda('stop');
        }
      }).error(function( data ) {
        ladda.ladda('stop');
      });
    });

    $excelModal.find('.modal-content').css({'margin-top': (($(window).height() - 650) / 2 < 0)? 0 : ($(window).height() - 650) / 2});
    $(window).resize(function(){
      $excelModal.find('.modal-content').css({'margin-top': (($(window).height() - 650) / 2 < 0)? 0 : ($(window).height() - 650) / 2});
    });
  })
</script>