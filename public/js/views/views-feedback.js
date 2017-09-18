// naming convention 
// ids & classes	: bla-bla-bla
// functions		: blaBlaBla
// variables		: bla_bla_bla
// cuepoint 		= player.currentTime() 	= 1.2333
// cuepoint_code 	= timecode	 			= 01:02:03

"use strict";
App.Views.EditCommentView = Backbone.View.extend({

    tagName:'form',

	template: _.template($('#edit-comment-form-template').html()),
    
    events: {
		"click .submit-edit-comment": "submitEditComment",
		"click .cancel-edit-comment": "cancelEditComment",
        "keydown"                   : "keyPress"
	},

    initialize: function(){
        var that = this;
	    _.bindAll(this, 'submitEditComment', 'cancelEditComment', 'keyPress');
        setTimeout(function(){
            that.$el.find('.feedback-note:first').focus();
            // focus cursor on the text box
            var $initial_val = that.$el.find('.feedback-note:first').val();
            // setting cursor at the end of the text
            that.$el.find('.feedback-note:first').val('').val($initial_val);
        }, 100);
    },

    render: function () {
        var that = this;
		return this.$el.html(that.template(that.model.toJSON()));
	},

	cancelEditComment: function () {
        this.$el.parent().append(this.model.get('parentComment').comment);
        this.$el.remove();
	},

    submitEditComment: function () {
        var that = this,
            comment = this.$el.find("textarea[name='feedback_note']").val(),
            div1 = $('<div/>'),
            div2 = $('<div/>');

        div1.css({
            'background'    :'yellow',
            'padding-left'  :'10px',
            'margin-top'    :'-20px',
        });
        div2.text('Editing comment...').css({
            'width':'960px',
            'padding-right':'10px',
            'margin-left'   :'auto',
            'margin-right'  :'auto'
        });
        div1.append(div2);
        $('.navbar').after(div1);
        // var svg_obj = this.model.get('belongs_to_cuepoint').get('svg');

        // var svg_code = App.Drawing.Export().export({
        //     width: '640', 
        //     height: '360'
		// });
        var svg_code = App.Drawing.Export().svg();
        
        //trim both ends of the string
        // var svg_trim_start = svg_code.substr(229);
        var svg_trim_start = svg_code.substr(177);
        var svg_trim_end = svg_trim_start.substring(0, svg_trim_start.length - 6);
        
		// the id of the item been edited/patched has to be set at the top level
        this.model.set({'id': that.model.get('parentComment').id});
        this.model.save({
            'parentComment':{
                'id': that.model.get('parentComment').id,
                'comment': comment,
                'svg': svg_trim_end
            }
        },{
            patch:true,
            success: function(model, response, options){
                console.log('comment edited sucessfuly');
                // update the comment and the canvas 
                if ( that.model.get('parentComment').svg != null ){
                    //Safari doesnot reconize the model.svg data type so, we force it onto Safari and append to the begining of model.svg
                    var safari_fix = IS_SAFARI() ? 'data:image/svg+xml,' : '';
                    var svg_raw = safari_fix + '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="640" height="360">' + that.model.get('parentComment').svg + '</svg>';
// console.log(svg_raw);
                    var canvas = that.$el.parent().parent().find('canvas')[0];
                    canvas.width = '640';
                    canvas.height = '360';
                    var ctx = canvas.getContext("2d");
                    var img = new Image();
                    //Safari does not reconize the Blob constructor, so we provide the 'model.svg' that includes the safari_fix string from above.
                    if(IS_SAFARI()){
                        img.src = svg_raw;
                        img.onload = function(){ ctx.drawImage(img,0,0); };
                    }else{
                        var DOMURL = self.URL || self.webkitURL || self;
                        var svg = new Blob([svg_raw], {type: "image/svg+xml;charset=utf-8"});
                        var url = DOMURL.createObjectURL(svg);
                        img.onload = function() {
                            ctx.drawImage(img, 0, 0);
                            DOMURL.revokeObjectURL(url);
                        };
                        img.src = url;
                        // canvas[0].toDataURL();
                    }
                }
                that.$el.parent().text(comment);
                div1.slideUp('slow', function(){
                    $(this).remove();
                });
            },
            error: function(model, response, options){
                div2.text(response.statusText+', comment NOT saved. Please Erase all, and try again.');
                setTimeout(function(){
                    div2.slideUp('slow', function(){
                        $(this).remove();
                    });
                },10000);
            }
        });
	},

    keyPress: function(e) {
        var code = e.keyCode || e.which;
        if (e.keyCode == 27){
            this.cancelEditComment();
        }
    }

});

