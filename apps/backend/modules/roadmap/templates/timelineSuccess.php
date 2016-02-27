<?php
/**
 * @var $roadmap Roadmap
 * @var $timeline_data array()
 * @var $un_finished_decisions array()
 */


$form = new RoadmapForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

$form = new DecisionForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

$form = new AlternativeForm();
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

decorate_with('steps_layout');

$sf_response->setTitle(htmlentities($roadmap->getName()));

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




<div id="timeline-embed"></div>

<?php if (count($un_finished_decisions)): ?>
  <div class="row text-center mr-top-50">
    You have following <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE, true) ?> without start date on the roadmap. These project are not shown on the timeline.<br>Click below to add them to the timeline by providing start date:
    <ul class="mr-top-10" style="list-style-type: none;">
      <?php foreach ($un_finished_decisions as $un_finished_decision): ?>
        <li><a href="<?php echo url_for('@dashboard?decision_id=' . $un_finished_decision->getId()) ?>"><?php echo $un_finished_decision->getName();?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($timeline_data): ?>
<script type="text/javascript">
  $(function(){
    createStoryJS({
      type:       'timeline',
      width:      '100%',
      height:     '600',
      source:     <?php echo $sf_data->getRaw('timeline_data') ?>,
      embed_id:   'timeline-embed'
    });
  });
</script>
<?php endif; ?>

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
    var applyActionsForAlternative = function($button){
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

      $('.save_alternative').on('click', function(){
        var
          links_post            = [],
          related_alternatives  = [],
          due_date              = new Date($('#alternative_due_date').val()),
          notify_date           = new Date($('#alternative_notify_date').val());

        $('#links').children().each(function(){
          links_post.push({ 'link': $('.link-input', $(this)).val(), 'id': $('.link-input', $(this)).data('link-id') });
        });

        $('#alternative_related_alternatives option:selected').each(function(){
          related_alternatives.push($(this).val());
        });

        $.ajax({
          url : $('#save_url').val(),
          type: "POST",
          data: {
            "alternative[name]"             : $('#alternative_name').val(),
            "alternative[status]"           : $('#alternative_status').val(),
            "alternative[additional_info]"  : CKEDITOR.instances['alternative_additional_info'].getData(),
            "alternative[notes]"            : CKEDITOR.instances['alternative_notes'].getData(),
            "alternative[assigned_to]"      : $('#alternative_assigned_to').find('option:selected').val(),
            "alternative[external_id]"      : $('#alternative_external_id').val(),
            "alternative[work_progress]"    : $('#alternative_work_progress').val(),
            "alternative[due_date]"         : $('#alternative_due_date').val() ? due_date.getFullYear() + '-' + (due_date.getMonth() + 1) + '-' + due_date.getDate() : '',
            "alternative[notify_date]"      : $('#alternative_notify_date').val() ? notify_date.getFullYear() + '-' + (notify_date.getMonth() + 1) + '-' + notify_date.getDate() : '',
            "tags"                          : JSON.stringify($(".tags_input").tagsinput('items')),
            "links"                         : JSON.stringify(links_post),
            "related_alternatives"          : JSON.stringify(related_alternatives)
          },
          success : function (response) {
            try{
              response = jQuery.parseJSON( response );
            }catch(e){}

            if (typeof response === 'object'){
              window.location.reload();
            }else{
              editor = CKEDITOR.instances['alternative_additional_info'];
              if (editor) { editor.destroy(true); }
              editor = CKEDITOR.instances['alternative_notes'];
              if (editor) { editor.destroy(true); }

              $('#editRowContent').html(response);

              applyActionsForAlternative($button);
            }
          },
          error: function(response){
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
      })
    };

    $('#edit-roadmap').on('click', function(){
      $.get('<?php echo url_for('roadmap\edit', array('id' => $roadmap->getId())) ?>', function(response) {
        $('#editRowModal').modal('show');

        // Check if ckeditor was created and destroy it
        $.each([ 'alternative_additional_info', 'alternative_notes', 'decision_objective', 'roadmap_description'  ], function( index, value ) {
          var editor = CKEDITOR.instances[value];
          if (editor) { editor.destroy(true); }
        });

        $('#editRowContent').html(response);

        applyActionsForRoadmap();
      });
    });

    $(document).on('click', function(event){
      var $this = $(event.target);

      if ($this.hasClass('alternative_edit')) {
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
      }else if ($this.hasClass('edit-decision')){
        $.get($this.data('edit-url'), function(response) {
          $('#editRowModal').modal('show');

          // Check if ckeditor was created and destroy it
          $.each([ 'alternative_additional_info', 'alternative_notes', 'decision_objective', 'roadmap_description'  ], function( index, value ) {
            var editor = CKEDITOR.instances[value];
            if (editor) { editor.destroy(true); }
          });

          $('#editRowContent').html(response);

          applyActionsForDecision($this);
        });
      }
    });
  });
</script>

<style>
  .flag-content > h3 {
    overflow-x: hidden;
    overflow-y: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: white!important;
  }

  .content-container > .text > .container > h3 {
    color: #999;
  }

  .media-container > .plain-text > .container > h2 {
    color: #999;
  }

  .content-container > .text > .container > h3 > span > a {
    color: #08c;;
  }

  .flag-content {
    overflow-x: hidden;
    overflow-y: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: white!important;
  }

  .flag-content:hover{
    width: auto!important;
  }

  .flag:hover {
    width: auto!important;
  }

  .marker .flag-content {
    opacity: 0.5;
  }

  .marker.active .flag-content {
    opacity: 1;
  }
</style>