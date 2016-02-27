var FolderEditView = Backbone.View.extend({
  tagName: 'tr',

  events: {
    "click a.delete"    : "destroy",
    "keyup #folder_name": 'processKey'
  },

  initialize: function(options) {
    _.bindAll(this, 'render', 'processKey'); // every function that uses 'this' as the current object should be in here
    this.tableView = options.tableView;
  },

  processKey: function (event) {
    var view, model = this.tableView.currentFolderEditView.model;
    if (event.keyCode == 13) {
      var name = this.$el.find('#folder_name').val();
      if (!name) {
        name = this.$el.find('#folder_name').data('default');
      }
      model.set({ name: name });

      view = new FolderShowView({
        model: model,
        tableView: this.tableView
      });
      if (this.tableView.currentFolderEditView.$el.closest('.panel-heading').length) {
        this.tableView.currentFolderEditView.$el.replaceWith(view.render().$el.find('tr.folder'));
      } else {
        this.tableView.currentFolderEditView.$el.replaceWith(view.render().el);
      }

      this.tableView.currentFolderEditView = false;
    }
  },

  render: function() {
    var self = this;

    $.get(this.model.get('edit_url'), function(response) {
      self.$el.html(response);
    });

    return this;
  },

  destroy: function () {
    if (confirm('Are you sure?')) {
      // we would call
      this.model.destroy();
      // which would make a DELETE call to the server with the id of the item
      this.$el.remove();

      this.tableView.currentFolderEditView = false;
    }
  }
});