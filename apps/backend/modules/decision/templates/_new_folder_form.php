<?php
/**
 * @var FolderForm $form
 */
?>
<td colspan="3">
  <div class="panel panel-default">
    <div style="padding-bottom:10px" class="panel-heading">
      <table class="header-table" style="width:100%;">
        <tr class="folder">
          <?php include_partial('folder_form', array('form' => $form)) ?>
        </tr>
      </table>
    </div>
    <div class="panel-body" style="padding: 0;">
      <div class="table-view-holder" data-id="<?php echo $form->getObject()->getId(); ?>"></div>
    </div>
  </div>
</td>