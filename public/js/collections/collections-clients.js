App.Collections.Clients = Backbone.Collection.extend({
    
	model: App.Models.Client,

	url: 'clients',
    
    methodUrl:  function(method){
        if(method == "read")
            return "clients/0/video/"+$('#video_id').val();
        return 'clients';
    },

    sync: function (method, collection, options) {
        options.url = collection.methodUrl(method.toLowerCase());
        return Backbone.sync.apply(this, arguments)
    }
});
