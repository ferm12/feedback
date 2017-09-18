// naming convention 
// ids & classes	: bla-bla-bla
// functions		: blaBlaBla
// variables		: bla_bla_bla
/*jslint browser: true, devel: true */

App.Models.VideoInfo = Backbone.Model.extend({

    url: 'videos/'+$('#video_id').val()

});

/*
 * I don't want to keep the childrenComments in a given model's
 * "attributes". I prefer to keep the childrenComments objects as direct 
 * properties on the model object instead of in the attributes. 
 * So, after I create the collection from the child data, i remove
 * the child data from the attributes of the model. this keeps me 
 * from having duplicate data to deal with and keeps my objects cleaner.
 * Calling model.set or model.unset does not change any property on the
 * model object directly. It changes properties stored in model.attributes. 
 * when I call model.get("childrenComments") it returns model.attributes.childrenComments. 
 * When i call model.unset("childrenComments"), it removes model.attribute.childrenComments. 
 * It never directly reads, writes or modifiesmodel.childrenComments. I am manually 
 * assigning a value to model.childrenCommnents.
 */
App.Models.Comment = Backbone.Model.extend({

    initialize: function(){
        var childrenComments = this.get("childrenComments");
        if (childrenComments){
            this.childrenComments = new App.Collections.Comments(childrenComments);
            this.unset("childrenComments");  
        }
    },

});
