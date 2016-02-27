var RowShowViewOld = Backbone.View.extend({
  tagName: 'tr',

  events: {
    "click a.delete": "destroy",
    "click a.edit": "edit"
  },

  initialize: function(options) {
    _.bindAll(this, 'render', 'destroy', 'edit');
    //this.model.bind('change', this.render);
    this.tableView = options.tableView;
  },


  render: function() {
    this.$el.html( _.template($(this.tableView.rowTemplate).html() )( this.model.attributes ) );
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

  edit: function(event) {
    var self = this, view;
    // if some row is already opened to edit
    if (this.tableView.currentEditView) {
      var model = this.tableView.currentEditView.model;

      self.tableView.currentEditView.saveCKEditor();
      $.get(model.get('fetch_url'), function (response) {
        // restore previous view
        model.set(response);
        view = new RowShowViewOld({
          model: model,
          tableView: self.tableView
        });
        self.tableView.currentEditView.destroyCKEditor();
        self.tableView.currentEditView.$el.replaceWith(view.render().el);

        // change to edit mode
        view = new RowEditViewOld({
          model: self.model,
          tableView: self.tableView
        });

        self.$el.replaceWith(view.render().el);
        self.tableView.currentEditView = view;
      });
    } else {
      view = new RowEditViewOld({
        model: this.model,
        tableView: this.tableView
      });

      this.$el.replaceWith(view.render().el);
      this.tableView.currentEditView = view;
    }
  }
});
