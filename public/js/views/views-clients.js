// Global App View
//
App.Views.App = Backbone.View.extend({
	initialize:	function () {

		backboneEvent.on('client:edit', this.editClient, this);
		backboneEvent.on('client:add', this.addClient, this);
        

		var addClientView = new App.Views.AddClient({collection: this.collection}); 			
		var allClientsView = new App.Views.Clients({collection: this.collection}).render();

		$('#allClients').append(allClientsView.el);
	},

	editClient: function (args) {
		console.log(args[0]);
		//create a new EditContacView
		var editClientView = new App.Views.EditClient({model: args[0], $tr: args[1]});
		// console.log(editClientView.el);
		// //bind the model
		// //append the form to the DOM
		$('#editClient').html(editClientView.el).find('input:first').focus();

	},
    addClient: function () {
        console.log('add client called');
		//create a new addContacView
		var addClientView = new App.Views.AddClient({collection: this.collection}); 			

		// console.log(editClientView.el);
		// //bind the model
		// //append the form to the DOM
		$('#addClient').html(addClientView.el).find('input:first').focus();
	}

	
});

// Add Client View
App.Views.AddClient = Backbone.View.extend({
	// el: '#add-client-form',
    template: template('addClientTemplate'),

	initialize: function () {
        this.render();
        this.form                   = this.$('form');
		this.username               = this.form.find('#username');
		this.email                  = this.form.find('#email');
		this.password_unencrypted   = this.form.find('#password_unencrypted');
        this.video_id               = $('#video_id');
	},

	events: {
		'submit'                :'addClient',
        'click button.cancel'   :'cancel',
        'keydown'               :'keyPress'
	},

	addClient: function (e) {
        var that = this;
		e.preventDefault();

        var password_unencrypted = this.password_unencrypted.val();
        if (password_unencrypted == '')
            password_unencrypted = 'feedback';
		// equivalent to instantiating a model with a hash of attributes,
		// saving the model to the server, and adding the model to the set 
		this.collection.create({
			username:               this.username.val(),
			email:                  this.email.val(),
			password_unencrypted:   password_unencrypted,
            video_id:               this.video_id.val()
		},{
            wait: true,
            success: function (model, response, options) {
                console.log(that);
                $('tbody tr:last').addClass('added_user_smg');
                // $('tbody tr:last').animate({ backgroundColor: '#FFFF00' }, "slow" );
                setTimeout(function(){
                    $('tbody tr:last').removeClass('added_user_smg');

                    // $('tbody tr:last').animate({ backgroundColor: '#FFFFFF' }, "slow" );
                },4000);
                that.clearForm();
                $('#username').parent().removeClass('has-error')
                $('#email').parent().removeClass('has-error');
                // $('#client').slideToggle('slow');
		        that.remove();
                $('#add-client-btn').show();
                
                // 
            },
            error: function (model, response, options) {
                console.log(response.error());
            }
        });	
	},

	clearForm: function () {
		this.username.val('');
		this.email.val('');
		this.password_unencrypted.val('');
	},

    render: function () {
		var html = this.template();
		this.$el.html(html);
		return this;
    },

    cancel: function () {
		this.remove()
        $('#add-client-btn').show();
        // $('#add-user').toggle();
        // $('#client').slideToggle('slow');

	},

    keyPress: function(e) {
        var code = e.keyCode || e.which;
        if (e.keyCode == 27){
            this.cancel();
        }
    }
});

//Edit client View
App.Views.EditClient = Backbone.View.extend({
	template: template('editClientTemplate'),

	initialize: function (args) {
        // forces to pass $tr as an argument to this view
        _.extend(this, _.pick(args, '$tr'));
		this.render();
		//lets cache this 
		this.form                   = this.$('form');
		this.username               = this.form.find('#edit_username');
		this.email                  = this.form.find('#edit_email');
		this.password_unencrypted   = this.form.find('#edit_password_unencrypted');
	},

	events: {
		'submit form'           :'submit',
		'click button.cancel'   :'cancel',
        'keydown'               :'keyPress'
	},

	submit: function (e) {
        var that = this;
		e.preventDefault();


		// this.model.save({first_name: this.first_name});
		//grab the related model
		// this.model.set({
		// 	first_name: first_name
		// });
		// change event going to sync with server when it is saved 
		that.model.save({
		// this.model.set({
			username:               this.username.val(),
			email:                  this.email.val(),
			password_unencrypted:   this.password_unencrypted.val()
		});
        that.$tr.addClass('added_user_smg');
                // $('tbody tr:last').animate({ backgroundColor: '#FFFF00' }, "slow" );
        setTimeout(function(){
            that.$tr.removeClass('added_user_smg');
        },4000);


		//update its attributes
		//sync with the server

		//on submition remove the form
		this.remove();
	},

	cancel: function () {
		this.remove();
	},

    keyPress: function(e) {
        var code = e.keyCode || e.which;
        if (e.keyCode == 27){
            this.cancel();
        }
    },

	render: function () {
		var html = this.template(this.model.toJSON());

		this.$el.html(html);
		return this;
	}
});

//All Clients View
App.Views.Clients = Backbone.View.extend({
	tagName: 'tbody',

	initialize: function () {
		this.collection.on('add', this.addOne, this);
	},

	render: function () {
		this.collection.each( this.addOne, this);
		return this;
	},

	addOne: function (client) {
		var clientView = new App.Views.Client({ model:client });
		this.$el.append(clientView.render().el);
	}
});

// Single Client View
App.Views.Client = Backbone.View.extend({
	tagName: 'tr',

	template: template('allClientsTemplate'),

	initialize: function () {
		this.model.on('destroy', this.unrender, this);
		//when any of the attributes change call render
		this.model.on('change', this.render, this); 
	},

	events: {
		'click a.delete': 'deleteClient',
		'click a.edit': 'editClient'
	},

	editClient:function (e) {
        var $tr = $(e.target).parent().parent();
		// broadcast costum client:edit any function to 
		backboneEvent.trigger('client:edit',[this.model, $tr]);
	},

	deleteClient: function () {
        var that = this;
        console.log(this.model);
        var r = confirm('Are you sure you want to delete user '+ this.model.get('username')+'?'),
            txt;

        if (r == true) {
    	    this.model.destroy();
            txt = "User " + this.model.get('username') + ' was successfully deleted!';
        } else {
            txt = "Deleting user " +this.model.get('username')+ " was cancel!";
        }
        alert(txt);
	},

	render: function () {
		this.$el.html( this.template(this.model.toJSON()) );
		return this;
	},

	unrender: function () {
		this.remove(); //this.$el.remove()
	}
});
