// Global App View
App.Views.App = Backbone.View.extend({
	initialize:	function () {

		backboneEvent.on('video:edit', this.editVideo, this);

	    new App.Views.AddVideo({collection: this.collection});

		var allVideosView = new App.Views.Videos({collection: this.collection}).render();

		$('#allVideos').append(allVideosView.el);
	},

	editVideo: function (video) {
		console.log(video);
		//create a new EditContacView
		var editVideoView = new App.Views.EditVideo({model: video});
		// console.log(editVideoView.el);
		// //bind the model
		// //append the form to the DOM
		$('#editVideo').html(editVideoView.el);
	}
	
});

// Add Video View
App.Views.AddVideo = Backbone.View.extend({
	initialize: function () {
		backboneEvent.on('video:conversionDone', this.addVideo, this);

        this.video_id   = $('#video_id');
		this.video_title = $('#video_title');
		/* this.version    = $('#version'); */
		this.duration   = $('#duration');
		this.fps        = $('#fps');
        this.width      = $('#width');
        this.height     = $('#height');
        this.description= $('#description');
        this.video_path = $('#video_path');
        this.video_srcs = $('#video_srcs');
        this.url        = $('#url');
        this.project_id = $('#project_id');
        this.project_name=$('#project_name');
        // this.users      = $('#users');
	},

    el: '#addVideo',
	// events: {
	// 	'submit': 'addVideo'
	// },

	// addVideo: function (e) {
	addVideo: function () {
		// e.preventDefault();
		// equivalent to instantiating a model with a hash of attributes,
		// saving the model to the server, and adding the model to the set 
		this.collection.create({
            video_id:       parseInt(this.video_id.val()),
			video_title:    this.video_title.val(),
			duration:       this.duration.val(),
			fps:            this.fps.val(),
            width:          this.width.val(),
            height:         this.height.val(),
            description:    this.description.val(),
            video_path:     this.video_path.val(),
            video_srcs:     this.video_srcs.val(),
            url:            this.url.val(),
            project_id:     this.project_id.val(),
            project_name:   this.project_name.val()
            // users:          this.users.val()
		},{
			// wait: true,
			success: function (model, response, options) {
				console.log('Video Saved Successfully');
			},
			// error: function (err) {
            error: function (model, response, options){
				console.log('There was an error saving to the database: ', response.error());
				//this error msg for dev only	
				// alert('There was an error. See console for details')
			}
		});	

		// this.clearForm();
	},

	clearForm: function () {
		this.first_name.val('');
		this.last_name.val('');
		this.description.val('')
		this.email_address.val('');
	}
});

//Edit video View
App.Views.EditVideo = Backbone.View.extend({
	template: template('edit-videos-template'),

	initialize: function () {
		this.render();
		//lets cache this 
		this.form = this.$('form');
		this.first_name = this.form.find('#edit_first_name');
		this.last_name = this.form.find('#edit_last_name');
		this.description= this.form.find('#edit_description');
		this.email_address = this.form.find('#edit_email_address');
	},

	events: {
		'submit form': 'submit',
		'click button.cancel': 'cancel' 
	},

	submit: function (e) {
		e.preventDefault();

		console.log(this.first_name.val());
		console.log(this.model);

		// I could've used this.model.set({...}); but,
		// the save method sets it, fires a change event, and syncs it to the server
		this.model.save({
			first_name: this.first_name.val(),
			last_name: this.last_name.val(),
			description: this.description.val(),
			email_address: this.email_address.val()
		});


		//on submition remove the form
		this.remove();
	},

	cancel: function () {
		this.remove();
	},

	render: function () {
		var html = this.template(this.model.toJSON());

		this.$el.html(html);
		return this;
	}

});

//All Videos View
App.Views.Videos = Backbone.View.extend({ //collection
	tagName: 'tbody',

	initialize: function () {
		this.collection.on('add', this.addOne, this);

	},

	render: function () {
		this.collection.each( this.addOne, this);
		return this;
	},

	addOne: function (video) {
		var videoView = new App.Views.Video({ model:video });
		// console.log(videoView.render().el );
		this.$el.append(videoView.render().el);
	}
});

// Single Video View
App.Views.Video = Backbone.View.extend({
	tagName: 'tr',

	template: template('all-videos-template'),

	initialize: function () {
		this.model.on('destroy', this.unrender, this);
		//when any of the attributes change call render
		this.model.on('change', this.render, this); 
	},

	events: {
		'click a.delete': 'deleteVideo',
		'click a.edit': 'editVideo'
	},

	editVideo:function () {
		// broadcast costum video:edit any function to 
		backboneEvent.trigger('video:edit', this.model)
	},

	deleteVideo: function () {
        var r = confirm("Are you sure you want to delete "+this.model.get('video_title')+ " video and its entire assets?"),
            txt;

        if (r == true) {
            this.model.destroy()
            txt = "Video deleted successfully!";
        } else {
            txt = "Deleting Video was cancel!";
        }

        alert(txt);
	},

	render: function () {
        // console.log(this.model.toJSON());
		this.$el.html( this.template(this.model.toJSON()) );
		return this;
	},

	unrender: function () {
		this.remove(); //this.$el.remove()
	}
});

