<?php
/**
 * @var RoadmapForm $form
 */
?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="editRowModalLabel">Add new roadmap</h4>
</div>
<div class="modal-body form-horizontal" id="editRowBody">
  <div class="row">
    <div class="col-md-1"></div>

    <div class="col-md-10">

      <div class="wizard" style="margin-top: 55px;">
        <div class="table-row">
          <div class="table-cell">
            <div id="wizard" class="form col-md-offset-3 col-md-6">
              <h3>Step 1/2</h3>
              <label class="roadmap-name" for="roadmap-name"><b>Type the name</b></label>
              <div>
                <?php echo $form['name']->renderError() ?>
                <?php echo $form['name']->render(array('class' => 'form-control input-lg', 'placeholder' =>"New roadmap" )) ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="inner-cell">
          <span id="fetch-url" data-url="<?php echo url_for('@roadmap\fetch?id=' . $form->getObject()->id) ?>"></span>
        </div>
        <input type="hidden" id="save_url" value="<?php echo url_for('@roadmap\create') ?>">

        <?php if ($form->getObject()->getId()): ?>
          <input type="hidden" id="roadmap_id" value="<?php echo $form->getObject()->getId() ?>">
        <?php endif; ?>
      </div>

    </div>

    <div class="col-md-1">
      <a href="javascript:void(0)" class="steps-navigation step-next wizard-steps-navigation" id="roadmap_next_1" ><i class="fa fa-arrow-circle-o-right"></i></a>
    </div>
  </div>
</div>
<div class="modal-footer">
  &nbsp;
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
  });
  /*]]>*/
</script>