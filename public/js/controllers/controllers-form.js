(function () {
    "use strict";
    var to, progress_file, pid_file, frame_count,
        progress_bar = $("#conversion-progress-bar");

    function getConversionProgress(progress_file, pid_file, total_frames) {
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
                // console.log('Server Response ', serverResponse);
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
                    // alert("File Conversion Done!");
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
                // console.log("There was an error connecting with the server: ", errorThrown);
            }
        });
    }

    function startConversion(video_id, video_type) {
        $.ajax({
            url:'startconversion',
            type: 'GET',
            data: {
                video_id: video_id,
                video_type: video_type
            },
            // dataType: 'json',
            success: function(response, status, xhr){
                console.log(response);
                progress_file = response.progress_file;
                pid_file = response.pid_file;
                frame_count = response.total_frames;
                getConversionProgress(response.progress_file, response.pid_file, response.total_frames);
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log("There was an error connecting with the server: ", errorThrown);
            }
        });
    }

    $('form').on('submit', function(e) {
        e.preventDefault(); //important 

        $('#upload-form-wrapper').hide();
        $('#upload-wrapper').show();

        $(this).ajaxSubmit({
            resetForm: true,
            dataType: 'json',
            uploadProgress: function(event, position, total, percent) {
                $('progress').attr('value', percent);
                var percentComplete = percent + '%';
            },
            success: function(response, status, xhr){
                console.log("videouplad response",response);
                console.log('ajaxFrom upload response status: ', status);
                
                startConversion(response.id, response.video_type);
                $('#upload-wrapper').hide();
                $('#conversion-wrapper').show();
                $('#video_title').html(response.video_title);
                $('#video_description').html(response.description);
                $('#video_url').html(function(){
                    return "<a href='"+response.url+"'>"+response.url+"</a>";
                });
            },
            failure: function(){alert('Failed to load image please try again!')},
            // complete is call after success and/or error with the success or error textStatus
            complete: function(xhr, status) {
                console.log('ajaxFrom complete method: ', status);
            },
            error: function (xhr, status, error){
                console.log('ajaxform error method: ', status);
            }
        });
    });


    // function uploadVideo(){
		//Prepares(DOES NOT submit the form, it waits for user to submit) a form to be submitted via AJAX by adding all of the necessary event listeners. 
		// $('form').ajaxForm({
		// 	// type:'POST',
		// 	// url: videoupload,
		// 	// beforeSubmit: beforeSubmit,
		// 	resetForm: true,
		// 	uploadProgress: function(event, position, total, percent) {
		// 		var percentComplete = percent + '%';
		// 		console.log('percentage completed: ', percentComplete);
		// 	},
		// 	success: function(data, status, xhr) {
                // console.log('ajaxFrom success method: ', status);
		// 	},
		// 	failure: function(){alert('Failed to load image please try again!')},
		// 	complete: function(xhr, status) {
                // console.log('ajaxFrom complete method: ', status);
		// 	},
            // error: function (xhr, status, error){
            //     console.log('ajaxform error method: ', status);
            // }
		// }); 
    // }


    




// http://www.dave-bond.com/blog/2010/01/JQuery-ajax-progress-HMTL5/
        // $.ajax({
        //   xhr: function()
        //   {
        //     var xhr = new window.XMLHttpRequest();
        //     //Upload progress
        //     xhr.upload.addEventListener("progress", function(evt){
        //       if (evt.lengthComputable) {
        //         var percentComplete = evt.loaded / evt.total;
        //         //Do something with upload progress
        //         console.log(percentComplete);
        //       }
        //     }, false);
        //     //Download progress
        //     xhr.addEventListener("progress", function(evt){
        //       if (evt.lengthComputable) {
        //         var percentComplete = evt.loaded / evt.total;
        //         //Do something with download progress
        //         console.log(percentComplete);
        //       }
        //     }, false);
        //     return xhr;
        //   },
        //   type: 'POST',
        //   url: "/",
        //   data: {},
        //   success: function(data){
        //     //Do something success-ish
        //   }
        // });
    
    // function saveVideoToDb() {
    //     $.ajax({
    //         url:'videos',
    //         type: 'POST',
    //         data: {
    //             video_id:       parseInt($('#video_id').val()),
    //             video_title:    $('#video_title').val(),
    //             duration:       $('#duration').val(),
    //             fps:            $('#fps').val(),
    //             width:          $('#width').val(),
    //             height:         $('#height').val(),
    //             description:    $('#description').val(),
    //             video_path:     $('#video_path').val(),
    //             video_srcs:     $('#video_srcs').val(),
    //             url:            $('#url').val(),
    //             project_id:     $('#project_id').val(),
    //             project_name:   $('#project_name').val()
    //         },
    //         success: function(serverResponse){
    //             console.log('Server Response ', serverResponse);
    //         },
    //         error: function(jqXHR, textStatus, errorThrown){
    //             console.log("There was an error connecting with the server: ", errorThrown);
    //         },
    //         complete: function(xhr, textStatus){
    //             console.log('video saving to the database status: ', textStatus);
    //         }
    //     });
    // }

    // if (progress_file !== "" && pid_file !== ""){
    //     // getConversionProgress();
    //     // saveVideoToDb();
    // }
/*
success: function(response){
    console.log(response);
    $target.siblings('.category-title').text(response);
    
},
error: function(jqXHR, textStatus, errorThrown){
    console.log(errorThrown);
}
//complete is call after success and error with the success or error textStatus
complete: function(xhr, textStatus){
    console.log(textStatus);
},

*/
/*
    new App.Router;
    Backbone.history.start();

    App.Videos = new App.Collections.Videos;
    // App.Videos.fetch().then(function(){
        new App.Views.App({collection: App.Videos});
    // });
*/
}());


