var DataTableView = Backbone.View.extend({
  template: _.template('<table class="table table-striped table-bordered"></table>'),

  events  : {
    "click a.edit_row"    : "edit",
    "click a.edit_project": "edit",
    "click a.edit_roadmap": "edit",
    "mouseenter tr"       : '_mouseover',
    "mouseleave tr"       : '_mouseout'
  },

  currentEditView: null,

  columns: [],

  addToFolderURL: '',

  droppable: false,

  deletable: true,

  droppableActive: false,

  tableView: null,

  initialize: function(options) {
    var $this = this;

    this.collection.on('reset change add remove', function(){$this.render();});

    if (options.hasOwnProperty('droppable')) {
      this.droppable = options.droppable;
    }
    if (options.hasOwnProperty('deletable')) {
      this.deletable = options.deletable;
    }
    if (options.hasOwnProperty('tableView')) {
      this.tableView = options.tableView;
    }
  },

  _mouseover: function(e) {
    $(e.currentTarget).find('.row-tools').css('visibility', 'visible');
    $(e.currentTarget).find('.popoverLnk').popover({
      trigger : 'hover',
      html    : true
    });

    if (!this.droppableActive) {
      $(e.currentTarget).find('.help-tip').tooltip();
    }
  },
  _mouseout: function(e) {
    $(e.currentTarget).find('.row-tools').css('visibility', 'hidden');
  },

  render: function() {
    var self = this, oTable, tableViewSearch;

    this.$el.html(this.template());
    oTable = $('table:first', this.$el).DataTable({
      data      : this.collection.toJSON(),
      info      : false,
      paging    : false,
      searching : true,
      columns   : this.columns,
      columnDefs: [
        {
          "data"          : null,
          "defaultContent": "",
          "targets"       : '_all'
        }
      ]
    });

    $('#check-all', this.$el).on('change', function() {
      self.$el.find('.check').prop('checked',this.checked);
    });

    this.$el.on('change','.check', function() {
      var $checkboxes = self.$el.find('.check');
      var $checked = $checkboxes.filter(function() { return this.checked; });
      if($checked.length === 0) {
        $('#check-all', self.$el).prop({'indeterminate':false,'checked':false});
      } else if($checked.length === $checkboxes.length) {
        $('#check-all', self.$el).prop({'indeterminate':false,'checked':true});
      } else {
        $('#check-all', self.$el).prop('indeterminate',true);
      }
    });

    if (this.$el.closest('.table-view-holder-wrapper').length) {
      tableViewSearch = this.$el.closest('.table-view-holder-wrapper').find('.table-view-search');

      // apply filter on render
      if (tableViewSearch.find('input').val()) {
        oTable
            .search(  tableViewSearch.find('input').val() )
            .draw();
      }

      tableViewSearch.find('button').click( function() {
        oTable
          .search(  tableViewSearch.find('input').val() )
          .draw();
      } );
    }

    if (this.droppable) {
      this.$el.find('tbody > tr').draggable({
        revert: true,
        helper: function(event, ui) {
          var ret = jQuery(this).clone();
          var self = jQuery(this);
          var width = jQuery(this)[0].offsetWidth;
          var myHelper = [];

          ret.find('td').each(function(i) {
            $(ret.find('td')[i]).width(self.find('td')[i].offsetWidth);
          });

          myHelper.push('<table class="table table-striped table-bordered" style="width:' + width + 'px;">');
          myHelper.push(ret.html());
          myHelper.push('</table>');

          helper = myHelper.join('');
          return helper;
        },
        start: function() {
          self.droppableActive = true;
        },
        stop: function() {
          self.droppableActive = false;
        },
        cursor: "move"
      });

      this.$el.droppable({
        drop: function (event, ui) {
          ui.draggable.appendTo($(this).find('.table'));
          $.post(
            self.addToFolderURL,
            {
              folder_id: self.$el.closest('.table-view-holder').data('id'),
              id       : ui.draggable.find('.element-id').data('id')
            },
            function(result) {
              self.tableView.deleteFromCollection(result);
              self.collection.add(result);
            },
            'json'
          );
        }
      });
    }
  },

  edit: function(event) {
    if (this.currentEditView) {
      this.currentEditView.updateData({
        model   : this.collection.get(jQuery(event.currentTarget).data('id')),
        element : this,
        mode    : 'edit'
      })
    } else {
      this.currentEditView = new RowEditView({
        el      : $('#editRowContent'),
        model   : this.collection.get(jQuery(event.currentTarget).data('id')),
        element : this,
        mode    : 'edit'
      });
    }
    this.currentEditView.render();
  },

  addNew: function (html) {
    // if some row is already opened to edit
    if (this.currentEditView) {
      // Check if ckeditor was created and destroy it
      $.each([ 'alternative_additional_info', 'alternative_notes', 'criterion_description' ], function( index, value ) {
        if (CKEDITOR.instances[value]) { CKEDITOR.instances[value].destroy(true); }
      });

      this.currentEditView.updateData({
        model   : null,
        element : this,
        mode    : 'create'
      });
    } else {
      this.currentEditView = new RowEditView({
        el      : $('#editRowContent'),
        model   : null,
        element : this,
        mode    : 'create'
      });
    }
    $('#editRowContent').html(html);
    $('#editRowModal').modal('show');
  }
});