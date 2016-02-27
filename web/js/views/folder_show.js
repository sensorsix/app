var FolderShowView = Backbone.View.extend({
  tagName: 'tr',

  events: {
    "click a.delete"              : "destroy",
    "click a.edit"                : "edit",
    "click a.folder-icon-wrapper" : "collapse",
    "keyup #folder_name"          : "keyPressEventHandler"
  },

  initialize: function(options) {
    _.bindAll(this, 'render', 'destroy', 'edit', 'collapse');
    //this.model.bind('change', this.render);
    this.tableView = options.tableView;
  },

  render: function() {
    this.$el.html( _.template($('#folderTemplate').html())( this.model.attributes ) );
    return this;
  },

  // delete the model
  destroy: function () {
    if (confirm('Are you sure?')) {
      // we would call
      this.model.destroy();
      // which would make a DELETE call to the server with the id of the item
      this.$el.remove();
    }
  },

  edit: function() {
    var self = this, view;
    // if some row is already opened to edit
    if (this.tableView.currentFolderEditView) {
      var model = this.tableView.currentFolderEditView.model;

      model.set({ name: this.tableView.currentFolderEditView.$el.find('#folder_name').val() });
      view = new FolderShowView({
        model: model,
        tableView: self.tableView
      });

      this.tableView.currentFolderEditView.$el.replaceWith(view.render().$el.find('tr.folder'));

      this.tableView.currentFolderEditView = false;
    } else {
      view = new FolderEditView({
        model: this.model,
        tableView: self.tableView
      });

      this.$el.find('.folder').replaceWith(view.render().el);
      this.tableView.currentFolderEditView = view;
    }
  },

  collapse: function () {
    var $icon = this.$el.find('.folder-icon');
    var self = this;
    if ($icon.hasClass('fa-minus-square-o')) {
      $.ajax({
        url   : $icon.data('url'),
        data  : { state: 0 },
        success: function(r) {
          if (r == 'success') {
            $icon.removeClass('fa-minus-square-o');
            $icon.addClass('fa-plus-square-o');
            self.$el.find('.panel-body').slideUp();
          }
        }
      });
    } else {
      $.ajax({
        url   : $icon.data('url'),
        data  : { state: 1 },
        success: function(r) {
          if (r == 'success') {
            $icon.removeClass('fa-plus-square-o');
            $icon.addClass('fa-minus-square-o');
            self.$el.find('.panel-body').slideDown();
          }
        }
      });
    }
  },

  keyPressEventHandler : function(event){
    if(event.keyCode == 13){
      this.edit(event);
    }
  }
});