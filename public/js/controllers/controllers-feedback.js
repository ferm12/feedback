// $, Backbone, _, _V_, window 
//jslint browser: true, devel: true 

// naming convention 
// ids & classes	: bla-bla-bla
// functions		: blaBlaBla
// variables		: bla_bla_bla

(function () { 
    "use strict";

    var to, video, progress_file, pid_file, total_frames, progress_bar, player;

    function getConversionProgress(progress_file, pid_file, total_frames)
    {
        $.ajax({
            url:'videoconversion',
            type: 'GET',
            data: {
                progress: progress_file,
                pid: pid_file,
                frames: total_frames 
            },
            dataType: 'json',
            success: function(serverResponse){
                if (serverResponse.hasOwnProperty("errors")) {
                    if (serverResponse.fatal) {
                        console.log(serverResponse.errors);
                    } else {
                        console.log("Progress unknown");
                        to = setTimeout(getConversionProgress.bind(null, progress_file, pid_file, total_frames), 10);
                    }
                    return false;
                }
                if (serverResponse.finished) {
                    console.log("File Conversion Done!");
                    progress_bar.removeClass('progress-bar-striped active');
		            // backboneEvent.trigger('video:conversionDone');
                    
                } else {
                    to = setTimeout(getConversionProgress.bind(null, progress_file, pid_file, total_frames), 10);
                }
                progress_bar.css("width", (serverResponse.percent_complete).toFixed(1)+"%" );
                progress_bar.html((serverResponse.percent_complete).toFixed(1)+"%" ) ;
            },
            error: function(jqXHR, textStatus, errorThrown){
                progress_bar.removeClass('active')
                progress_bar.addClass('progress-bar-danger');
                alert(errorThrown+". Please try again.");
            }
        });
    }

    function loadApp()
    {
		var controls,
            comments = new App.Collections.Comments;

        $.when(comments.fetch()).then( function(){

            function addControls() {
                var container = $("#playback-controls-container");
                controls = new App.Views.PlaybackControls({ collection: comments });
                // make the last cuepoint in the array as the active cuepoint  
                // var last_cuepoint = model_comment.get('cuepoints').last();
                var last_cuepoint = comments.last();

                //check if the page is generatepdf.php, if it is run App.Controllers.Pdf()
                var generatepdf = window.location.pathname;
                // if url does not contain viewpdf run this code
                if( generatepdf.indexOf('viewpdf') == -1 ){
                    drawing_svg_script(); 
                    controls.setActive(last_cuepoint);
                }

                container.append(controls.render().el);
            }

            function addCommentsCollectionView() {
                $("#comments-tree").append('<a id="collapse-all" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Collapse all">+</a><div class="clear"><div>');
                var CommentsCollectionView = new App.Views.CommentsCollectionView({ collection: comments });
                CommentsCollectionView.render();

                $("#comments-tree").append(CommentsCollectionView.el);
            }
            
            function addViews() {
                addControls();
                addCommentsCollectionView();
                //tooltip
                $('[data-toggle="tooltip"]').tooltip();
                // collapse all listener
                $('#collapse-all').on('click',function(e){
                    if ($(this).text() == "+"){
                        $(this).text("-");
                        $(this).siblings('ul').children('li').find('.collapse-parent').trigger('click','+');
                    }else{
                        $(this).text("+");
                        $(this).siblings('.collapse-parent:first').trigger('click','-');
                        $(this).siblings('ul').children('li').find('.collapse-parent').trigger('click','-');
                    }
                });
                $('#video-container, #svg-container').on('click', function(){
                    App.AppStateChanged = true;
                    // console.log('AppStateChanged', App.AppStateChanged);
                });
            }

            function  attachFileEvents(){
                // Deleting individual attachments
                $('.delete-attachment').on('click', function(e){
                    // console.log($._data($(this)[0], 'events'));
                    
                    // e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    var $target = $(e.currentTarget);
                    var classes = $target.attr('class');
                    classes  = classes.split(" ");

                    var $target_to_delete = classes[0];
                    var index = $target_to_delete.lastIndexOf('_');
                    $target_to_delete = $target_to_delete.substr(0,index)+'.'+$target_to_delete.substr(index+1);
                    var r = confirm('Are you sure you want to delete '+$target_to_delete),
                        txt;
                        
                    if (r == true) {
                        $.ajax({
                            url: window.location.origin +'/detachfile' ,
                            type: 'POST',
                            data: {
                                file: $target_to_delete
                            },
                            success: function(data, textStatus, jqXHR){
                                console.log('javascript ajax SUCCESS', textStatus);
                                $('.'+classes[0]).remove();

                            },
                            error: function(jqXHR, textStatus, errorThrown){
                                console.log('javascript ajax ERROR', errorThrown);
                            },
                            // complete is call after success or error is called
                            complete: function (jqXHR, textStatus){
                                console.log('javascript ajax COMPLETE', textStatus);
                            },
                        });
                        txt = $target_to_delete+' was successfully deleted';
                        
                    } else {
                        txt = "Deleting was cancel!";
                    }
                    alert(txt);
                });
            }

            addViews()
            window.domready = true;
            attachFileEvents();
            // attach
            backboneEvent.on('attachment:added', attachFileEvents);
        }); //end of $.when
	}
 
    //if element with id=conversion-progress-bar exists then display the progress bar, otherwise display the video.
    if ( document.getElementById('conversion-progress-bar') !== null ){

        progress_bar = $('#conversion-progress-bar');
        progress_file = $('#progress_file').val();
        pid_file = $('#pid_file').val();
        total_frames = $('#total_frames').val();
        getConversionProgress(progress_file, pid_file, total_frames); 

    }else{

        video = document.getElementsByTagName('video');
	    player = videojs(video[0]);
        player.ready(function(){
            var videoInfo = new App.Models.VideoInfo;
            // fech video from the database.
            $.when( videoInfo.fetch() ).done( function(){
                App.VideoInfo = videoInfo.toJSON();
                loadApp();
                App.Video = player;
            });
        });
    }


})();