App.Views.AttachFileView = Backbone.View.extend({

    tagName: 'form',

    initialize: function(){
        var that = this;
	    _.bindAll(this, 'keyPress', 'cancelAttachFile');
        // console.log(this.model.get('parentComment').id);
        // setTimeout(function(){
        //     that.$el.find('.attach-file:first').trigger('click');
        // }, 100);
    },

    template: _.template($('#attach-file-form-template').html()),
    
    attributes: function(){
        return ({
            'class'         : 'attach-file-form',
            'method'        : 'post',
            'action'        : 'attachfile',
            'accept-charset': 'UTF-8',
            'enctype'       : 'multipart/form-data'
        });
    },

    events: {
		"click .submit-attach-file" : "submitAttachFile",
		"click .cancel-attach-file" : "cancelAttachFile",
        "keydown"                   : "keyPress"
	},

    render: function(){
        var that = this;
        this.$el.html(that.template());
        return this;
    },

    submitAttachFile: function(e){
        e.preventDefault(); //important 
        var that = this;

        // submit the attachment through AJAX
        that.$el.ajaxSubmit({

            resetForm: true,

            dataType: 'json',

            data: {
                video_title     : $('#video-title').val(),
                comment_id      : that.model.get('parentComment').id
            },

            // uploadProgress: function(event, position, total, percent) {
            //     $('progress').attr('value', percent);
            //     var percentComplete = percent + '%';
            // },

            success: function(response, status, xhr){
                var attachments = that.$el.closest('.composite-view-li').find('.attachments:first');
                var file_name_period = response.file_name;
                var index = file_name_period.lastIndexOf('_');
                file_name_period = file_name_period.substr(0,index)+'.'+file_name_period.substr(index+1);

                // if this is the first attachment add this h5
                if (attachments.children().length == 0)
                    attachments.append("<h5>Attachments</h5>");
                if (response.type == 'image'){
                     attachments.append("<figure><a class='"+response.file_name+" delete-attachment' href='javascript:void(0)'><img src='/img/close.png' /></a><a class='"+response.file_name+" attached-file' href='/video_review/"+response.file_url+"' target='_blank'><img src='/img/image-icon.png'><figcaption>"+file_name_period+"</figcaption></a></figure>");
                }
                if (response.type == 'video'){
                    attachments.append("<figure><a class='"+response.file_name+" delete-attachment' href='javascript:void(0)'><img src='/img/close.png' /></a><a class='"+response.file_name+" attached-file' href='/video_review/"+response.file_url+"' target='_blank'><img src='/img/video-icon.png'><figcaption>"+file_name_period+"</figcaption></a></figure>");
                }
                if (response.type == 'text'){
                    attachments.append("<figure><a class='"+response.file_name+" delete-attachment' href='javascript:void(0)'><img src='/img/close.png' /></a><a class='"+response.file_name+" attached-file' href='/video_review/"+response.file_url+"' target='_blank'><img src='/img/text-icon.png'><figcaption>"+file_name_period+"</figcaption></a></figure>");
                }
                if (response.type == 'pdf'){
                    attachments.append("<figure><a class='"+response.file_name+" delete-attachment' href='javascript:void(0)'><img src='/img/close.png' /></a><a class='"+response.file_name+" attached-file' href='/video_review/"+response.file_url+"' target='_blank'><img src='/img/pdf-icon.png'><figcaption>"+file_name_period+"</figcaption></a></figure>");
                }
                that.$el.remove();

                // broadcast custom attachment:added event
            },

            error: function (xhr, status, error){
                console.log('ajaxForm error error: ', error);
                alert('There was an error attaching your file, please try again');
            },

            // complete is call after success or error is called with the success or error textStatus
            complete: function(xhr, status) {
                console.log('ajaxForm complete status: ', status);
                backboneEvent.trigger('attachment:added');
                console.log($('#video-title'));
            },
        });
    },
    
    cancelAttachFile: function(){
        this.$el.remove();
    },

    keyPress: function(e) {
        var code = e.keyCode || e.which;
        if (e.keyCode == 27){
            this.cancel();
        }
    }
});

