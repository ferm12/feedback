@extends('master')

@section('content')
    
    @include('navbar')

    <link rel="stylesheet" href="css/feedback.css" type="text/css">

    <div id="page-wrapper">
        <div id="upload-form-wrapper">
            <h1 id="">Upload Video Form</h1>
            <form method="post" action="videoupload" accept-charset="UTF-8" enctype="multipart/form-data">
                <!-- <div class="form&#45;group"> -->
                <!--     <label for="video&#45;title">Video Title:</label> -->
                <!--     <input type="text" class="form&#45;control" id="video&#45;title" name="video&#45;title" autofocus="autofocus" placeholder="Enter Video Title"> -->
                <!-- </div> -->

                <div class="form-group">
                    <label for="video-file">Video File</label>
                    <input type="file" id="video-file" name="video-file">
                    <p class="help-block">Convert any movie to html5 file formats(.mp4 and .webm)</p>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea name="description" class="form-control" placeholder="optional"/></textarea>
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>
        </div>
        <div id='upload-wrapper'>
            <h1>Upload file progress</h1>
            <progress id="upload-progress-bar" max="100"></progress>
        </div>

        <div id="conversion-wrapper">
            <h1>Conversion In Progress</h1>
            <p>Your video has been uploaded and is currently being converted to HTML5 video formats (.mp4 and .webm).</p>

            <div class="progress">
                <div id="conversion-progress-bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <div class="boostrap-table">
                <table id="all-videos" class="table">
                    <thead>
                        <tr>
                            <td><b>Video Title</b></td>
                            <td><b>Description</b></td>
                            <td><b>Url</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="bb" id="video_title"></td>
                            <td class="bb" id="video_description"></td>
                            <td class="bb" id="video_url"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><!-- end #conversion-wrapper -->    
    </div> <!-- end #page&#45;wrapper -->

    <script src="js/libs/jquery/jquery.form.js"></script>  
    <script src="js/controllers/controllers-form.js"></script>  

@stop
