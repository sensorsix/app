<?php
/**
 * @var $roadmap Roadmap
 * @var $alternative_relations array
 * @var $related_decisions array
 */

$form = new RoadmapForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

$form = new DecisionForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

decorate_with('steps_layout');

$alternative_relations = $sf_data->getRaw('alternative_relations');
$related_decisions = $sf_data->getRaw('related_decisions');
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array('roadmap_id' => $roadmap->getId()));?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
  <?php echo $roadmap->getName(); ?> <span style="font-size: 10pt;">- <a id="edit-roadmap" title="Edit <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?>" href="javascript:void(0)">Edit</a></span>
<?php end_slot(); ?>

<?php slot('project_name'); ?>
  <a data-toggle="tooltip" title="Download presentation" data-placement="bottom" href="<?php echo url_for('roadmap\export', array('id' => $roadmap->getId())) ?>" class="btn btn-default"> Download presentation</a>
<?php end_slot(); ?>

<div id="dashboard_roadmap_description" class="row">
  <div class="col-md-11 lead">
    <?php if ($roadmap->getShowDescription()): ?>
      <?php echo sfOutputEscaperGetterDecorator::unescape($roadmap->getDescription()); ?>
    <?php endif; ?>
  </div>
  <div class="col-md-1">
    <span class="roadmap-blue-label"><?php echo $roadmap->getStatus(); ?></span>
  </div>
</div>
<hr style="margin-bottom: 0;">

