var TableView = Backbone.View.extend({

  addToFolderURL: '',

  type: '',

  collection: [],

  initialize: function(options) {
    _.bindAll(this, 'addFolder', 'addFolders', 'addNewProject', 'addNewRoadmap');
    this.type           = options.type;
    this.addToFolderURL = options.addToFolderURL;
  },

  addNewFolder: function (response) {
    this.addFolder(new RowObject(response)).edit();
  },

  addNewProject: function (html) {
    // if some row is already opened to edit

    if (this.currentEditView) {
      this.model = false;

      this.currentEditView.updateData({
        el        : $('#editRowModal'),
        model     : null,
        element   : _.find( this.collection, function (folder) { if (typeof folder == 'object' && !folder.deletable) return folder; } ),
        tableView : this,
        mode      : 'create'
      });
    } else {
      // change to edit mode
      this.currentEditView = new RowEditView({
        el        : $('#editRowModal'),
        model     : null,
        element   : _.find( this.collection, function (folder) { if (typeof folder == 'object' && !folder.deletable) return folder; } ),
        tableView : this,
        mode      : 'create'
      });

      this.currentEditView.model = new RowObject;
    }
    $('#editRowContent').html(html);
    $('#editRowModal').modal('show');
  },

  addNewRoadmap: function (html) {
    // if some row is already opened to edit

    if (this.currentEditView) {
      this.model = false;

      this.currentEditView.updateData({
        el        : $('#editRowModal'),
        model     : null,
        element   : _.find( this.collection, function (folder) { if (typeof folder == 'object' && !folder.deletable) return folder; } ),
        tableView : this,
        mode      : 'create'
      });
    } else {
      // change to edit mode
      this.currentEditView = new RowEditView({
        el        : $('#editRowModal'),
        model     : null,
        element   : _.find( this.collection, function (folder) { if (typeof folder == 'object' && !folder.deletable) return folder; } ),
        tableView : this,
        mode      : 'create'
      });

      this.currentEditView.model = new RowObject;
    }
    $('#editRowContent').html(html);
    $('#editRowModal').modal('show');
  },

  deleteFromCollection: function(received_item) {
    _.find( this.collection, function (folder) {
        folder.collection.find(function (item) {
          if (item && item.get('id') == received_item.id) {
            folder.collection.remove(item);
          }
        });
    } );
  },

  addFolder: function (folder) {
    var rowCollection, generalTableView,
        view = new FolderShowView({
          model     : folder,
          tableView : this
        }),
        projects = folder.get(this.getCollectionProperty());

    this.$el.find("tbody.main").prepend(view.render().el);

    rowCollection     = new RowCollection;
    generalTableView  = new DataTableView({ collection: rowCollection, droppable: true, deletable: folder.get('deletable'), tableView: this });
    generalTableView.addToFolderURL = this.addToFolderURL;

    view.$el.find('.table-view-holder').empty().append(generalTableView.$el);
    generalTableView.columns = this.getColumns();
    generalTableView.render();
    rowCollection.reset(projects);

    this.collection.push(generalTableView);

    return view;
  },

  addFolders: function (folders) {
    folders.each(this.addFolder);
  },

  getColumns: function () {
    if (this.type == 'roadmap') {
      return [
        {"title": "Name", "data": 'displayed_name'},
        {"title": "Description", "data": 'description'},
        {"title": "Link", "data": 'roadmap_url', "sorting": false}
      ];
    }else{
      return [
        {"title": "Name", "data": 'displayed_name'},
        {"title": "Quick Stats", "data": 'quick_stats', "sorting": false},
        {"title": "Owner", "data": 'assigned_to'}
      ];
    }
  },

  getCollectionProperty: function() {
    if (this.type == 'roadmap') {
      return 'roadmaps';
    }else{
      return 'projects';
    }
  }
});
