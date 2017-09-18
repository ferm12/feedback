App.Models.Client = Backbone.Model.extend({
	validate: function (attrs) {
		if ( !attrs.username ){
            alert('An username is required!');
            $('#username').parent().addClass('has-error');
			return 'An username is required';
		}
		// else if (!attrs.email){
            // alert('An email is required!');
            // $('#email').parent().addClass('has-error');
		// 	return 'An email is required!';
		// }
	}

});
