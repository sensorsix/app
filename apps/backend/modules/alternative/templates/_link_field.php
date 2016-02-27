<?php /** @var AlternativeLink $link */ ?>
<div id="alternative-link-<?php echo $link_number ?>" class="form-group">
  <div class="col-xs-12">
    <a class="link-delete glyphicon glyphicon-remove pull-right" data-id="<?php echo $link_number ?>" title="<?php echo __('Delete') ?>" href="javascript:void(0)"></a>

    <div class="field-wrapper">
      <input type="text" value="<?php echo $link->link ?>" data-link-id="<?php echo $link->id ?>" name="alternative_link[<?php echo $link_number ?>]" class="form-control link-input"/>
    </div>
  </div>
</div>