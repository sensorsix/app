<?php
/**
 * @var $expertPanel ExpertPanel
 * @var $type string
 * @var $form RoleForm
 */
?>
<div class="modal-header <?php echo $form->getObject()->active ? "modal-enabled" : "modal-disabled"; ?>">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
  </button>
  <h4 class="modal-title"
      id="editRowModalLabel"><?php echo (!isset($type) || $type !== 'new') ? "Edit" : "Create"; ?></h4>
</div>
<div class="modal-body form-horizontal" id="editRowBody">
<div class="row">
<div class="col-md-2">
  <ul class="modal-left-menu">
    <li data-tab="#tab-overview"><a href="javascript:void(0)">Overview</a></li>
    <?php if (!isset($type) || $type !== 'new'): ?>
      <li data-tab="#tab-share"><a href="javascript:void(0)">Share</a></li>
    <?php endif; ?>
    <li data-tab="#tab-details"><a href="javascript:void(0)">Details</a></li>
    <li data-tab="#tab-advanced"><a href="javascript:void(0)">Advanced</a></li>
  </ul>
</div>
<div class="col-md-10 modal-tabs">
<div class="form-group" id="role_modal_head_wrapper">
  <div class="col-sm-10">
    <?php echo $form->renderGlobalErrors() ?>
    <?php echo $form['name']->renderError() ?>
    <?php echo $form['name']->render() ?>
    <?php echo $form->renderHiddenFields(false) ?>
  </div>

  <div class="col-sm-2">
    <?php echo $form['active']->renderError() ?>
    <?php echo $form['active']->render() ?>
  </div>

  <div class="col-sm-12 mr-top-15" id="disabled-hint"
       style="<?php echo !$form->getObject()->active ? : "display: none;"; ?>">
    <div class="bg-danger small" style="padding:1em; margin-bottom:1em;">
      Workspace is currently inactive.
      <span class="pull-right">Toggle workspace on above. <i class="fa fa-arrow-up"></i></span>
    </div>
  </div>
</div>

<div class="modal-tab" id="tab-overview">


  <div class="form-group">
    <div class="col-sm-12">
      <?php if ($form->getObject()->Decision->type_id == 3) : // Vendor selection ?>
        <?php if (!isset($type) || $type !== 'new'): ?>
          <div class="accordion" id="expert-panel">
            <div class="accordion-group">
              <fieldset class="fieldset-form">
                <legend class="col-xs-2 col-xs-offset-2 accordion-heading">
                  <a class="accordion-toggle btn btn-default" data-toggle="collapse" data-parent="#expert-panel"
                     href="#collapse-expert-panel">Expert panel</a>
                </legend>
                <div id="collapse-expert-panel" class="accordion-body collapse">
                  <div class="accordion-inner">
                    <?php $expertPanel->render() ?>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
        <?php endif ?>
      <?php endif ?>

      <div class="row modal-field-row">
        <div class="col-md-5">
          <label>
            <input type="checkbox" id="checkbox_matrix" data-size="small">
            Rate <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?>
          </label>
        </div>
        <div class="col-md-7">
          <small>Lets visitors rate <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?> against criteria.</small>
        </div>
      </div>

      <div class="row role-matrix-wrapper">
        <div class="col-md-12">
          <?php if (isset($type) && $type == 'new'): ?>
            <?php include_component('role', 'matrix', array('role' => '', 'decision_id' => $form->getObject()->Decision->id)) ?>
          <?php else: ?>
            <?php include_component('role', 'matrix', array('role' => $form->getObject(), 'decision_id' => $form->getObject()->decision_id)) ?>
          <?php endif; ?>
        </div>
      </div>

      <hr>
      <div class="row modal-field-row">
        <div class="col-md-5">
          <label>
            <?php echo $form['prioritize']->renderError() ?>
            <?php echo $form['prioritize']->render(array('data-size' => 'small')) ?>
            Prioritize criteria
          </label>
        </div>
        <div class="col-md-7">
          <small>Lets visitors to prioritize criteria.</small>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">

          <div class="form-group" id="prioritization_method_group">
            <div class="col-xs-12">
              <div class="field-wrapper">
                <?php echo $form['prioritization_method']->renderError() ?>
                <?php echo $form['prioritization_method']->render() ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="row modal-field-row">
        <div class="col-md-5">
          <label>
            <input type="checkbox" id="checkbox_ideas" data-size="small">
            Gather ideas
          </label>
        </div>
        <div class="col-md-7">
          <small>Lets visitors submit <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?>.</small>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">


          <div class="form-group" id="collect_ideas_group">
            <div class="col-xs-12">
              <div class="checkbox">
                <?php echo $form['collect_items']->renderError() ?>
                <label>
                  <?php echo $form['collect_items']->render() ?>
                  <?php echo 'Collect suggestions for  ' . InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?>
                </label>
              </div>

              <div class="checkbox">
                <?php echo $form['display_items']->renderError() ?>
                <label>
                  <?php echo $form['display_items']->render() ?>
                  <?php echo $form['display_items']->renderLabelName() ?>
                </label>
              </div>

              <div class="checkbox">
                <?php echo $form['allow_voting']->renderError() ?>
                <label>
                  <?php echo $form['allow_voting']->render() ?>
                  <?php echo $form['allow_voting']->renderLabelName() ?>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <hr>

    </div>
  </div>
