var TableViewOld = Backbone.View.extend({
  initialize: function(options) {
    _.bindAll(this, 'addOne', 'addAll');
    this.collection.bind('reset', this.addAll);
    this.rowTemplate = options.rowTemplate || '#rowTemplate';
  },

  addOne: function (object) {
    var view = new RowShowViewOld({
      model: object,
      tableView: this
    });
    this.$el.find("tbody.main").prepend(view.render().el);
    if ($.colorbox) {
      $('.colorbox').colorbox();
    }
  },

  addAll: function () {
    this.collection.each(this.addOne);
  },

  addNew: function (object, html) {
    var self = this,
      view;
    // if some row is already opened to edit
    if (this.currentEditView) {
      var model = this.currentEditView.model;

      $.get(model.get('fetch_url'), function (response) {
        // restore previous view
        model.set(response);
        view = new RowShowViewOld({
          model: model,
          tableView: self
        });
        self.currentEditView.destroyCKEditor();
        self.currentEditView.$el.replaceWith(view.render().el);

        // change to edit mode
        view = new RowEditViewOld({
          model: object,
          tableView: self
        });

        self.currentEditView = view;
        self.$el.find("tbody.main").prepend(view.el);
        view.$el.html(html);
      });
    } else {
      // change to edit mode
      view = new RowEditViewOld({
        model: object,
        tableView: this
      });

      this.currentEditView = view;
      this.currentEditView.model = object;
      this.$el.find("tbody.main").prepend(view.el);
      view.$el.html(html);
    }
  }
});

