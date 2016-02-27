var RowEditView = Backbone.View.extend({
  element   : null,
  model     : null,
  mode      : '',
  tableView : null,
  only_edit : false,

  events  : {
    "click a.delete"                : "destroy",
    "click a.add"                   : "addNew",
    "click button.save_alternative" : "saveAlternative",
    "click button.save_release"     : "saveRelease",
    "click button.save_criterion"   : "saveCriterion",
    "click button.save_role"        : "saveRole",
    "click button.save_roadmap"     : "saveRoadmap",
    "click a#roadmap_next_1"        : "roadmap_first_next",
    "click a#roadmap_next_2"        : "roadmap_second_next",
    "click a.save_project"          : "saveProject",
    "click a.create_project"        : "saveProject"
  },

  initialize: function(options) {
    _.bindAll(this, 'render', 'addNew'); // every function that uses 'this' as the current object should be in here
    this.element = options.element;
    this.model   = options.model;
    this.mode    = options.mode;

    if (options.hasOwnProperty('tableView')) {
      this.tableView = options.tableView;
    }

    if (options.hasOwnProperty('only_edit')) {
      this.only_edit = options.only_edit;
    }
  },

  render: function() {
    this.clearCKEditor();
    var only_edit = this.only_edit;

    $.get(this.model.get('edit_url'), function(response) {
      $('#editRowModal').modal('show');
      $('#editRowContent').html(response);

      // don't show "Delete" button
      if (only_edit) {
        $('.modal-footer .edit-delete').remove();
      }
    });

    return this;
  },

  updateData: function(options){
    this.clearCKEditor();

    this.element = options.element;
    this.model   = options.model;
    this.mode    = options.mode;

    if (options.hasOwnProperty('tableView')) {
      this.tableView = options.tableView;
    }

    if (options.hasOwnProperty('only_edit')) {
      this.only_edit = options.only_edit;
    }
  },

  addNew: function () {
    $('#add-row').trigger('click');
    $('html, body').animate({
      scrollTop: $("#table-view").offset().top
    }, 1000);
  },

  // delete the model
  destroy: function () {
    if (confirm('Are you sure?')) {
      this.model.collection.remove(this.model);
      this.model.destroy();
      $('#editRowModal').modal('hide');
    }
  },

  saveAlternative: function(){
    var
      $this                 = this,
      linksPost             = [],
      dueDate               = new Date($('#alternative_due_date').val()),
      notifyDate            = new Date($('#alternative_notify_date').val());

    $('#links').children().each(function(){
      linksPost.push({ 'link': $('.link-input', $(this)).val(), 'id': $('.link-input', $(this)).data('link-id') });
    });

    $.ajax({
      url : $('#save_url').val(),
      type: "POST",
      data: {
        "alternative[name]"             : $('#alternative_name').val(),
        "alternative[status]"           : $('#alternative_status').val(),
        "alternative[additional_info]"  : CKEDITOR.instances['alternative_additional_info'].getData(),
        "alternative[notes]"            : CKEDITOR.instances['alternative_notes'].getData(),
        "alternative[assigned_to]"      : $('#alternative_assigned_to').val(),
        "alternative[external_id]"      : $('#alternative_external_id').val(),
        "alternative[work_progress]"    : $('#alternative_work_progress').val(),
        "alternative[due_date]"         : $('#alternative_due_date').val() ? dueDate.getFullYear() + '-' + (dueDate.getMonth() + 1) + '-' + dueDate.getDate() : '',
        "alternative[notify_date]"      : $('#alternative_notify_date').val() ? notifyDate.getFullYear() + '-' + (notifyDate.getMonth() + 1) + '-' + notifyDate.getDate() : '',
        "tags"                          : JSON.stringify($(".tags_input").tagsinput('items')),
        "links"                         : JSON.stringify(linksPost),
        "related_alternatives"          : JSON.stringify($('#alternative_related_alternatives').val())
      },
      success : function (response) {
        try{
          response = jQuery.parseJSON( response );
        }catch(e){}

        if (typeof response === 'object'){
          response.tags = $(".tags_input").tagsinput('items').join(', ');
          if ($this.mode == 'edit'){
            $this.model.set(response);
          }else{
            $this.element.collection.add(response);
          }
          $('#editRowModal').modal('hide');
        }else{
          $this.clearCKEditor();
          $('#editRowContent').html(response);
        }
      },
      error: function(response){
        $('#editRowModal').modal('hide');
      }
    });
  },

  saveRelease: function() {
    var
      $this     = this,
      startDate = new Date($('#project_release_start_date').val()),
      endDate   = new Date($('#project_release_end_date').val())
    ;

    $.ajax({
      url : $('#save_url_release').val(),
      type: "POST",
      data: {
        "project_release[name]"       : $('#project_release_name').val(),
        "project_release[status]"     : $('#project_release_status').val(),
        "project_release[start_date]" : $('#project_release_start_date').val() ? startDate.getFullYear() + '-' + (startDate.getMonth() + 1) + '-' + startDate.getDate() : '',
        "project_release[end_date]"   : $('#project_release_end_date').val() ? endDate.getFullYear() + '-' + (endDate.getMonth() + 1) + '-' + endDate.getDate() : '',
        "tags"                        : JSON.stringify($(".tags_input").tagsinput('items')),
        "criterion_id"                : $('.save_release').data('criterion_id')
      },
      success : function (response) {
        try{
          response = jQuery.parseJSON( response );
        }catch(e){}

        if (typeof response === 'object'){
          response.tags = $(".tags_input").tagsinput('items').join(', ');
          if ($this.mode == 'edit'){
            $('#release-' + response.id).find('.release-name').text(response.name);
          // new
          } else {
            $('.col-add-release').before(response.html);
            $('#editRowModal').trigger('release:added', response.id);
          }
          $('#editRowModal').modal('hide');
        }else{
          $('#editRowContent').html(response);
        }
      },
      error: function(response){
        $('#editRowModal').modal('hide');
      }
    });
  },

  saveCriterion: function(){
    var $this = this;

    $.ajax({
      url : $('#save_url').val(),
      type: "POST",
      data: {
        "criterion[name]"           : $('#criterion_name').val(),
        "criterion[description]"    : CKEDITOR.instances['criterion_description'].getData(),
        "criterion[variable_type]"  : $('#criterion_variable_type').val(),
        "criterion[measurement]"    : $('#criterion_measurement').val()
      },
      success : function (response) {
        try{
          response = jQuery.parseJSON( response );
        }catch(e){}

        if (typeof response === 'object'){
          if ($this.mode == 'edit'){
            $this.model.set(response);
          }else{
            $this.element.collection.add(response);
          }
          $('#editRowModal').modal('hide');
        }else{
          $this.clearCKEditor();
          $('#editRowContent').html(response);
        }
      }
    });
  },

  saveRole: function(){
    var
      $this = this,
      matrix  = [];

    $(".role-matrix-wrapper").find('input.measure').each(function(){
      var el = $(this);
      matrix.push({'alternative_id': el.data('alternative'), 'criterion_id': el.data('criterion'), 'checked': el.is(':checked') });
    });

    $.ajax({
      url : $('#save_url').val(),
      type: "POST",
      data: {
        "role[name]"                    : $('#role_name').val(),
        "role[comment]"                 : CKEDITOR.instances['role_comment'].getData(),
        "role[prioritize]"              : $('#role_prioritize').is(':checked')?'checked':'',
        "role[prioritization_method]"   : $('input[name="role[prioritization_method]"]:checked').val(),
        "role[collect_items]"           : $('#role_collect_items').is(':checked')?'checked':'',
        "role[display_items]"           : $('#role_display_items').is(':checked')?'checked':'',
        "role[allow_voting]"            : $('#role_allow_voting').is(':checked')?'checked':'',
        "role[language]"                : $("#role_language").val(),
        "role[continue_url]"            : $('#role_continue_url').val(),
        "role[anonymous]"               : $('#role_anonymous').is(':checked')?'checked':'',
        "role[updateable]"              : $('#role_updateable').is(':checked')?'checked':'',
        "role[show_criteria_weights]"   : $('#role_show_criteria_weights').is(':checked')?'checked':'',
        "role[show_alternatives_score]" : $('#role_show_alternatives_score').is(':checked')?'checked':'',
        "role[show_comments]"           : $('#role_show_comments').is(':checked')?'checked':'',
        "role[active]"                  : $('#role_active').is(':checked')?'checked':'',
        "matrix"                        : JSON.stringify(matrix)
      },
      success : function (response) {
        try{
          response = jQuery.parseJSON( response );
        }catch(e){}

        if (typeof response === 'object'){
          if (!response.error){
            if ($this.mode == 'edit'){
              $this.model.set(response);
            }else{
              $this.element.collection.add(response);
            }
            $('#editRowModal').modal('hide');
          }
        }else{
          $this.clearCKEditor();
          $('#editRowContent').html(response);
        }
      }
    });
  },

  saveRoadmap: function(){
    var $this         = this,
        decisions_id  = [];

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
            $this.model.set(response);
            $('#editRowModal').modal('hide');
          }
        }else{
          $this.clearCKEditor();
          $('#editRowContent').html(response);
        }
      }
    });
  },

  roadmap_first_next: function(){
    var self = this;

    $.ajax({
      url : $('#save_url').val(),
      type: "POST",
      data: {
        "roadmap_id"     : $('#roadmap_id').val(),
        "roadmap[name]"  : $('#roadmap_name').val(),
        "to_step"        : 2
      },
      dataType: "json",
      success : function (data) {
        if (data.status == 'success'){
          if (self.mode == 'edit'){
            self.model.set(data.roadmap);
          }else{
            if (typeof self.element == 'undefined') {
              self.tableView.addFolder(new RowObject(data.folder));
            }else {
              self.element.collection.add(data.roadmap);
            }
          }
        }
        $('#editRowContent').html(data.html);
      },
      error: function(jqXHR){

      }
    });
  },

  roadmap_second_next: function(){
    var selected = [];
    $('#roadmap_decisions option:selected').each(function() {
      selected.push($(this).val());
    });

    $.ajax({
      url : $('#save_url').val(),
      type: "POST",
      data: {
        "roadmap_id"        : $('#roadmap_id').val(),
        "roadmap_decisions" : JSON.stringify(selected),
        "to_step"           : 3
      },
      dataType: "json",
      success : function (data, textStatus, jqXHR) {
        if (data.status == 'success'){
          $('#editRowModal').modal('hide');
        }
      },
      error: function(jqXHR){

      }
    });
  },

  saveProject: function(){
    var $this     = this,
        start_date  = new Date($('#decision_start_date').val()),
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
        "decision[color]"       : $('#decision_color').val(),
        "decision[status]"      : $('#decision_status').val(),
        "decision[type_id]"     : $('#decision_type_id').val(),
        "decision[template_id]" : $('#decision_template_id').val(),
        "tags"                  : JSON.stringify($(".tags_input").tagsinput('items'))
      },
      success : function (response) {
        if (_.has(response, 'status') && response.status === 'error'){
          $this.clearCKEditor();
          $('#editRowContent').html(response.html);
        }else{
          if (response.redirect) {
            window.location = response.redirect;
          }else {
            $this.model.set(response.data);
            $('#editRowModal').modal('hide');
          }
        }
      }
    });
  },

  clearCKEditor: function() {
    // Check if ckeditor was created and destroy it
    $.each([ 'alternative_additional_info', 'criterion_description', 'role_comment', 'alternative_notes', 'roadmap_description', 'decision_objective' ], function( index, value ) {
      var editor = CKEDITOR.instances[value];
      if (editor) { editor.destroy(true); }
    });
  }
});
