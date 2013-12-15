"use strict";

var dispatcher = {};
_.extend(dispatcher, Backbone.Events);

var Filter = Backbone.Model.extend({

    valid: false,

    defaults: {
        page : "",
        screenwidth: ""
    },

    initialize: function(){
        this.on("change", this.validate);
    },

    validate: function(){
        var valid = true;
        for(var attr in this.attributes) {

            if(this.attributes[attr].length == 0) {
                valid = false;
            }
        }
        if(valid) {
            dispatcher.trigger("filter_change", this.attributes);
        }
    },

    pageIsUrl: function(){
        return this.attributes.page.match(/^http/) != null;
    }

});


var Click = Backbone.Model.extend({

});

var ClickCollection = Backbone.Collection.extend({
  model: Click,

  url: '/sites/'+site+'/clicks',

  initialize: function(){
    var $this = this;
    dispatcher.on("filter_change", function(filters) {
        $this.update(filters);
    });
  },

  update: function(filters){
    this.fetch({data:filters});
  }
});



var FilterView = Backbone.View.extend({

  el: $("section.filters"),

  events: {
    "change #page":          "changePage",
    "change #screenwidth":   "changeWidth"
  },

  changePage: function(e){
      this.model.set({ page : e.currentTarget.value });
  },

  changeWidth: function(e){
      this.model.set({ screenwidth : e.currentTarget.value });
  }

});



var WebView = Backbone.View.extend({

  el: $("div.webview"),

  initialize: function(){
    this.iframe = this.$el.find('iframe');
    this.addressbar = this.$el.find('input.addressbar');

    this.listenTo(this.model, "change:page", this.updatePage);
    this.listenTo(this.model, "change:screenwidth", this.updateWidth);
  },

  events: {
    "submit form.addressbar": "changeUrl"
  },

  changeUrl: function(e) {
    e.preventDefault();
    this.iframe.attr('src', this.addressbar.val());
  },

  updatePage: function() {
    if(this.model.pageIsUrl()) {
        var url = this.model.get('page');
// debugger
        this.iframe.attr('src', url);
        //this.addressbar.val(url);
        document.getElementById("addressbar").value = url;
    }
  },

  updateWidth: function() {
    var width = this.model.get('screenwidth');

    if(width > 0) {
        this.$el.css({ width : width + "px" });
    }
  }
});

var HeatMapView = Backbone.View.extend({

  el: $('#heatmapContainer'),


  events: {
    "click a.replay": "replay"
  },

  initialize: function(){

    this.heatmap = createWebGLHeatmap({canvas: $('#heatmapContainer canvas')[0]});
    var $this = this;
    // Keeps canvas up to date with the latest click data added to the
    // map, and animates the existing points by making them slowly blur
    // and fade out
    
    var heatmapLoop = function(){

        // update heatmap with new data
        $this.heatmap.update();
        $this.heatmap.display();

        // make old points slowly disappear
        $this.heatmap.multiply(0.99);
        $this.heatmap.blur();

        setTimeout(function () {
            requestAnimationFrame(heatmapLoop);
        }, 100);
    };
    requestAnimationFrame(heatmapLoop);

    this.listenTo(this.collection, "reset", this.play);
  },

  play: function() {

    var $this = this, i = 0, l = _.size(this.collection);

    if(this.timer) {
        clearTimeout(this.timer);
        this.timer = 0;
    }

    console.log("received "+l+"clicks");

    if(l == 0) return;

    (function iterator() {
        var click = $this.collection.at(i);

        $this.heatmap.addPoint(
            click.get('x'), // x coordinate
            click.get('y'), // y coordinate
            40,             // size
            30/l             // intensity
        );

        if(++i < l) {
            var delay = $this.collection.at(i).get('elapsed') - click.get('elapsed');
            $this.timer = setTimeout(iterator, delay);
        }
    })();
  },

  replay: function(e) {
    this.heatmap.clear();
    e.preventDefault();
    this.play();
  }, 
  timer: 0
});




var filter = new Filter;
var clicks = new ClickCollection;

var filterView = new FilterView({ model: filter });
var webView = new WebView({ model: filter });
var heatmapView = new HeatMapView({ collection: clicks });
