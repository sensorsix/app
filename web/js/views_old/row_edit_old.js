var RowEditViewOld = Backbone.View.extend({
  tagName: 'tr',

  events: {
    "click a.delete": "destroy",
    "click a.collapse": "collapse",
    "click a.add": "addNew"
  },

  initialize: function(options) {
    _.bindAll(this, 'render', 'destroyCKEditor', 'saveCKEditor', 'addNew'); // every function that uses 'this' as the current object should be in here
    this.tableView = options.tableView;
  },

  render: function() {
    var self = this;

    $.get(this.model.get('edit_url'), function(response) {
      self.$el.html(response);
    });

    return this;
  },

  addNew: function (event) {
    $('#add-row').trigger('click');
    $('html, body').animate({
      scrollTop: $("#table-view").offset().top
    }, 1000);
  },

  collapse: function () {
    var self = this, view;
    $.get(this.model.get('fetch_url'), function (response) {
      // restore previous view
      self.model.set(response);
      view = new RowShowViewOld({
        model: self.model,
        tableView: self.tableView
      });
      self.destroyCKEditor();
      self.$el.replaceWith(view.render().el);
      self.tableView.currentEditView = null;
    });
    return false;
  },

  destroyCKEditor: function () {
    this.$el.find('span[id^=cke_]').each(function () {
      var instance_id = this.id.replace('cke_', '');
      if (CKEDITOR.instances[instance_id]) {
        try {
          CKEDITOR.instances[instance_id].destroy();
        }
        catch(e) {

        }
        delete CKEDITOR.instances[instance_id];
      }
    });
  },

  saveCKEditor: function() {
    if (typeof CKEDITOR != 'undefined') {
      for (var index in CKEDITOR.instances) {
        var $textarea = $('#' + CKEDITOR.instances[index].name);
        if (CKEDITOR.instances[index]) {
          try {
            if ($textarea.val() != CKEDITOR.instances[index].getData()) {
              $textarea.val(CKEDITOR.instances[index].getData()).trigger('change');
            }
          }
          catch (e) {
          }
        }
      }
    }
  },

  // delete the model
  destroy: function () {
    if (confirm('Are you sure?')) {
      // we would call
      this.model.destroy();
      this.destroyCKEditor();
      // which would make a DELETE call to the server with the id of the item
      this.$el.remove();
    }
  }
});