App.Views.CommentsCompositeView = Backbone.Marionette.CompositeView.extend({

    tagName: "li",

    template: _.template($("#comment-template").html()),


    className: "composite-view-li",

    events: {
		"click .edit-comment"       : "editComment",
		"click .destroy-comment"    : "destroyComment",
        "click .reply-comment"      : "replyComment",
        "click .time-link"          : "seekTime",
        "click .edit-image"         : "seekTime",
        "click .attach-file-link"   : "attachFile"
	},
    modelEvents: {
        "change": "modelChanged"
    },

    initialize: function(){
        /*
         * grab the child collection from the parent model
         * so that we can render the collection as children
         * of this parent node
         */
        if(this.model.childrenComments)
            this.collection = this.model.childrenComments;
        else
            this.collection = new App.Collections.Comments([],{});

		_.bindAll(this, 'editComment', 'destroyComment', 'replyComment', 'seekTime', 'attachFile');
    },
    modelChanged: function(model, unknown){
		// backboneEvent.trigger('model:changed',this.model);
        
    },
    attachFile: function(e){
        e.stopPropagation();
        var that = this;
        var $target = $(e.currentTarget);
        var attach_file_view = new App.Views.AttachFileView({model: that.model});

        $target.after(attach_file_view.render().el);
    },
    
    seekTime: function (event) {
        var that = this;
        App.Video.currentTime(this.model.get('parentComment').cuepoint_seconds);

        if ( !(this.model.get('parentComment').svg == '') ){
            svg_container.clear();
            svg_container.svg( that.model.get('parentComment').svg );
            if ($('.move').hasClass('current-tool')){
                window.draggableOn();
            }
        }
        // broadcast that the active cuepoint has changed, so that the playbackControls updates itself accordingly
        backboneEvent.trigger('cuepoint:setActive', that.model );
	},
    //This method is called when render is called
    // attachHtml: function(collectionView, childView, index){
    //     if (collectionView.isBuffering) {
          // buffering happens on reset events and initial renders
          // in order to reduce the number of inserts into the
          // document, which are expensive.
        //   collectionView._bufferedChildren.splice(index, 0, childView);
        // }else {
          // If we've already rendered the main collection, append
          // the new child into the correct order if we need to. Otherwise
          // append to the end.
        //   if (!collectionView._insertBefore(childView, index)){
        //     collectionView._insertAfter(childView);
        //   }
        // }
        // collectionView.$("ul:first").append(childView.el);
    // },
    
    onRender: function() {
        var that = this;
        this.$el.addClass('added-comment');
        setTimeout(function () {
            that.$el.removeClass('added-comment');
        }, 1000);

        if ( that.model.get('parentComment').cuepoint != null ){
            //Safari doesnot reconize the model.svg data type so, we force it onto Safari and append to the begining of model.svg
            var safari_fix = IS_SAFARI() ? 'data:image/svg+xml,' : '';
            // XML Namespace to avoid name conflicts with svgjs by differentiating elements or attributes within an XML document that may have identical names, but different definitions
            var svg_raw = safari_fix + '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="640" height="360">'+ that.model.get('parentComment').svg + '</svg>';

            var canvas = that.$el.find('.comment-canvas:first')[0];
            canvas.width = '640';
            canvas.height = '360';
            var ctx = canvas.getContext("2d");
            var img = new Image();
            //Safari does not reconize the Blob constructor, so we provide the 'model.svg' that includes the safari_fix string from above.
            if(IS_SAFARI()){
                img.src = svg_raw;
                img.onload = function(){ ctx.drawImage(img,0,0); };
            }else{
                var DOMURL = self.URL || self.webkitURL || self;
                var svg = new Blob([svg_raw], {type: "image/svg+xml;charset=utf-8"});
                var url = DOMURL.createObjectURL(svg);
                img.onload = function() {
                    ctx.drawImage(img, 0, 0);
                    DOMURL.revokeObjectURL(url);
                };
                img.src = url;
                // canvas[0].toDataURL();
            }
        }

        // attach event listeners to the attached files (delete);
        // this.attachFileEvents();
        
        // if(_.isUndefined(this.collection)){
        //     this.$("ul:first").remove();
        // }
        this.$el.find('.collapse-parent:first').on('click', function(e,expand){
            var $trigger = $(e.currentTarget);
            if (expand != undefined){
                if (expand == "+"){
                    $trigger.text("-").siblings('.collapse-children').text('-');

                    that.$el.find('.comment-wrapper').slideUp(); //hide
                }else{
                    $trigger.text("+").siblings('.collapse-children').text('+');
                    that.$el.find('.comment-wrapper').slideDown(); //hide
                }
            }else{
               if ($trigger.text() == "+"){
                    $trigger.text("-");
                    that.$el.find('.comment-wrapper:first').slideUp(); //hide
                }else{
                    $trigger.text("+");
                    that.$el.find('.comment-wrapper:first').slideDown(); //show
                }
            }
         });
        this.$el.find('.collapse-children:first').on('click', function(e){
            e.stopPropagation();
            var $trigger = $(e.currentTarget);
            // console.log($._data($(this)[0], 'events'));
            if ($trigger.text() == "+"){
                $trigger.text("-");
                $trigger.siblings('.collapse-parent').text('-');
                that.$el.find('.comment-wrapper:first').slideUp(); //hide
            }else{
                $trigger.text("+");
                $trigger.siblings('.collapse-parent').text('+');
                that.$el.find('.comment-wrapper:first').slideDown(); //show
            }
            var more_less_all = $trigger.siblings('li').find('.collapse-children');
            // console.log($trigger);
            more_less_all.each(function(){
                if ($(this).text() == "+"){
                    $(this).text("-");
                    $(this).siblings('.collapse-parent:first').trigger('click', '+');
                }else{
                    $(this).text("+");
                    $(this).siblings('.collapse-parent:first').trigger('click','-');
                }
            });
        });
    },

	editComment: function (e) {
        var that = this;
        // prevents the childView from notifying the parant of the event, 
        // thus avoiding an unecessary/undesired bubling chain reaction http://api.jquery.com/event.stopPropagation
        e.stopPropagation();
        var edit_comment = new App.Views.EditCommentView({model:this.model});
        edit_comment.render();
        this.$el.find('.comment:first').html(edit_comment.render());
        //seek time on the video
        this.seekTime();
	},

	destroyComment: function () {
        var that = this;

        var r = confirm('Are you sure you want to delete comment at time '+'"'+this.model.get('parentComment').cuepoint+'"? Children of this comment would  to be destroy.'),
            txt;
            
        if (r == true) {
            this.model.set({'id':that.model.get('parentComment').id}) 
            this.model.destroy();
            txt = 'Comment at time '+this.model.get('parentComment').cuepoint+' deleted successfully!';
        } else {
            txt = "Deleting comment was cancel!";
        }
        alert(txt);
	},

    replyComment: function (e) {
        var $target = $(e.currentTarget);
        
        // prevents the childView from notifying the parant of the event, 
        // thus avoiding an undesired bubling chain reaction http://api.jquery.com/event.stopPropagation
        e.stopPropagation();
        $target.hide();
        var create_comment_form = new App.Views.CreateCommentForm({
            parentComment: this.model,
            collection: this.collection,
            type: 'Reply'
        });

        this.$el.find('.comment:first').after(create_comment_form.render().el);
        if ( this.model.get('parentComment').cuepoint != null )
            this.seekTime();
        App.AppStateChanged = false;
        console.log('AppStateChanged: ', App.AppStateChanged);
    },
});