</div>

<div class="modal-tab" id="tab-share">
  <?php if (!isset($type) || $type !== 'new'): ?>
    <div class="form-group">
      <div class="col-sm-9">
        <label class="control-label"><?php echo __('Link') ?></label>
        <?php echo $form['link']->renderError() ?>
        <?php echo $form['link']->render(array('class' => 'form-control w-100 cursor-default', 'onclick' => '$(this).select();')) ?>
      </div>

      <div class="col-sm-3">
        <?php if (!isset($type) || $type !== 'new'): ?>
          <label class="control-label"><?php echo __('Social') ?></label>
          <div class="role-social-wrapper">
            <a
                onClick="window.open('http://www.facebook.com/sharer.php?s=100&amp;p[title]=SensorSix&amp;p[url]=<?php echo urlencode($form['link']->getValue()); ?>','sharer','toolbar=0,status=0,width=548,height=325');"
                href="javascript: void(0)">
              <img src="<?php echo image_path('fb.png'); ?>">
            </a>

            <a target="_blank"
               href="https://twitter.com/intent/tweet?url=<?php echo $form['link']->getValue() ?>&amp;text=Please help us by giving your input to this workspace&amp;via=sensorsixhq">
              <img src="<?php echo image_path('twitter.png'); ?>">
            </a>

            <a
                onClick="window.open('http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $form['link']->getValue() ?>&title=SensorSix','sharer','toolbar=0,status=0,width=548,height=450');"
                href="javascript: void(0)">
              <img src="<?php echo image_path('linkedin.png'); ?>">
            </a>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-sm-9">
        <label class="control-label modal-label"><?php echo __('Embed') ?></label>
        <?php echo $form['linkEmbed']->renderError() ?>
        <?php echo $form['linkEmbed']->render(array('class' => 'form-control w-100 cursor-default', 'onclick' => '$(this).select();')) ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<div class="modal-tab" id="tab-details">
  <h5>Workspace Introduction
    <small>(Displayed in the beginning of the workspace.)</small>
  </h5>

  <div class="form-group">
    <div class="col-sm-12">
      <?php echo $form['comment']->renderError() ?>
      <?php echo $form['comment']->render() ?>
    </div>
  </div>

  <?php if (!isset($type) || $type !== 'new'): ?>
    <div class="page-header" style="padding-bottom: 0;">
      <h5>Workspace files
        <small>(downloadable files in the beginning of the workspace)</small>
      </h5>
    </div>
    <div class="form-group">
      <div class="col-xs-12">
        <?php echo $form['upload']->render() ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<div class="modal-tab" id="tab-advanced">
  <div>

    <div class="row">
      <div class="col-md-12">
        <label class="control-label">Language</label>
        <?php echo $form['language']->renderError() ?>
        <?php echo $form['language']->render() ?>

      </div>

      <div class="col-md-12">

        <label class="control-label">Continue to this URL after workspace ends</label>
        <?php echo $form['continue_url']->renderError() ?>
        <?php echo $form['continue_url']->render() ?>
      </div>

    </div>
    <hr>

    <div class="row modal-field-row">
      <div class="col-md-5">
        <?php echo $form['anonymous']->renderError() ?>
        <label>
          <?php echo $form['anonymous']->render() ?>
          <?php echo $form['anonymous']->renderLabelName() ?>
        </label>

      </div>
      <div class="col-md-7">
        <p class="help-block small">All entries anonymous and visitor does not need to provide email.</p>
      </div>
    </div>
    <hr>

    <div style="display:none" class="row modal-field-row">
      <div class="col-md-5">
        <?php echo $form['updateable']->renderError() ?>
        <label>
          <?php echo $form['updateable']->render() ?>
          <?php echo $form['updateable']->renderLabelName() ?>
        </label>

      </div>
      <div class="col-md-7">
        <p class="help-block small">The visitor can return and modify response. If disabled, all answers are recorded as new entries.</p>
      </div>
    </div>
    <hr>

    <div class="row modal-field-row">
      <div class="col-md-5">
        <label>
          <?php echo $form['show_criteria_weights']->renderError() ?>
          <?php echo $form['show_criteria_weights']->render() ?>
          <?php echo $form['show_criteria_weights']->renderLabelName() ?>

        </label>
      </div>
      <div class="col-md-7">
        <p class="help-block small">Shows criteria weight graph for visitor in the end of survey.</p>
      </div>
    </div>
    <hr>

    <div class="row modal-field-row">
      <div class="col-md-5">
        <label>
          <?php echo $form['show_alternatives_score']->renderError() ?>
          <?php echo $form['show_alternatives_score']->render() ?>
          <?php echo $form['show_alternatives_score']->renderLabelName() ?>

        </label>
      </div>
      <div class="col-md-7">
        <p class="help-block small">Shows criteria score graph for visitor in the end of survey.</p>
      </div>
    </div>
    <hr>

    <div class="row modal-field-row">
      <div class="col-md-5">
        <label>
          <?php echo $form['show_comments']->renderError() ?>
          <?php echo $form['show_comments']->render() ?>
          <?php echo $form['show_comments']->renderLabelName() ?>

        </label>
      </div>
      <div class="col-md-7">
        <p class="help-block small">Enables commenting on items.</p>
      </div>
    </div>







  </div>


