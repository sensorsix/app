<?php
use_javascript('/libs/jquery-ui/jquery-ui.min.js?v=1.11.2');
use_stylesheet('/libs/jquery-ui/jquery-ui.min.css?v=1.11.2');
?>

<style type="text/css">
.draggable, .droppable { width: 427px; height: 130px; padding: 0.5em; float: left; margin: 10px; border: 1px solid black; border-radius: 5px; white-space: nowrap; }
.draggable { background: #818281; color: white; cursor: move; }
.draggable .bootstrap-tooltip { overflow: hidden; display: inline-block; text-overflow: ellipsis; max-width: 100%; padding-right: 15px; line-height: normal; font-size: 15px; }
.droppable { background: white; color: black; line-height: 110px; }
.desc-cell {
  cursor: default;
  background: white;
  color: black;
  border: 1px solid black;
  height: 80px;
  overflow: hidden;
  overflow-y: scroll;
  border-radius: 5px;
  font-size: 15px;
  text-align: left;
  padding: 3px 10px;
  margin-top: 5px;
  white-space: normal;
}
</style>

<?php $max_rating =  $collection->count() ?>
<div class="row control-group">
  <div class="col-xs-12">
    <?php foreach ($collection as $i => $item) : ?>
      <div class="row bg-arrow">
        <div class="col-xs-5">
          <div style="position: absolute;" id="measurement-<?php echo $item->id ?>" class="draggable ui-widget-content">
            <div class="text-left">
              <a href="javascript:void(0)" data-toggle="modal" data-target="#comments-<?php echo $item->id ?>"><img style="margin-top: -5px" src="<?php echo image_path('comment.png'); ?>" /></a>
              <span class="bootstrap-tooltip" data-toggle="tooltip" data-placement="top" title="<?php echo $item->name ?>"><?php echo Utility::teaser($item->name, 42) ?></span>
            </div>
            <div class="desc-cell"><?php echo $item->getRawValue()->getTooltip() ?></div>
          </div>
        </div>
        <div class="col-xs-2"></div>
        <div class="col-xs-5 text-center" style="position: relative">
          <div id="rating-<?php echo $max_rating - $i ?>" class="droppable ui-widget-header" style="float: right;"><?php echo __('Move here') ?></div>
          <div class="right" style="margin-top: 65px"><b><?php echo $i + 1 ?>.</b> </div>
        </div>
        <input id="measure-<?php echo $item->id ?>" type="hidden" name="measurement[<?php echo $item->id ?>]" value="<?php echo isset($values[$item->id]) ? $values[$item->id] : '' ?>"/>
      </div>
      <div class="modal fade" id="comments-<?php echo $item->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title" id="myModalLabel"><?php echo __('Comments') ?></h4>
            </div>
            <div class="modal-body">
              <?php
                include_partial('field_comment', array(
                  'id' => $item->id,
                  'comments' => isset($comments[$item->id]) ? $comments[$item->id] : array(),
                  'prioritization' => $prioritization,
                  'visible' => true
                  )
                )
              ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close') ?></button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach ?>
  </div>
</div>
<?php include_partial('comments_js', array('role_id' => $role_id)) ?>

<script type="text/javascript">
/*<![CDATA[*/
var was_drop = false;

$('.bootstrap-tooltip').tooltip();

$('.modal').on('show.bs.modal', function () {
  $(this).find('.comment-box').show();
});

$('input[id^=measure]').each(function () {
  var value = $(this).val();
  if (value) {
    var id = $(this).attr('id').replace('measure-', ''),
      rating_item = $('#rating-' + value),
      measurement = $('#measurement-' + id);
    rating_item.data('measurement', id);
    measurement.data('rating', value);
    rating_item.parent().append(measurement);
    measurement.css({ top: 0, right: 0 });
  }
});

$('.draggable').draggable({
  cancel: ".desc-cell",
  revert: "invalid",
  start: function (event, ui) {
    ui.helper.css('zIndex', 100);
    ui.helper.find('.bootstrap-tooltip').tooltip('destroy').unbind('click');
    was_drop = false;
  },
  stop: function (event, ui) {
    ui.helper.find('.bootstrap-tooltip').click(function (e) {
      // Prevents showing tooltip after drop
      if (was_drop) {
        was_drop = false;
      } else {
        e.stopPropagation(e);
        var tooltip = $('#tooltip-' + $(this).data('tooltip_id')).html();
        if (tooltip.length) {
          $(this).attr('data-original-title', tooltip);
          $('.tooltip').hide();
          $(this).tooltip('show');
        }
      }
    }).tooltip({ trigger: 'manual' });
  }
});

$('.droppable').droppable({
  drop: function( event, ui ) {
    var id = ui.draggable.attr('id').replace('measurement-', ''),
      rating = $(this).attr('id').replace('rating-', ''),
      rating_item = $('#rating-' + rating),
      prev_rating = ui.draggable.data('rating'),
      prev_id = rating_item.data('measurement');

    was_drop = true;
    // The 'revert' option default value
    ui.draggable.draggable("option", "revert", "invalid");

    // If the placeholder is already occupied, switch the items
    if (prev_rating) {
      var prev_rating_item = $('#rating-' + prev_rating);
      if (prev_id) {
        var prev_measurement = $('#measurement-' + prev_id);
        prev_rating_item.data('measurement', prev_id);
        $('#measure-' + prev_id).val(prev_rating);
        prev_measurement.data('rating', prev_rating);
        prev_rating_item.parent().append(prev_measurement);
        prev_measurement.css({ right: 0, top: 0, zIndex: 0, left: '' });
      } else {
        prev_rating_item.data('measurement', false);
      }
    }

    // The place is busy and there are free places
    if (!prev_rating && prev_id) {
      ui.draggable.draggable("option", "revert", true);
    } else {
      $('#measure-' + id).val(rating);
      rating_item.data('measurement', id);
      rating_item.parent().append(ui.draggable);
      ui.draggable.data('rating', rating);
      ui.draggable.css({ right: 0, top: 0, zIndex: 0, left: '' })
    }
  }
});
/*]]>*/
</script>