<?php foreach ($roadmap->getOrderedRoadmapDecision() as $roadmapDecision): ?>
  <div class="roadmap-decision-wrapper" style="border-left: 20px solid <?php echo $roadmapDecision->getDecision()->getColor() ?: 'transparent'; ?>; padding-left: 10px;">
    <div class="row">
      <div class="col-md-7"><h2 class="roadmap-decision-header"><?php echo $roadmapDecision->getDecision()->getName(); ?></h2> - <a href="javascript: void(0);" class="edit-decision" data-edit-url = "<?php echo url_for('decision\edit', array('id' => $roadmapDecision->getDecision()->getId())) ?>" data-delete-url = "<?php echo url_for('decision\delete', array('id' => $roadmapDecision->getDecision()->getId())) ?>">Edit</a></div>
      <div class="col-md-4">
        <?php
        if ($roadmapDecision->getDecision()->getStartDate()){
          echo '<h4 class="text-primary roadmap-decision-date">' . DateTime::createFromFormat('Y-m-d H:i:s', $roadmapDecision->getDecision()->getStartDate())->format('j M Y') . '</h4>';
        }else{
          echo "<i><small>not set</small></i>";
        }
        ?>
        -
        <?php
        if ($roadmapDecision->getDecision()->getEndDate()){
          echo '<h4 class="text-primary roadmap-decision-date">' . DateTime::createFromFormat('Y-m-d H:i:s', $roadmapDecision->getDecision()->getEndDate())->format('j M Y'). '</h4>';
        }else{
          echo "<i><small>not set</small></i>";
        }
        ?>
      </div>
      <div class="col-md-1">
        <span class="roadmap-blue-label" style="background-color: <?php echo $roadmapDecision->getDecision()->getStatusColor(); ?>;"><?php echo $roadmapDecision->getDecision()->getStatus(); ?></span>
      </div>
    </div>

    <div class="row" style="margin-top: 15px;">
      <div class="col-md-8"><?php echo sfOutputEscaperGetterDecorator::unescape($roadmapDecision->getDecision()->getObjective()); ?></div>
      <div class="col-md-4 pull-right">
        <?php foreach ($roadmapDecision->getDecision()->getTagDecision() as $tagDecision): ?>
          <span class="tag label label-info"><?php echo $tagDecision->getTag()->getName(); ?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if ($roadmap->getShowReleases() && count($roadmapDecision->getDecision()->getProjectRelease())): ?>
      <div class="row" style="margin-top: 15px;">
        <div class="col-md-12"><h3 class="roadmap-releases-header"><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE, true) ?></p></h3></div>
      </div>

      <div class="roadmap-releases-list">
        <?php foreach ($roadmapDecision->getDecision()->getProjectRelease() as $project_release): ?>
          <div class="row">
            <div class="col-md-12"><h4 class="roadmap-releases-header"><?php echo $project_release->getName(); ?></h4> - <a href="<?php echo url_for('@planner?decision_id=' . $roadmapDecision->getDecision()->getId()) ?>">Edit</a></div>

            <?php if ($roadmap->getShowItems()): ?>
              <?php foreach ($project_release->getProjectReleaseAlternative() as $projectReleaseAlternative): ?>
                  <div class="col-md-12">
                    <?php echo $projectReleaseAlternative->getAlternative()->getName(); ?>
                    <?php if (count($projectReleaseAlternative->getAlternative()->getAlternativeRelation())): ?>
                      <i class="fa fa-link"></i>
                    <?php endif; ?>
                  </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($roadmap->getShowDependencies()): ?>
      <?php if (array_key_exists($roadmapDecision->getDecision()->getId(), $alternative_relations)): ?>
        <div class="row" style="margin-top: 15px;">
          <div class="col-md-12">
            <b>This <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?> has dependency to <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?>(s) <?php echo implode(', ', $related_decisions[$roadmapDecision->getDecision()->getId()]); ?></b>
            <ul>
              <?php foreach ($alternative_relations[$roadmapDecision->getDecision()->getId()] as $alternative_relation): ?>
                <?php foreach ($alternative_relation['relations'] as $relation): ?>
                  <li>
                    - <a href="javascript:void(0)" data-edit-url="<?php echo url_for('alternative/edit?id=' . $alternative_relation['alternative']->getId()); ?>" data-delete-url="<?php echo url_for('alternative/delete?id=' . $alternative_relation['alternative']->getId()); ?>" class="alternative_edit" data-toggle="popover" data-content="<?php include_partial("roadmap/alternativePopupInfo", array('alternative' => $alternative_relation['alternative']));?>"><?php echo $alternative_relation['alternative']->getName(); ?></a>
                    has dependency to

                    <?php foreach ($relation['linked_alternatives'] as $linked_alternative): ?>
                      <a href="javascript:void(0)" data-edit-url="<?php echo url_for('alternative/edit?id=' . $linked_alternative->getId()); ?>" data-delete-url="<?php echo url_for('alternative/delete?id=' . $linked_alternative->getId()); ?>" class="alternative_edit" data-toggle="popover" data-content="<?php include_partial("roadmap/alternativePopupInfo", array('alternative' => $linked_alternative));?>"><?php echo $linked_alternative->getName(); ?></a>
                    <?php endforeach; ?>

                    in project <?php echo $relation['decision']->getName(); ?>
                  </li>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <hr style="margin-bottom: 0; margin-top: 0;">
<?php endforeach; ?>

<!-- Modal -->
<div class="modal" id="editRowModal" tabindex="-1" role="dialog" aria-labelledby="editRowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="editRowContent">

    </div>
  </div>
</div>

<style>
  .modal-footer {
    position: absolute;
    left:     0;
    right:    0;
    bottom:   0;
  }

  .modal-tab{
    overflow-y: scroll;
    overflow-x: hidden;
  }
</style>