// The tree's root: a simple collection view that renders 
// a recursive tree structure for each item in the collection
App.Views.CommentsCollectionView = Backbone.Marionette.CollectionView.extend({ //collection: comments
    initialize: function(){
		// _.bindAll(this, );
		// backboneEvent.on('model:changed', this.render());
    },

    tagName: "ul",

    className: "collection-ul",

    childView: App.Views.CommentsCompositeView

});

//////////////////////////////////////////////////////////////////////////////////////////////
App.Views.CreateCommentForm = Backbone.View.extend({// parentComment: 
                                                    // collection:
	tagName: "form",                                // type:
											
	className: "create-comment",			
	
	template: _.template($('#create-comment-form-template').html()),

	events: {
		'click .Create' : 'createComment',
        'click .Reply'  : 'createComment',
        'click .cancel' : 'cancelComment',
        'keydown'       : 'keyPress'
	},

	initialize: function (args) {
        var that = this;
        //attached the extra args (player and type) to this View
        _.extend(this, _.pick(args, 'type', 'parentComment'));
		_.bindAll(this, 'render', 'createComment', 'cancelComment', 'cuepointFormatted', 'keyPress');
        setTimeout(function(){
            that.$el.find('.feedback-note:first').focus();
            // $('.feedback-note').setCursorToTextEnd();
            
        }, 100);
        var user = $('#user').text();
        var user = user.split('@');
        this.user = user[0].trim();
	},
	
	render: function () {
        var model = {},
            parend_id;
        model.type = this.type;
        model.user = this.user;
		this.$el.html(this.template(model));
		return this;
	},
	
	createComment: function () {
	    var that = this,
            parent_id,
            svg_to_save,
            svg_trim,
            div1 = $('<div/>'),
            div2 = $('<div/>');

            div1.css({
                'background'    :'yellow',
                'padding-left'  :'10px',
                'margin-top'    :'-20px',
            });
            div2.text('Adding comment...').css({
                'width':'960px',
                'padding-right':'10px',
                'margin-left'   :'auto',
                'margin-right'  :'auto'
            });
            div1.append(div2);
            $('.navbar').after(div1);

        // Export the svg drawing only if the AppStateChanged is true
        if (App.AppStateChanged){
            // var svg_code = App.Drawing.Export().export({
            //         width: '640', 
            //         height: '360'
            //     });
            var svg_code = App.Drawing.Export().svg();
            //tests to see if there is a drawing to be saved. To do this we count the number of svg nodes. if the nodes >= 2 we have a drawing svg to save 
            var svg_nodes_count = (svg_code.match(/<svg /g) || []).length;
            if (svg_nodes_count >= 2){
                svg_trim    = svg_code.substr(177);
                svg_to_save = svg_trim.substring(0, svg_trim.length-6 );
            }else{
            //if no drawing clear svg_code variable 
                console.log('there is NO svg code to save');
                svg_to_save = '';
            }
        }
        //trim both ends of the string

		// importing drawing
		// var importDrawing = SVG('import');
		// importDrawing.svg(svgExport);

		var note = this.el.feedback_note;
		if (note.value === "") {
			alert("Please include a note, or click 'Cancel'");
			return false;
		}
        
        if (this.type === 'Create')
            parent_id = null;
        else
            parent_id = this.parentComment.get('parentComment').id;
       
        this.collection.create({'parentComment':{
            'parent_id': parent_id,
            'cuepoint':  App.AppStateChanged ? that.cuepointFormatted(App.Video.currentTime()) : null,
            'cuepoint_seconds': App.AppStateChanged ? that.cuepointExactFrameLocation(App.Video.currentTime()) : null,
            'comment': note.value,
            'thumbnail': '',
            'svg': App.AppStateChanged ? svg_to_save : null,
            'video_id': App.VideoInfo.id, 
            'user': that.user
        }},{
            wait:true,
            success: function(model, response, options){
                if (that.type == 'Create'){
                    // broadcast that a cuepoint is added for the playbackControls that listening to the setActive
                    backboneEvent.trigger('cuepoint:setActive', model );
                }
                that.$el.remove();
                div1.remove();
                
                
                // activate the tooltip on the new added comment.
                $('[data-toggle="tooltip"]').tooltip();
            },
            error:  function(model, response, options){
                div2.text(response.statusText+', comment NOT saved. Please Erase all, and try again.');
                setTimeout(function(){
                    div2.slideUp('slow', function(){
                        $(this).remove();
                    });
                },10000);
            }
        });
        this.$el.next('.reply-comment').show();
        this.$el.addClass('added-comment');
	},

    keyPress: function(e) {
        var code = e.keyCode || e.which;
        if (e.keyCode == 27){
            this.cancelComment();
        }
    },

    cancelComment: function() {
        this.$el.next('.reply-comment').show();
        this.$el.remove();
    },
    // Calculate the nearest exact frame location
    cuepointExactFrameLocation: function (cuepoint) {
        var duration = App.VideoInfo.duration,
            fps = App.VideoInfo.fps,
            seconds = Math.floor(cuepoint),
            fraction = cuepoint - seconds,
            // we can round instead of floor so long as we check that the result is not greater than the duration
            frames = Math.round(fraction * fps),
            nearestFrame = (frames / fps),
            cuepoint = (seconds + nearestFrame);

        if (cuepoint > duration) {
            return cuepoint;
            // this.set({
            //     'parentComment':{'cuepoint_seconds' : duration }
            // });
        }
        // this.set({
        //     'parentComment':{'cuepoint_seconds': cuepoint_seconds}
        // });
        return cuepoint;
    },

    // Formats cuepoint in the form (MM::SS)
    cuepointFormatted: function (cuepoint) {
        // var time = this.get('parentComment').cuepoint_seconds;
        var fps = App.VideoInfo.fps,
            minutes = Math.floor(cuepoint / 60),
            seconds = Math.floor(cuepoint - (minutes * 60)),
            frames = Math.floor((cuepoint - Math.floor(cuepoint)) * fps),
            // timecode = ("0" + minutes).substr(-2) + ":" + ("0" + seconds).substr(-2) + ":" + ("0" + frames).substr(-2);
            cuepoint = ('0' + minutes).substr(-2) + ':' + ('0' + seconds).substr(-2);
        return cuepoint;
        // this.set({
        //     'parentComment':{'cuepoint': cuepoint}
        // });
        // console.log(this.toJSON());
    },

});

