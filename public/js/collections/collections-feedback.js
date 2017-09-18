// naming convention 
// ids & classes	: bla-bla-bla
// functions		: blaBlaBla
// variables		: bla_bla_bla
// options = options || {};
//
// var TreeNodeCollection = Backbone.Collection.extend({
App.Collections.Comments = Backbone.Collection.extend({

    model: App.Models.Comment,

    url:'comments',

    methodUrl:  function(method, options){
        if(method == "read")
            return "comments/0/video/"+App.VideoInfo.id;
        return 'comments'
    },
    // backbone core sync function is overwritting to fit our needs(expecially the 'method' request)
    sync: function (method, collection, options) {
        options.url = collection.methodUrl(method.toLowerCase());
        return Backbone.sync.apply(this, arguments)
    },

    comparator: function (model) {
		return model.get("parentComment").cuepoint_seconds;
	}
});
