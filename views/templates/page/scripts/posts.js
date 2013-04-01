/**
 * Mobile Gateways
 *
 * An open source application framework for Mobile Gateways
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@theaustinconnergroup.info so we can send you a copy immediately.
 *
 * @package		Mobile Gateways
 * @author		Mobile Gateways Dev Team
 * @copyright   Copyright (c) 2013, The Austin Conner Group. (http://theaustinconnergroup.info/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://theaustinconnergroup.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * Models
 *
 *
 */
window.News = Backbone.Model.extend({
    url: '/api/feeds/news/'+feedId,
    defaults:{
    "id":null,
    "title":"",
    "content":"",
    "post_date":{"date":"2013-04-01 00:00:00","timezone_type":2,"timezone":"PDT"},
    "feed_id":feedId
    }
});

window.Feeds = Backbone.Collection.extend({
    model:News,
    id:0,   // feed id
    mo:0,   // news month

    initialize: function(options) {
        options || (options = {});
        this.id = options.id;
        this.mo = options.mo;
    },
    fetchMonth: function(options) {
        options || (options = {});
        this.id = options.id;
        this.mo = options.mo;
        this.fetch();
    },
    // override fetch url for addtional uri elements
    url:function() {
        // /feeds/{id}
        var uri = ''+ this.id;
        // /feeds/{id}/{month}
        uri = uri + (this.mo > 0 ? '/'+this.mo:'');
        // build new uri

        console.log(uri);
        return "/api/feeds/"+uri;
    },
    parse:function(response){
        //console.log(response);
        return response.data;
    }
});

/**
 * Views
 *
 *
 */
window.FeedView = Backbone.View.extend({
  el: '#feedsContext',
  collection: null,
  initialize: function(options){
    _.bind(this, 'render');
    this.listenTo(this.collection, 'reset', this.render);
    this.collection.fetch();

  },

  events:{
    "click .feed":"detail",
    "click #prevMo":"prevMo",
    "click #nextMo":"nextMo"
  },

  render: function(){
    var params = { posts: this.collection.models };

    var template = _.template($("#feeds").html(), params);
    $(this.el).html(template);
    return this;
  },

  prevMo: function(){
    console.log(--curMonth)
    this.collection.fetchMonth({id: feedId, mo: curMonth});
  },

  nextMo: function(){
    console.log(++curMonth)
    this.collection.fetchMonth({id: feedId, mo: curMonth});
  },

  /**
   * Display Event Details in a Modal Dialog
   *
   */
  detail: function(e){

    console.log('detail called');
    var target = e.target;
    model = this.collection.get(target.id);

    var template = _.template($("#postDetail").html(), model.toJSON());
    $('#dialog').html(template).modal();
    return this;
  }

});

window.NewsView = Backbone.View.extend({
  el: '#feedsContext',
  model: null,
  initialize: function(options){
    _.bind(this, 'render');
    this.listenTo(this.model, 'change', this.render);

  },
  events:{
    "change input":"change",
    "click #submit":"savePost",
    "click #close":"cancel"
  },
  change:function (event) {
      var target = event.target;
      console.log('changing ' + target.id + ' from: ' + target.defaultValue + ' to: ' + target.value);

  },
  savePost:function () {
    this.model.set({
        title: $('#postTitle').val(),
        description: $('#postContent').val(),
        post_date: {date: $('#postDate input').val(),timezone_type:2,timezone:timeZone}
    });
    if (this.model.isNew()) {
        var self = this;
        router.eventList.create(this.model, {
            success:function () {
                router.navigate('/', {trigger: true});
            }
        });
    } else {
        this.model.save();
         router.navigate('/', {trigger: true});
    }

    return false;
  },
  render: function(){

    var template = _.template($("#postForm").html(), this.model.toJSON());
    console.log(this.el);
    $(this.el).html(template);
    // initilize date/time controls
    $('.form_date').datetimepicker({
       //initalDate:  '',
       weekStart: 1,
       todayBtn:  1,
       autoclose: 1,
       todayHighlight: 0,
       startView: 2,
       minView: 2,
       forceParse: 0
    });
    return this;
  },
  cancel:function () {
    this.close();
    window.history.back();
  },

  close:function () {
    $('.datetimepicker').remove();
    $(this.el).unbind();
    $(this.el).empty();
  }

});

/**
 * Routes
 *
 *
 */
window.Routes = Backbone.Router.extend({

	routes: {
        "" : "index",                       // initial view
        "post/add" : "add",
        "post/remove/:id" : "remove",
		"post/edit/:id" : "edit"
	},
    /*
     * Display Current Month's Events
     */
    index: function(){

        this.feedList = new window.Feeds({id: feedId, mo: curMonth});
        new window.FeedView({collection: this.feedList});
    },
    /*
     * Add Event
     */
    add: function(){

         new window.NewsView({model: new window.News(), month: curMonth}).render();

    },
    /*
     * Edit Events
     */
    edit: function(id){
        var post = this.feedList.get(id);
        new window.NewsView({model:post}).render();
    },
    /*
     * Remove Events
     */
    remove: function(id){

    }
});


/**
 *
 * Start App
 *
 */
// template pattern (Mustache {{ name }})
_.templateSettings = {
    interpolate: /\{\{\=(.+?)\}\}/g,
    evaluate: /\{\{(.+?)\}\}/g
};
var router = new window.Routes();
Backbone.history.start({pushstate:false});