<script>
  $(function () {
    var applyActionsForAlternative = function($button) {
      $('.edit-delete').on('click', function () {
        if (confirm('Are you sure?')) {
          $.ajax({
            url    : $button.data('delete-url'),
            type   : "POST",
            success: function (response) {
              window.location.reload();
            },
            error  : function (response) {

            }
          });
        }
      });

      $('.save_alternative').on('click', function () {
        var
          links_post = [],
          related_alternatives = [],
          due_date = new Date($('#alternative_due_date').val()),
          notify_date = new Date($('#alternative_notify_date').val());

        $('#links').children().each(function () {
          links_post.push({'link': $('.link-input', $(this)).val(), 'id': $('.link-input', $(this)).data('link-id')});
        });

        $('#alternative_related_alternatives option:selected').each(function () {
          related_alternatives.push($(this).val());
        });

        $.ajax({
          url    : $('#save_url').val(),
          type   : "POST",
          data   : {
            "alternative[name]"           : $('#alternative_name').val(),
            "alternative[status]"         : $('#alternative_status').val(),
            "alternative[additional_info]": CKEDITOR.instances['alternative_additional_info'].getData(),
            "alternative[notes]"          : CKEDITOR.instances['alternative_notes'].getData(),
            "alternative[assigned_to]"    : $('#alternative_assigned_to').find('option:selected').val(),
            "alternative[external_id]"    : $('#alternative_external_id').val(),
            "alternative[work_progress]"  : $('#alternative_work_progress').val(),
            "alternative[due_date]"       : $('#alternative_due_date').val() ? due_date.getFullYear() + '-' + (due_date.getMonth() + 1) + '-' + due_date.getDate() : '',
            "alternative[notify_date]"    : $('#alternative_notify_date').val() ? notify_date.getFullYear() + '-' + (notify_date.getMonth() + 1) + '-' + notify_date.getDate() : '',
            "tags"                        : JSON.stringify($(".tags_input").tagsinput('items')),
            "links"                       : JSON.stringify(links_post),
            "related_alternatives"        : JSON.stringify(related_alternatives)
          },
          success: function (response) {
            try {
              response = jQuery.parseJSON(response);
            } catch (e) {
            }

            if (typeof response === 'object') {
              window.location.reload();
            } else {
              editor = CKEDITOR.instances['alternative_additional_info'];
              if (editor) {
                editor.destroy(true);
              }
              editor = CKEDITOR.instances['alternative_notes'];
              if (editor) {
                editor.destroy(true);
              }

              $('#editRowContent').html(response);

              applyActionsForAlternative($button);
            }
          },
          error  : function (response) {
            $('#editRowModal').modal('hide');
          }
        });
      });
    };

    var applyActionsForRoadmap = function(){
      $('.edit-delete').on('click', function(){
        if (confirm('Are you sure?')) {
          $.ajax({
            url : '<?php echo url_for('roadmap\delete'); ?>',
            type: "POST",
            data: {
              "id" : '<?php echo $roadmap->getId(); ?>'
            },
            success : function (response) {
              window.location.href = '<?php echo url_for('roadmap') ?>';
            },
            error: function(response){

            }
          });
        }
      });

      $('.save_roadmap').on('click', function(){
        var decisions_id  = [];

        $("input[name='roadmap_decisions[]']:checked").each( function () {
          decisions_id.push($(this).val());
        });

        $.ajax({
          url : $('#save_url').val(),
          type: "POST",
          data: {
            "roadmap[name]"               : $('#roadmap_name').val(),
            "roadmap[description]"        : CKEDITOR.instances['roadmap_description'].getData(),
            "roadmap[active]"             : $('#roadmap_active').is(':checked')?'checked':'',
            "roadmap[show_items]"         : $('#roadmap_show_items').is(':checked')?'checked':'',
            "roadmap[show_releases]"      : $('#roadmap_show_releases').is(':checked')?'checked':'',
            "roadmap[show_dependencies]"  : $('#roadmap_show_dependencies').is(':checked')?'checked':'',
            "roadmap[show_description]"   : $('#roadmap_show_description').is(':checked')?'checked':'',
            "roadmap[status]"             : $('#roadmap_status').val(),
            "roadmap[workspace_mode]"     : $('input[name="roadmap[workspace_mode]"]:checked').val(),
            "decisions_id"                : JSON.stringify(decisions_id)
          },
          success : function (response) {
            try{
              response = jQuery.parseJSON( response );
            }catch(e){}

            if (typeof response === 'object'){
              if (!response.error){
                window.location.reload();
              }
            }else{
              editor = CKEDITOR.instances['roadmap_description'];
              if (editor) { editor.destroy(true); }

              $('#editRowContent').html(response);

              applyActionsForRoadmap();
            }
          }
        });
      });
    };

    var applyActionsForDecision = function($button){
      $('.edit-delete').on('click', function(){
        if (confirm('Are you sure?')) {
          $.ajax({
            url : $button.data('delete-url'),
            type: "POST",
            success : function (response) {
              window.location.reload();
            },
            error: function(response){

            }
          });
        }
      });

      $('.save_project').on('click', function(){
        var start_date  = new Date($('#decision_start_date').val()),
            end_date    = new Date($('#decision_end_date').val());

        $.ajax({
          url : $('#save_url').val(),
          type: "POST",
          dataType: 'json',
          data: {
            "decision[name]"        : $('#decision_name').val(),
            "decision[assigned_to]" : $('#decision_assigned_to').val(),
            "decision[objective]"   : CKEDITOR.instances['decision_objective'].getData(),
            "decision[start_date]"  : $('#decision_start_date').val() ? start_date.getFullYear() + '-' + (start_date.getMonth() + 1) + '-' + start_date.getDate() : '',
            "decision[end_date]"    : $('#decision_end_date').val() ? end_date.getFullYear() + '-' + (end_date.getMonth() + 1) + '-' + end_date.getDate() : '',
            "decision[color]"       : $('#decision_color > option:selected').val(),
            "decision[status]"      : $('#decision_status').val(),
            "tags"                  : JSON.stringify($(".tags_input").tagsinput('items'))
          },
          success : function (response) {
            if (_.has(response, 'status') && response.status === 'error'){
              // Check if ckeditor was created and destroy it
              var editor = CKEDITOR.instances['decision_objective'];
              if (editor) { editor.destroy(true); }

              $('#editRowContent').html(response.html);

              applyActionsForDecision();
            }else{
              window.location.reload();
            }
          }
        });
      });
    };

    $('#edit-roadmap').on('click', function(){
      $.get('<?php echo url_for('roadmap\edit', array('id' => $roadmap->getId())) ?>', function(response) {
        $('#editRowModal').modal('show');

        // Check if ckeditor was created and destroy it
        $.each([ 'decision_objective', 'roadmap_description' ], function( index, value ) {
          var editor = CKEDITOR.instances[value];
          if (editor) { editor.destroy(true); }
        });

        $('#editRowContent').html(response);

        applyActionsForRoadmap();
      });
    });

    $('.edit-decision').on('click', function(){
      var $this = $(this);

      $.get($this.data('edit-url'), function(response) {
        $('#editRowModal').modal('show');

        // Check if ckeditor was created and destroy it
        $.each([ 'decision_objective', 'roadmap_description' ], function( index, value ) {
          var editor = CKEDITOR.instances[value];
          if (editor) { editor.destroy(true); }
        });

        $('#editRowContent').html(response);

        applyActionsForDecision($this);
      });
    });

    $('.alternative_edit').on('click', function(){
      var $this = $(this);

      $.get($this.data('edit-url'), function(response) {
        $('#editRowModal').modal('show');

        // Check if ckeditor was created and destroy it
        $.each([ 'alternative_additional_info', 'alternative_notes', 'decision_objective', 'roadmap_description' ], function( index, value ) {
          var editor = CKEDITOR.instances[value];
          if (editor) { editor.destroy(true); }
        });

        $('#editRowContent').html(response);

        applyActionsForAlternative($this);

        $('.modal-tab').css('overflow-y', 'scroll').css('overflow-x', 'hidden');
        $('.modal-footer').css('position', 'absolute').css('left', 0).css('right', 0).css('bottom', 0);
      });
    });

    $('[data-toggle="popover"]').popover({ trigger: "hover", html: true });
  });
</script>