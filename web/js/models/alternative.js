var AlternativeObject = Backbone.Model.extend({});

var AlternativeHeaderObject = Backbone.Model.extend({});

var AlternativeCollection = Backbone.Collection.extend({
  model: AlternativeObject
});

var AlternativeView = Backbone.View.extend({
  events  : {},

  tagName: 'tr',

  template: _.template('<% _.each(alternative, function(value, key){ %><td><%- value %></td><% }); %>'),

  initialize: function() {
    this.render();
  },

  render: function() {
    this.$el.html( this.template({alternative: this.model.toJSON()}));
  }
});

var AlternativeHeaderView = Backbone.View.extend({
  events  : {
    'change select': 'updateValue'
  },

  tagName: 'tr',

  templateEl: null,

  initialize: function(options) {
    this.templateEl = options.templateEl;

    _.mapObject(this.model.attributes, function(val, key) {
      if (_.indexOf(['name', 'status', 'work progress', 'tags', 'additional info', 'notes', 'due date', 'notify date'], key) !== -1) {
        this.model.set(key, key);
      } else {
        this.model.set(key, null);
      }
    }, this);

    this.render();
  },

  render: function() {
    this.$el.html( _.template(this.templateEl.html() )({alternative: this.model.attributes}) );
  },

  updateValue: function(event) {
    var $currentTarget = $(event.currentTarget);

    if (this.model.get($currentTarget.data('key')) !== undefined) {
      this.model.set($currentTarget.data('key'), $currentTarget.val());
    }
  }
});

var AlternativeCollectionView = Backbone.View.extend({
  events  : {
    'click #submit-excel-import-2' : 'save'
  },

  $headerTemplate: null,

  alternativeHeaderView: {},

  alternativeViewCollection: [],

  urlFor: null,

  $importError: null,

  initialize: function(options) {
    var $this = this;

    $this.$headerTemplate  = options.headerTemplate;
    $this.urlFor           = options.urlFor;
    $this.$importError     = options.importError;

    this.collection.on('reset', function () {$this.render();});
  },

  render: function() {
    if (this.collection.length) {
      this.alternativeHeaderView = new AlternativeHeaderView({templateEl: this.$headerTemplate, model: new AlternativeHeaderObject(_.object(_.allKeys(this.collection.first().attributes), [null])) });
      this.$el.find('table').append(this.alternativeHeaderView.el);

      this.collection.each(function (alternative) {
        var alternativeView = new AlternativeView({model: alternative});
        this.$el.find('table').append(alternativeView.el);
        this.alternativeViewCollection.push(alternativeView);
      }, this);
    }
  },

  save: function(element) {
    var alternativeViewCollectionJson = [],
        self = this,
        ladda = $( element.currentTarget).ladda();

    ladda.ladda('start');

    _.each(this.alternativeViewCollection, function(alternativeView){
      alternativeViewCollectionJson.push(alternativeView.model.toJSON());
    });

    this.$importError.html('');

    $.ajax({
      type: "POST",
      url : this.urlFor,
      data: {
        header: this.alternativeHeaderView.model.toJSON(),
        data  : alternativeViewCollectionJson
      },
      dataType: 'json',
      success: function(response) {
        if (response.status == 'error') {
          self.$importError.html(response.text);
        }else {
          window.location.reload();
        }
      },
      error: function() {
        self.$importError.html('Error');
        ladda.ladda('stop');
      }
    })
  }
});

// Extend backbone mixin
_.mixin({
  capitalize: function(string) {
    return string.charAt(0).toUpperCase() + string.substring(1).toLowerCase();
  }
});