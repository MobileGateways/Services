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
window.Photo = Backbone.Model.extend({
    url: '/api/gallery/photo/'+galId,
    defaults:{
    "id":null,
    "title":"",
    "resource":"",
    "type":"",
    "post_date":{"date":"2013-04-01 00:00:00","timezone_type":2,"timezone":"PDT"},
    "account":galId
    }
});

window.Gallery = Backbone.Collection.extend({
    model:Photo,
    id:0,   // gallery id
    mo:0,   // gallery month
    yr:0,   // gallery year
    startDate: '',
    endDate: '',
    initialize: function(options) {
        options || (options = {});
        this.id = options.id;
        this.mo = options.mo;
        this.yr = options.yr;
    },
    fetchMonth: function(options) {
        options || (options = {});
        this.id = options.id;
        this.mo = options.mo;
        this.yr = options.yr;
        this.fetch();
    },
    // override fetch url for addtional uri elements
    url:function() {
        // /gallery/{id}
        var uri = ''+ this.id;
        // /gallery/{id}/{month}
        uri = uri + (this.mo > 0 ? '/'+this.mo:'')+(this.yr > 0 ? '-'+this.yr:'2013');
        // build new uri

        console.log(uri);
        return "/api/gallery/"+uri;
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
window.GalleryView = Backbone.View.extend({
  el: '#eventContext',
  collection: null,
  initialize: function(options){
    _.bind(this, 'render');
    this.listenTo(this.collection, 'reset', this.render);
    this.collection.fetch();

  },

  events:{
    "click .event":"detail",
    "click #prevMo":"prevMo",
    "click #nextMo":"nextMo"
  },

  render: function(){
    var params = { gallery: this.collection.models };
    var template = _.template($("#gallery").html(), params);
    $(this.el).html(template);

    return this;
  },

  prevMo: function(){

    if(--curMonth < 1){
        curMonth = 12;
        --curYear;
    }
    navTime.subtract('M',1)
    this.collection.fetchMonth({id: galId, mo: curMonth, yr: curYear});
  },

  nextMo: function(){
    if(++curMonth > 12){
        curMonth = 1;
        ++curYear;
    }
    navTime.add('M',1)
    this.collection.fetchMonth({id: galId, mo: curMonth, yr: curYear});
  },

  /**
   * Display Photo Details in a Modal Dialog
   *
   */
  detail: function(e){

    console.log('detail called');
    var target = e.target;
    model = this.collection.get(target.id);

    var template = _.template($("#eventDetail").html(), model.toJSON());
    $('#dialog').html(template).modal();
    return this;
  }


});

window.PhotoView = Backbone.View.extend({
  el: '#eventContext',
  model: null,
  initialize: function(options){
    _.bind(this, 'render');
    this.listenTo(this.model, 'change', this.render);

  },

  gallery:{
    "change input":"change",
    "click #submit":"savePhoto",
    "click #close":"cancel"
  },

  change:function (event) {
      var target = event.target;
      console.log('changing ' + target.id + ' from: ' + target.defaultValue + ' to: ' + target.value);

  },
  savePhoto:function () {
    this.model.set({
        title: $('#eventTitle').val(),
        place: $('#eventPlace').val(),
        description: $('#eventDescription').val(),
        start_time: {date: $('#startDate input').val()+' '+$('#startTime input').val(),timezone_type:2,timezone:timeZone},// $('#eventTitle').val(),
        end_time: {date: moment($('#startDate input').val()+' '+$('#endTime input').val()),timezone_type:2,timezone:timeZone}, //$('#eventTitle').val()
        account: galId

    });
    if (this.model.isNew()) {
        var self = this;
        router.eventList.create(this.model, {
            success:function () {
                router.navigate('/', {trigger: true});
            }
        });
    } else {
        this.model.save({}, {
            success:function () {
                router.navigate('/', {trigger: true});
            }
        });
    }
    this.close();
    // force refreash
    preview = $('#preview').attr('src');
    $('#preview').attr('src', '');
    setTimeout(function () {
        $('#preview').attr('src', preview);
    }, 300);

    return false;
  },
  render: function(){
    var template = _.template($("#photoForm").html(), this.model.toJSON());
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
        "photo/add" : "add",
        "photo/remove/:id" : "remove",
        "photo/edit/:id" : "edit"
	},

    /*
     * Display Current Month's Photos
     */
    index: function(){

        this.eventList = new window.Gallery({id: galId, mo: curMonth, yr: curYear});
        new window.GalleryView({collection: this.eventList});

    },

    /*
     * Add Photo
     */
    add: function(){

         new window.PhotoView({model: new window.Photo(), month: curMonth, yr: curYear}).render();

    },
    /*
     * Edit Photos
     */
    edit: function(id){
        var event = this.eventList.get(id);
        new window.PhotoView({model:event}).render();
    },
    /*
     * Remove Photos
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

/**
 * Utilities
 *
 *
 */
