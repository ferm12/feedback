(function () {
    "use strict";

    // new App.Router;
    Backbone.history.start();

    App.Videos = new App.Collections.Videos;
    
    App.Videos.fetch().then(function(){
        new App.Views.App({collection: App.Videos});

        $('.video-active').change(function(){
            var target = $(this), active;
            (target.is(":checked")) ? active = 1 : active = 0;

            $.ajax({
                url:'videos/'+target.attr('id'),
                type: 'PUT',
                data: {
                    active: active
                },
                success: function(data, textStatus, jqXHR){
                    console.log('updating video:', target.attr('id'), textStatus);
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log('updating video:', target.attr('id'), errorThrown);
                }
            });
        });

        $('.delete').change(function(){

            console.log('You clicked delete');
            // $.ajax({
            //     url:'videos/'+target.attr('id'),
            //     type: 'PUT',
            //     data: {
            //         active: active
            //     },
            //     success: function(data, textStatus, jqXHR){
            //         console.log('updating video:', target.attr('id'), textStatus);
            //     },
            //     error: function(jqXHR, textStatus, errorThrown){
            //         console.log('updating video:', target.attr('id'), errorThrown);
            //     }
            // });
        });
    });

}());


