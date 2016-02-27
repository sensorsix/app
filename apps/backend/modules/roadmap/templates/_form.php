<?php
/**
 * @var $grouped_decisions Decision[]
 * @var $un_grouped_decisions Decision[]
 * @var $roadmap_decisions array
 */
?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  <h4 class="modal-title" id="editRowModalLabel">Edit Roadmap</h4>
</div>
<div class="modal-body form-horizontal" id="editRowBody">
  <div class="row">
    <div class="col-md-2">
      <ul class="modal-left-menu">
        <li data-tab="#tab-overview"><a href="javascript:void(0)">Overview</a></li>
        <li data-tab="#tab-description"><a href="javascript:void(0)">Description</a></li>
        <li data-tab="#tab-projects"><a href="javascript:void(0)"><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE, true) ?></p></a></li>
      </ul>
    </div>
    <div class="col-md-10 modal-tabs">
      <div class="form-group" id="roadmap_modal_head_wrapper">
        <div class="col-sm-10">
          <?php echo $form->renderGlobalErrors() ?>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name']->render(array('class' => 'form-control')) ?>
          <?php echo $form->renderHiddenFields(false) ?>
        </div>

        <div class="col-sm-2">
          <?php echo $form['active']->renderError() ?>
          <?php echo $form['active']->render() ?>
        </div>

        <div class="col-sm-12 mr-top-15" id="disabled-hint" style="<?php echo !$form->getObject()->active ?: "display: none;"; ?>">
          <div class="bg-danger small" style="padding:1em; margin-bottom:1em;">
            Roadmap is currently inactive.
            <span class="pull-right">Toggle roadmap on above. <i class="fa fa-arrow-up"></i></span>
          </div>
        </div>
      </div>

      <div class="modal-tab" id="tab-overview">
        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label"><?php echo __('Status') ?></label>
            <?php echo $form['status']->renderError() ?>
            <?php echo $form['status']->render(array('class' => 'form-control')) ?>
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <label>
              <?php echo $form['show_items']->renderError() ?>
              <?php echo $form['show_items']->render(array('data-size' => 'small')) ?>
              Show <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?>
            </label>
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <label>
              <?php echo $form['show_releases']->renderError() ?>
              <?php echo $form['show_releases']->render(array('data-size' => 'small')) ?>
              Show <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE, true) ?>
            </label>
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <label>
              <?php echo $form['show_dependencies']->renderError() ?>
              <?php echo $form['show_dependencies']->render(array('data-size' => 'small')) ?>
              Show dependencies
            </label>
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <label>
              <?php echo $form['show_description']->renderError() ?>
              <?php echo $form['show_description']->render(array('data-size' => 'small')) ?>
              Show roadmap description
            </label>
          </div>
        </div>

        <div class="form-group">
          <div class="col-xs-12">
            <label>
              Show roadmap in The Workspace as:
              <div class="workspace_mode_list">
                <?php echo $form['workspace_mode']->renderError() ?>
                <?php echo $form['workspace_mode']->render() ?>
              </div>
            </label>
          </div>
        </div>
      </div>

      <div class="modal-tab" id="tab-description">
        <div class="form-group">
          <div class="col-sm-12">
            <label class="control-label modal-label"><?php echo __('Description') ?></label>
            <?php echo $form['description']->renderError() ?>
            <?php echo $form['description']->render(array('class' => 'form-control')) ?>
          </div>
        </div>
      </div>

      <div class="modal-tab" id="tab-projects">
        <?php if (count($un_grouped_decisions)): ?>
          <div class="row">
            <div class="col-md-12"><b><i><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE, true) ?></p> without <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::FOLDER_TYPE) ?>:</i></b></div>
          </div>
          <?php foreach ($un_grouped_decisions as $decision): ?>
            <div class="form-group">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="roadmap_decisions[]" value="<?php echo $decision->getId(); ?>" <?php if (in_array($decision->getId(), $roadmap_decisions->getRawValue())) echo "checked"; ?>>
                    <?php echo $decision->getName(); ?>
                  </label>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php foreach ($grouped_decisions as $group_name => $decisions): ?>
          <div class="row">
            <div class="col-md-12"><b><?php echo $group_name; ?>:</b></div>
          </div>
          <?php foreach ($decisions as $decision): ?>
            <div class="form-group">
              <div class="col-md-12">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="roadmap_decisions[]" value="<?php echo $decision->getId(); ?>" <?php if (in_array($decision->getId(), $roadmap_decisions->getRawValue())) echo "checked"; ?> >
                    <?php echo $decision->getName(); ?>
                  </label>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </div>

      <div class="form-group">
        <div class="inner-cell">
          <span id="fetch-url" data-url="<?php echo url_for('@roadmap\fetch?id=' . $form->getObject()->id) ?>"></span>
        </div>

        <input type="hidden" id="save_url" value="<?php echo url_for('@roadmap\update?id=' . $form->getObject()->id) ?>">
      </div>

    </div>
  </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
      <a href="javascript:void(0)" title="Delete" class="delete btn btn-danger btn-small edit-delete"><i lass="glyphicon glyphicon-remove-circle"></i> Delete the roadmap</a>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary save_roadmap" id="save">Save changes</button>
</div>

<script type="text/javascript">
  /*<![CDATA[*/
  $(function () {
    var $roadmap_active = $("#roadmap_active"),
        $modal_header   = $('.modal-header');

    $(".modal-left-menu").on("click", "a", function(){
      $(".modal-tab").hide();
      $($(this).closest("li").data('tab')).show();
      $(".modal-left-menu").find('a.active').removeClass('active');
      $(this).addClass('active');
    }).find("a:first").click();

    $('#editRowContent').css({height: '500px'});
    $('.modal-footer').css({bottom: 0, left: 0, position: 'absolute', right: 0});
    $('.modal-dialog').css({'margin-top': ($(window).height() - 500) / 2});

    $(window).resize(function(){
      $('.modal-dialog').css({'margin-top': ($(window).height() - 500) / 2});
    });

    // Handle $roadmap_active
    $roadmap_active.bootstrapSwitch();

    $roadmap_active.on('switchChange.bootstrapSwitch', function(event, state) {
      if (event.type == "switchChange"){
        if (state){
          $('#disabled-hint').hide();
          $modal_header.removeClass('modal-disabled').addClass('modal-enabled');
        }else{
          $modal_header.removeClass('modal-enabled').addClass('modal-disabled');
          $('#disabled-hint').show();
        }

        var tab_height = 345 - $('#roadmap_modal_head_wrapper > .col-sm-10').outerHeight();
        if ($('#disabled-hint').is(':visible')){ tab_height -= $('#disabled-hint').outerHeight(true); }
        $('.modal-tab').css({height: tab_height});
      }
    });

    $('#roadmap_show_items').add('#roadmap_show_releases').add('#roadmap_show_dependencies').add('#roadmap_show_description').bootstrapSwitch();

    var tab_height = 345 - $('#roadmap_modal_head_wrapper > .col-sm-10').outerHeight();
    if ($('#disabled-hint').is(':visible')){ tab_height -= $('#disabled-hint').outerHeight(true); }
    $('.modal-tab').css({height: tab_height});
  });
  /*]]>*/
</script>

<style>
  .workspace_mode_list > ul{
    display: inline;
  }
  .workspace_mode_list {
    display: inline;
    margin-left: 20px;
  }
</style>