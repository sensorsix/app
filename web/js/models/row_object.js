var RowObject = Backbone.Model.extend({
  destroy: function () {
    $.post(this.get('delete_url'), { id: this.get('id') });
  },

  toJSON: function() {
    var attributes = _.clone(this.attributes);

    if (attributes._element_type == 'alternative') {
      attributes.name = '<a href="javascript:void(0)" title="Edit" class="edit_row" data-id="' + attributes.id + '">' + attributes.name + '</a>';
      attributes.work_progress = attributes.work_progress + '%';
    }else if (attributes._element_type == 'criterion') {
      attributes.name = '<a href="javascript:void(0)" title="Edit" class="edit_row" data-id="' + attributes.id + '">' + attributes.name + '</a>';
    }else if (attributes._element_type == 'role') {
      attributes.name = '<a href="javascript:void(0)" title="Edit" class="edit_row" data-id="' + attributes.id + '">' + attributes.name + '</a>';
      if (attributes.responses_count == 1){
        attributes.responses_count  += ' Respondent';
      }else{
        attributes.responses_count  += ' Respondents'
      }

      if (!attributes.active){
        attributes.workspace_link = '<div class="text-muted">Workspace inactive</div>';
      } else {
        attributes.workspace_link = '<div class="input-group input-group-sm survey-link-cell">' +
          '<input readonly style="cursor: default;" onclick="this.select();" type="text" name="url" class="form-control" value="' + attributes.url + '">' +
          '<span class="input-group-btn">' +
          '<button class="btn btn-info btn-copy copy-button" type="button" data-clipboard-text="' + attributes.url + '">Copy</button>' +
          '<button style="visibility:hidden;" class="btn btn-link btn-go go-button" type="button" onclick="javascript:window.open(\'' + attributes.url + '\'); return false" title="Click to open ' + attributes.url + ' in new tab.">Go</button>' +
          '</span></div>';
      }
    }else if (attributes._element_type == 'roadmap') {
      if (!attributes.active){
        attributes.roadmap_url = '<div class="text-muted">Roadmap inactive</div>';
      } else {
        attributes.roadmap_url = '<div class="input-group input-group-sm roadmap-link-cell">' +
          '<input readonly style="cursor: default;" onclick="this.select();" type="text" name="url" class="form-control" value="' + attributes.url + '">'+
          '<span class="input-group-btn">' +
          '<button class="btn btn-info btn-copy copy-button" type="button" data-clipboard-text="' + attributes.url + '">Copy</button>' +
          '<button style="visibility:hidden;" class="btn btn-link btn-go go-button" type="button" onclick="javascript:window.open(\'' + attributes.url + '\'); return false" title="Click to open ' + attributes.url + ' in new tab.">Go</button>' +
          '</span>';
      }
    }

    return attributes;
  }
});

var RowCollection = Backbone.Collection.extend({
    model: RowObject
});