</div>
</div>
</div>
</div>

<div class="modal-footer">
  <?php if (!isset($type) || $type !== 'new'): ?>
    <div class="pull-left">
      <a href="javascript:void(0)" title="Delete" class="delete btn btn-danger btn-small edit-delete"><i
            lass="glyphicon glyphicon-remove-circle"></i> Delete the <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?></a>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary save_role" id="save">Save changes</button>
  <?php else: ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary save_role" id="save">Create</button>
  <?php endif; ?>
</div>

<?php if (!isset($type) || $type !== 'new'): ?>
  <input type="hidden" id="save_url" value="<?php echo url_for('@role\update?id=' . $form->getObject()->id) ?>">
<?php else: ?>
  <input type="hidden" id="save_url"
         value="<?php echo url_for('@role\create?decision_id=' . $form->getObject()->Decision->id) ?>">
<?php endif; ?>

<script type="text/javascript">
$(function () {
  // Find and load facebook share button
  if (typeof FB == 'object') {
    FB.XFBML.parse();
  }

  // Find and load twitter share button
  if (typeof twttr == 'object') {
    twttr.widgets.load();
  }

  // Popovers initialization.
  $('.help-info').tooltip();

  $('#measure-button').click(function () {
    window.open($('#role_link').val());
  });

  var default_text = '<?php echo __('New role') ?>';

  // Rename node in the tree on the name edit
  $('#role_name').on('change',function () {
    if (!$(this).val()) {
      $(this).val(default_text);
    }
  }).on('focus', function () {
    if ($(this).val() == default_text) {
      $(this).val('');
    }
  });

  // Checks all criteria
  $('input[id^=all-criterion]').each(function () {
    var id = $(this).attr('id').replace('all-criterion-', '');

    if ($('.criterion-' + id + ':not(:checked)').length) {
      $(this).prop('checked', false);
    } else {
      $(this).prop('checked', true);
    }

    $(this).click(function () {
      // Select or deselect row
      if (this.checked) {
        $('.criterion-' + id + ':not(:checked)').each(function () {
          $(this).prop('checked', true);
        });
      } else {
        $('.criterion-' + id + ':checked').each(function () {
          $(this).prop('checked', false);
        });
      }
    });
  });

  // Checks all alternatives
  $('input[id^=all-alternative]').each(function () {
    var id = $(this).attr('id').replace('all-alternative-', '');
    if ($('.alternative-' + id + ':not(:checked)').length) {
      $(this).prop('checked', false);
    } else {
      $(this).prop('checked', true);
    }

    $(this).on('click', function () {
      // Select or deselect column
      if (this.checked) {
        $('.alternative-' + id + ':not(:checked)').each(function () {
          $(this).prop('checked', true);
        });
      } else {
        $('.alternative-' + id + ':checked').each(function () {
          $(this).prop('checked', false);
        });
      }
    });
  });

  // The planned alternative measurement table data edit
  $('input.measure').on('change', function () {
    var data = {
      mode        : $(this).is(':checked') ? 'save' : 'delete',
      measurements: [
        { criterion_id: $(this).data('criterion'), alternative_id: $(this).data('alternative') }
      ]
    };

    // All alternatives for given criterion are selected
    if ($('.criterion-' + $(this).data('criterion') + ':not(:checked)').length) {
      $('#all-criterion-' + $(this).data('criterion')).removeAttr('checked');
    } else {
      $('#all-criterion-' + $(this).data('criterion')).attr('checked', 'checked');
    }

    // All criteria for give alternative are selected
    if ($('.alternative-' + $(this).data('alternative') + ':not(:checked)').length) {
      $('#all-alternative-' + $(this).data('alternative')).removeAttr('checked');
    } else {
      $('#all-alternative-' + $(this).data('alternative')).attr('checked', 'checked');
    }
  });

  <?php if (!isset($type) || $type !== 'new'): ?>
  var delete_file = [],
      $fileupload = $('#fileupload');

  $fileupload.fileupload({
    url        : '<?php echo url_for('@role\upload?id=' . $form->getObject()->id) ?>',
    maxFileSize: 20000000,
    autoUpload : false
  });

  // Load existing files:
  $fileupload.each(function () {
    var that = this;
    $.getJSON(
        $(this).fileupload('option', 'url'),
        function (result) {
          if (result && result.length) {
            $(that).fileupload('option', 'done').call(that, null, {result: result});
          }
        }
    );
  });

  $fileupload.bind('fileuploaddestroy', function (e, data) {
    delete_file.push(data.url);
    data.url = '';
  });

  $('.save_role').click(function () {
    $('.start').each(function () {
      $(this).find('button').click();
    });

    delete_file.forEach(function (url, i) {
      $.ajax({
        'url' : url,
        'type': 'delete'
      });
    });
  });
  <?php endif; ?>

  $(".collapse").collapse({ toggle: false });

  // Prioritize criteria
  var $prioritization_method = $("#prioritization_method_group"),
      $role_prioritize = $('#role_prioritize'),
      $modal_header = $('.modal-header'),
      $collect_ideas_group = $("#collect_ideas_group"),
      $checkbox_ideas = $("#checkbox_ideas"),
      $role_active = $("#role_active");


  // Preapre checkbox_ideas
  //   validate starting state (make checked if at least one option is selected and hide options if none is selected)
  if ($collect_ideas_group.find("input[type=checkbox]:checked").length) {
    $checkbox_ideas.attr('checked', true)
  } else {
    $collect_ideas_group.hide();
  }

  $checkbox_ideas.bootstrapSwitch();

  $checkbox_ideas.on('switchChange.bootstrapSwitch', function (event, state) {
    if (event.type == "switchChange") {
      $collect_ideas_group.find("input[type=checkbox]").prop('checked', state);
      if (state) {
        $collect_ideas_group.slideDown();
      } else {
        $collect_ideas_group.slideUp();
      }
    }
  });

  //    handle shanging of collect_ideas_group
  $collect_ideas_group.find("input[type=checkbox]").on('change', function () {
    if (!$collect_ideas_group.find("input[type=checkbox]:checked").length) {
      $checkbox_ideas.bootstrapSwitch('toggleState');
    }
  });

  // Handle $role_prioritize
  if (!$role_prioritize.is(':checked')) {
    $prioritization_method.hide();
  }

  $role_prioritize.on('switchChange.bootstrapSwitch', function (event, state) {
    if (event.type == "switchChange") {
      $prioritization_method.slideToggle();
    }
  });

  // Handle $role_active
  $role_active.bootstrapSwitch();

  $role_active.on('switchChange.bootstrapSwitch', function (event, state) {
    if (event.type == "switchChange") {
      if (state) {
        $('#disabled-hint').hide();
        $modal_header.removeClass('modal-disabled').addClass('modal-enabled');
      } else {
        $modal_header.removeClass('modal-enabled').addClass('modal-disabled');
        $('#disabled-hint').show();
      }

      var tab_height = $(window).height() - 250;
      if ($('#disabled-hint').is(':visible')) {
        tab_height -= 66;
      }
      $('.modal-tab').css({height: tab_height});
    }
  });

  // Fieldset toggle border
  $('.fieldset-form').children('.accordion-heading').click(function () {
    $(this).parent().toggleClass('active');
  });

  $(".modal-left-menu").on("click", "a",function () {
    $(".modal-tab").hide();
    $($(this).closest("li").data('tab')).show();
    $(".modal-left-menu").find('a.active').removeClass('active');
    $(this).addClass('active');

    if ($(this).closest("li").data('tab') == '#tab-advanced') {
      var checkboxes = $("#tab-advanced").find('input[type=checkbox]');
      if (checkboxes.first().data('bootstrap-switch') == null) {
        checkboxes.bootstrapSwitch({size: 'small'});
      }
    } else {
      if ($(this).closest("li").data('tab') == '#tab-overview') {
        if (typeof oTable === 'object') {
          oTable.fnAdjustColumnSizing();
        }
      }
    }
  }).find("a:first").click();

  // Handle matrix switcher
  var measurement_wrapper = $("#planned-measurement_wrapper"),
      checkbox_matrix = $('#checkbox_matrix');

  if (measurement_wrapper.find("input[type=checkbox]:checked").length) {
    checkbox_matrix.bootstrapSwitch('toggleState');
  }else{
    measurement_wrapper.slideUp();
  }

  checkbox_matrix.on('switchChange.bootstrapSwitch', function (event, state) {
    if (event.type == "switchChange") {
      measurement_wrapper.find("input[type=checkbox]").prop('checked', state);
      if (state) {
        measurement_wrapper.slideDown();
      } else {
        measurement_wrapper.slideUp();
      }
    }
  });

  //Handle errors in tabs
  var tab_overview_errors = $('#tab-overview').find('.alert-danger').length,
      tab_share_errors = $('#tab-share').find('.alert-danger').length,
      tab_details_errors = $('#tab-details').find('.alert-danger').length,
      tab_advanced_errors = $('#tab-advanced').find('.alert-danger').length;

  if (!tab_overview_errors) {
    if (tab_share_errors) {
      $('li[data-tab="#tab-share"]').find('a').click();
    } else if (tab_details_errors) {
      $('li[data-tab="#tab-details"]').find('a').click();
    } else if (tab_advanced_errors) {
      $('li[data-tab="#tab-advanced"]').find('a').click();
    }
  }

  $("#role_prioritize").add("#checkbox_matrix").bootstrapSwitch();

  var tab_height = $(window).height() - 300;
  if ($('#disabled-hint').is(':visible')) {
    tab_height -= 30;
  }
  $('.modal-tab').css({height: tab_height});
});
</script>
