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
    url: '/api/ads/copy/'+chId,
    defaults:{
    "id":null,
    "title":"",
    "content":"",
    "post_date":{"date":"2013-04-01 00:00:00","timezone_type":2,"timezone":"PDT"},
    "account":chId
    }
});

window.Feeds = Backbone.Collection.extend({
    model:News,
    id:0,   // feed id
    mo:0,   // news month
    yr:0,   // calendar year

    initialize: function(options) {
        options || (options = {});
        this.id = options.id;
        this.yr = options.yr;
        this.mo = options.mo;
    },
    fetchMonth: function(options) {
        options || (options = {});
        this.id = options.id;
        this.yr = options.yr;
        this.mo = options.mo;
        this.fetch();
    },
    // override fetch url for addtional uri elements
    url:function() {
        // /feeds/{id}
        var uri = ''+ this.id;
        // /feeds/{id}/{month}
        uri = uri + (this.mo > 0 ? '/'+this.mo:'')+(this.yr > 0 ? '-'+this.yr:'2013');
        // build new uri

        console.log(uri);
        return "/api/ads/"+uri;
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
window.AdsView = Backbone.View.extend({
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
    var params = { ads: this.collection.models };

    var template = _.template($("#ads").html(), params);
    $(this.el).html(template);
    return this;
  },

  prevMo: function(){

    if(--curMonth < 1){
        curMonth = 12;
        --curYear;
    }

    this.collection.fetchMonth({id: chId, mo: curMonth, yr: curYear});
  },

  nextMo: function(){
    if(++curMonth > 12){
        curMonth = 1;
        ++curYear;
    }
    this.collection.fetchMonth({id: chId, mo: curMonth, yr: curYear});
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

window.AdView = Backbone.View.extend({
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
        content: $('#postContent').val(),
        post_date: {date: $('#postDate input').val(),timezone_type:2,timezone:timeZone}
    });
    if (this.model.isNew()) {
        var self = this;
        router.adsList.create(this.model, {
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
	// build content
    var template = _.template($("#postForm").html(), this.model.toJSON());
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
        "ad/add" : "add",
        "ad/remove/:id" : "remove",
		"ad/edit/:id" : "edit"
	},
    /*
     * Display Current Month's Events
     */
    index: function(){

        this.adsList = new window.Feeds({id: chId, mo: curMonth, yr: curYear});
        new window.AdsView({collection: this.adsList});
    },
    /*
     * Add Event
     */
    add: function(){

         new window.AdView({model: new window.News(), month: curMonth, yr: curYear}).render();

    },
    /*
     * Edit Events
     */
    edit: function(id){
        var post = this.adsList.get(id);
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