//////////////////////////////////////////////////////////////////////////////////////////////
App.Views.PlaybackControls = Backbone.View.extend({ //collection: comments

	tagName: "div",	

	className: "playback-controls",

	template: _.template($('#playback-controls-template').html()),

	initialize: function (args) {
		_.bindAll(this, 'render', 'videoPlaying', 'videoStoped', 'setActive','seekActive', 'toggleActive', 'addCuepoint', 'addCuepoints', 'addNote');
        
        //listens to when a cuepoint is added
		backboneEvent.on('cuepoint:setActive', this.setActive, this);
        
		this.collection.on('change', this.render);
		// this.collection.on("add", this.render);


        App.Video.on('play', this.videoPlaying);
        App.Video.on('pause', this.videoStoped);
	},
    events: {
		"click a.add-note" : "addNote",
		"click .prev": "toggleActive",
		"click .next": "toggleActive"
	},
    active_cuepoint: null,
	next_cuepoint: null,
	prev_cuepoint: null,

    render: function () {
		var model = this.collection.toJSON();
		model.active_cuepoint = this.active_cuepoint;
		model.next_cuepoint = this.next_cuepoint;
		model.prev_cuepoint = this.prev_cuepoint;
		this.$el.html(this.template(model));
		this.$nav = this.$el.find(".cuepoints");
		this.addCuepoints();
		return this;
	},

    videoPlaying: function(){
        // console.log('video playing....');
        //broadcast the event 'video:playing' in the global space to any function that is lestening or need it.
        backboneEvent.trigger('video:playing', App.Video);
        $('#new-comment-container').removeClass('frame-img-adjust');
        svg_container.clear();
    },
    videoStoped: function(){
        // console.log('video stoped.....'); 
    },

	setActive: function (cuepoint) { 
		// var cuepoints = this.model.get("cuepoints"),
		// var cuepoints = this.model,
		var that = this,
            cuepoints = this.collection,
            index;
            
		index = cuepoints.contains(cuepoint) ? cuepoints.indexOf(cuepoint) : null;

		this.active_cuepoint = cuepoint;
		this.prev_cuepoint = index > 0 ? cuepoints.at(index - 1) : null;
		this.next_cuepoint = index < (cuepoints.length - 1) ? cuepoints.at(index + 1) : null;
		this.render();
        if (this.active_cuepoint != undefined)
            this.seekActive(that.active_cuepoint);
    
        var comment_list = $('.collection-ul').children();
        comment_list.each(function(){
            $(this).removeClass('active-cuepoint');
        });
        $(comment_list[index]).addClass('active-cuepoint');
            $('[data-toggle="tooltip"]').tooltip();
	},

    seekActive: function (active_cuepoint) {
        var that = this;

        App.Video.currentTime(active_cuepoint.get('parentComment').cuepoint_seconds);
        if ( !(active_cuepoint.get('parentComment').svg == '') ){
            svg_container.clear();
            svg_container.svg( active_cuepoint.get('parentComment').svg );
            if ($('.move').hasClass('current-tool')){
                window.draggableOn();
            }
        }
	},

	toggleActive: function (event) {
		var $target;
		event.preventDefault();
		$target =$(event.currentTarget || event.srcElement);
		
		if ($target.hasClass("inactive")) {
			return false;
		}
		
		if ($target.hasClass("prev")) {
			this.setActive(this.prev_cuepoint);
		} else {
			this.setActive(this.next_cuepoint);
		}
        $('#new-comment-container').removeClass('frame-img-adjust');
		return false;
	},

	addCuepoint: function (cuepoint) {
		var li = $("<li/>"),
			a = $("<a />"),
			img = $("<img />"),
			self = this;
		img.attr("src", "img/cuepoint_icon.png").css("cursor","pointer");
        li.attr({
            "data-toggle"           :"tooltip",
            "data-original-title"   :cuepoint.get("parentComment").cuepoint,
            "data-placement"        :"bottom"
        });

		if (cuepoint === this.active_cuepoint) {
			a.addClass("active");
		}
		
		a.on("click", function () {
			self.setActive(cuepoint);
		});
        
		a.append(img);
		li.append(a);
		this.$nav.append(li);
	},

	addCuepoints: function () {
		var cuepoints = this.collection;
		this.$nav.empty();
		cuepoints.each(this.addCuepoint);
	},
	
	addNote: function () {
        var $single_feedback_container = $("#new-comment-container");

        //if theres NO(null) active_cuepoint or the cuepoints array DOES NOT contain the active_cuepoint then go to the CreateFeedBackFrom 
        // if (controls.active_cuepoint === null || cuepoints.contains(controls.active_cuepoint) === false) {
        // if (controls.active_cuepoint === null || comments.contains(controls.active_cuepoint) === false) {
        var single_display = new App.Views.CreateCommentForm({
            parentComment: null,
            collection: this.collection,
            type: "Create"
        });
            
        // }else{
            // single_display = new App.Views.SingleFeedback({
            //     model: controls.active_cuepoint,
            //     player: player
            // });
            // backboneEvent.trigger('force:loadSingleCanvas',  $single_feedback_container.html(single_display.render().el));
        // }
        $single_feedback_container.html(single_display.render().el);
	
		// var cuepoints = this.model.get("cuepoints");
		// var cuepoint = {'cuepoint': this.player.currentTime()};
		// // cuepoints.add(cuepoint);
		// this.setActive(cuepoint);


		// console.log('addNote called');
		// this.active_cuepoint = null;
		// this.trigger('changeActive');
	}
});
