<?php
/**
 * @var sfWebResponse $sf_response
 * @var sfGuardSecurityUser $sf_user
 */

$sf_response->setTitle('Labels');
$sf_response->setSlot('disable_menu', true);
?>
<div class="row">

  <div class="col-md-3">
    <?php include_partial("tabs"); ?>
  </div>

  <div class="col-md-9">
    <h1 class="title"><?php echo __('Labels') ?></h1>
    <p>Please click the alias to change the label.</p>

    <table class="table table-striped table-hover">
      <thead>
        <th style="width: 20%">Concept</th>
        <th style="width: 40%">Alias Singular</th>
        <th style="width: 40%">Alias Plural</th>
      </thead>
      <tbody>
        <tr>
          <td>Item</td>
          <td><a href="#" data-pk="1_singular" class="x-editable"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?></a></td>
          <td><a href="#" data-pk="1_plural" class="x-editable"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?></a></td>
        </tr>
        <tr>
          <td>Project</td>
          <td><a href="#" data-pk="2_singular" class="x-editable"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?></a></td>
          <td><a href="#" data-pk="2_plural" class="x-editable"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE, true) ?></a></td>
        </tr>
        <tr>
          <td>Folder</td>
          <td><a href="#" data-pk="3_singular" class="x-editable"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::FOLDER_TYPE) ?></a></td>
          <td><a href="#" data-pk="3_plural" class="x-editable"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::FOLDER_TYPE, true) ?></a></td>
        </tr>
        <tr>
          <td>Release</td>
          <td><a href="#" data-pk="4_singular" class="x-editable"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE) ?></a></td>
          <td><a href="#" data-pk="4_plural" class="x-editable"><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE, true) ?></a></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
  $.fn.editable.defaults.mode = 'inline';

  $(document).ready(function() {
    $('.x-editable').editable({
      url: '<?php echo url_for('user_profile\edit_label') ?>',
      success: function(response, newValue) {

      },
      validate: function(value) {
        if($.trim(value) == '') {
          return 'This field is required';
        }else if(value.length > 16) {
          return 'Label cannot be longer than 16 characters long';
        }
      }
    });
  });
</script>