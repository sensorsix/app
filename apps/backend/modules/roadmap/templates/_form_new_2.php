<?php
/**
 * @var Decision[] $decisions
 * @var Roadmap $roadmap
 */
?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="editRowModalLabel">Add new roadmap</h4>
</div>
<div class="modal-body form-horizontal" id="editRowBody">
  <div class="row">
    <div class="col-md-1">
      <a href="javascript:void(0)" class="steps-navigation step-prev wizard-steps-navigation" id="prev_1" ><i class="fa fa-arrow-circle-o-left"></i></a>
    </div>

    <div class="col-md-10">
      <div class="wizard" style="margin-top: 55px;">
        <div class="table-row">
          <div class="table-cell">
            <div id="wizard" class="form col-md-offset-3 col-md-6">
              <h3>Step 2/2</h3>

              <div>
                  <select id="roadmap_decisions" name="multiselect[]" multiple="multiple" class="multiselect dropdown-toggle btn btn-default">
                    <?php foreach($decisions as $decision): ?>
                      <option value="<?php echo $decision->getId();?>" selected="selected"><?php echo $decision->getName();?></option>
                    <?php endforeach; ?>
                  </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="inner-cell">
          <span id="fetch-url" data-url="<?php echo url_for('@roadmap\fetch?id=' . $roadmap->getId()) ?>"></span>
        </div>
        <input type="hidden" id="save_url" value="<?php echo url_for('@roadmap\create') ?>">

        <?php if ($roadmap->getId()): ?>
          <input type="hidden" id="roadmap_id" value="<?php echo $roadmap->getId() ?>">
        <?php endif; ?>
      </div>
    </div>

    <div class="col-md-1">
      <a href="javascript:void(0)" class="steps-navigation step-next wizard-steps-navigation" id="roadmap_next_2" ><i class="fa fa-arrow-circle-o-right"></i></a>
    </div>
  </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Skip this wizard</button>
</div>

<script type="text/javascript">
  /*<![CDATA[*/
  $(function () {

    $('#editRowContent').css({height: '500px'});
    $('.modal-footer').css({bottom: 0, left: 0, position: 'absolute', right: 0});
    $('.modal-dialog').css({'margin-top': ($(window).height() - 500) / 2});

    $(window).resize(function(){
      $('.modal-dialog').css({'margin-top': ($(window).height() - 500) / 2});
    });

    $('#roadmap_decisions').multiselect({
      includeSelectAllOption: true,
      enableFiltering: true,
      buttonText: function(options, select) {
        if (options.length === 0) {
          return 'Select existing <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE, true) ?> <b class="caret"></b>';
        }
        else if (options.length > 3) {
          return options.length + ' projects selected';
        }
        else {
          var labels = [];
          options.each(function() {
            if ($(this).attr('label') !== undefined) {
              labels.push($(this).attr('label'));
            }
            else {
              labels.push($(this).html());
            }
          });
          return labels.join(', ') + ' ';
        }
      }
    });

    $('#prev_1').on('click', function(){
      $.ajax({
        url : $('#save_url').val(),
        type: "POST",
        data: {
          "roadmap_id"     : $('#roadmap_id').val(),
          "to_step"        : 1
        },
        success : function (response) {
          $('#editRowContent').html(response);
        },
        error: function(response){

        }
      });
    });
  });
  /*]]>*/
</script>