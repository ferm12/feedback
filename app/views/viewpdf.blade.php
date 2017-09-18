@extends('master')
@section('content')
    @include('navbar')
    <link href="css/feedback.css" rel="stylesheet" type="text/css">
    <link href="css/viewpdf.css" rel="stylesheet" type="text/css">

    <?php $video = DB::table('videos')->where('video_title', $_GET["video"] )->first(); ?>
    <div id="page-wrapper">
        <div id='pdf-content'>
            <div id="page-wrapper">
            <!-- <h1>Video Feedback Prototype</h1> -->
            <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>"><h1 id="video_title"><?php echo $_GET['video']; ?></h1></a>
            <input type="hidden" id="video_id" value="<?php echo $video->id; ?>"/>
            <video id="<?php echo $_GET['video']?>" class="vjs-quicktime-skin" controls preload="auto" width="640" height="360" style="display:none">
                <source src="video_review/<?php echo $_GET["video"]."/".$_GET["video"].'.mp4'; ?>" type='video/mp4'>
                <source src="video_review/<?php echo $_GET["video"]."/".$_GET["video"].'.webm'; ?>" type='video/webm'>
            </video>
            <div id="comments-tree"></div>
      <!-- <center style="z&#45;index:20;margin:&#45;38px 0px 0px 0px;z&#45;index:20;position:relative;"><a href="javascript:void();" id="print&#45;pdf&#45;button" class="btn btn&#45;primary btn&#45;large" >Print/Save PDF</a></center> -->
            <br/><center><a href="javascript:void(0);" id="print-pdf-button" class="btn btn-info" >Print/Save PDF</a></center><br/>
        </div>
    </div><!-- end #page-wrapper -->
                        <!-- <iframe id="preview&#45;pane" type="application/pdf" width="50%" height="650" frameborder="0"></iframe> -->
    <!-- <div id="preview&#45;container"> -->

    <!--     <div id="inner&#45;preview">  -->
    <!--         <iframe id="preview" name="preview"></iframe> -->
    <!--     </div> -->

    <!--     <center style="z&#45;index:20;margin:&#45;38px 0px 0px 0px;z&#45;index:20;position:relative;"><a href="javascript:void();" id="print&#45;pdf&#45;button" class="btn btn&#45;primary btn&#45;large" >Print/Save PDF</a></center> -->

    <!-- </div> <!&#45;&#45; end #preview&#45;container &#45;&#45;> -->
    <!-- feedback templates --> <!--playback controls-->
    <script id="playback-controls-template" type="text/template">
        <nav class="cuepoint-nav">
            <ul>
                <li>
                    <a href="#prev" class="prev<% if (prev_cuepoint === null) { %> inactive<% } %>">&laquo;</a>
                </li>
                <li class="cuepoint-icons">
                    <ul class="cuepoints"></ul>
                </li>
                <li>
                    <a href="#next" class="next<% if (next_cuepoint === null) { %> inactive<% } %>">&raquo;</a>
                </li>
            </ul>
        </nav>
        <p>
            <a href="#add-note" class="add-note btn btn-info">New Note</a>
        </p>
    </script>

    <script id="comment-template" type="text/template">

        <%if ( parentComment.cuepoint == null ) { %>
            <span class="comment-user">
                <%= parentComment.user %>
                <span data-toggle="tooltip" data-placement="bottom" title="<%=parentComment.created_at%>">
                    <%  var date = parentComment.created_at;
                            date = date.split(" ");
                            date = date[0].split('-');    
                            date = new Date(date[0],date[1]-1,date[2]); 
                            date = date.toDateString();                     
                            date = date.split(" ");
                    %>
                    <%=date[1]%>&nbsp;<%=date[2]%>
                </span>
                <a href="#edit-comment" class="edit-comment">edit</a>
                <a href="#destroy-comment" class="destroy-comment"> delete</a>
                <a href="#attach-file-link" class="attach-file-link">attach file</a>
            </span>
        <%}else{%>
            <h2 class="timecode">
                <a href="#<%= parentComment.cuepoint %>" class="time-link"><%= parentComment.cuepoint %></a>
            </h2>
        <%}%>

        <a class="collapse-children" href="#collapse-children" data-toggle="tooltip" data-placement="top" title="Collapse children">+</a>
        <a class="collapse-parent" href="#collpase-parent" data-toggle="tooltip" data-placement="top" title="Collapse parent">+</a>
        <div class="comment-wrapper">
            <% if (parentComment.cuepoint != null ) { %>
                <a href="#edit-image" class="edit-image">
                    <div class='imgs-container'>
                        <img class="comment-thumbnail" src="video_review/<%= parentComment.thumbnail %>"/>
                        <canvas class="comment-canvas"></canvas>
                    </div>
                </a>
            <%}%>
            <div class="attachments">
                <% if ( parentComment.attachments != null && parentComment.attachments != "") {%>
                    <h5>Attachments</h5>
                    <% var attachments = parentComment.attachments.split(',')%>
                    <% for (var i = 0, j = attachments.length; i < j; i += 1) {%>
                        <% var type = attachments[i].substring(attachments[i].lastIndexOf('(')) %> 
                        <% type = type.substring(1,type.length-1) %>
                        <% var url = attachments[i].substring(0, attachments[i].lastIndexOf('('))%>
                        <% var file_name = url.split("/")%>
                        <% var file_name_period = file_name.pop()%>
                        <% var file_name_underscore = file_name_period.replace(/\./, "_")%>

                        <% if (type == "image") { %>
                            <figure>
                                <img src="/img/image-icon.png"/><figcaption><%=file_name_period%></figcaption>
                            </figure>
                        <%}%>
                        <% if (type == "video" ) { %>
                            <figure>
                                <img src="/img/video-icon.png"/><figcaption><%=file_name_period%></figcaption>
                            </figure>
                        <%}%>
                        <% if (type == "text" ) { %>
                            <figure>
                                <img src="/img/text-icon.png"/><figcaption><%=file_name_period%></figcaption>
                            </figure>
                        <%}%>
                        <% if (type == "pdf" ) { %>
                            <figure>
                                <img src="/img/pdf-icon.png"/><figcaption><%=file_name_period%></figcaption>
                            </figure>
                        <%}%>
                        
                    <%}%>
                <%}%>
            </div>
            <%if ( parentComment.cuepoint != null ) {%>
                <span class="comment-user">
                    <%= parentComment.user %>
                    <span data-toggle="tooltip" data-placement="bottom" title="<%=parentComment.created_at%>">
                        <%  var date = parentComment.created_at;
                                date = date.split(" ");
                                date = date[0].split('-');    
                                date = new Date(date[0],date[1]-1,date[2]); 
                                date = date.toDateString();                     
                                date = date.split(" ");
                        %>
                        <%=date[1]%>&nbsp;<%=date[2]%>
                    </span>
                    <a href="#edit-comment" class="edit-comment">edit</a>
                    <a href="#destroy-comment" class="destroy-comment"> delete</a>
                    <a href="#attache-file-link" class="attach-file-link">attach file</a>
                </span>
            <%}%>
            <p class="comment"><%= parentComment.comment %></p>
            <a href="#reply-comment" class="reply-comment">Reply</a>

            <ul></ul>
        </div>
    </script>
<!--
    <script id="comment-template" type="text/template">
        <div class="">
            <a href="#edit-image" class="edit-image">
                <div class='imgs-container'>
                <img class="comment-thumbnail" src="video_review/<%= parentComment.thumbnail %>"/>
                <canvas class="comment-canvas"></canvas>
                </div>
            </a>
            <div class="comment-content">
                <h2 class="timecode"><a href="#<%= parentComment.cuepoint %>" class="time-link"><%= parentComment.cuepoint %></a></h2>
                <p class="comment-user">
                    <%= parentComment.user %> said on
                    <span><%= parentComment.created_at  %></span>
                </p>
                <p class="comment"><%= parentComment.comment %></p>
            </div>
        </div>
        <ul></ul>
    </script>
-->
    <!--create feedback--> <!-- post -->
    <script id="create-comment-form-template" type="text/template" >
        <legend><%=user%>:</legend>
        <textarea class="feedback-note form-control" name="feedback_note"></textarea>
        <input type="button" class="<%= type %> btn btn-default" value="<%= type %>" />
        <input type="button" class="cancel btn btn-default" value="Cancel" />
    </script>

    <!--edit note template -->    <!-- edit -->
    <script id="edit-comment-form-template" type="text/template" >
        <textarea class="feedback-note form-control" name="feedback_note" cols="68"><%= parentComment.comment %></textarea>
        <fieldset>
            <input type="button" class="submit-edit-comment btn btn-default" value="Submit" />
            <input type="button" class="cancel-edit-comment btn btn-default" value="Cancel" />
        </fieldset>
    </script>

    <script id="attach-file-form-template" type="text/template">
        <input class="attach-file" type="file" name="attach-file" />
        <input class="submit-attach-file btn btn-default btn-xs" type="submit" value="Submit" />
        <input class="cancel-attach-file btn btn-default btn-xs" type="button" value="Cancel" />
    </script>

	<script src="js/main.js"></script>
    <!-- backbone -->
    <script src="js/models/models-feedback.js"></script>
    <script src="js/collections/collections-feedback.js"></script>
    <script src="js/views/views-feedback.js"></script>
    <script src="js/controllers/controllers-feedback.js"></script>
    <script src="js/viewpdf.js"></script>

<?php// else :?>
<!-- <form id="staff&#45;review&#45;login" method="post">  -->
<!--     <p>  -->
<!--         <label for="username">Username: </label>  -->
        <!-- <input type="text" name="username" id="username" class="input" value="<?php //if(isset($_COOKIE['username'])) echo $_COOKIE['username']; ?>"/>  -->
<!--         <input type="text" name="username" id="username" class="input" value=""/>  -->
<!--     </p>  -->
<!--     <p>  -->
<!--         <label for="password">Password: </label>  -->
<!--         <input type="password" name="password" id="password" class="input form&#45;control staff&#45;login&#45;password" value="" /><br/> -->
<!--         Please visit the blog for current password.  -->
<!--     </p>  -->
<!--     <p>  -->
<!--         <input type="submit" name="login" id="staff&#45;review&#45;btn&#45;login" value="Login" />  -->
<!--     </p>  -->
<!-- </form>  -->

<?php// endif; ?>

<!-- demo.php -->
<?php

//require_once("dompdf_config.inc.php");
// include(app_path().'/dompdf/dompdf_config.inc.php');

// We check wether the user is accessing the demo locally
// $local = array("::1", "127.0.0.1");
// $is_local = in_array($_SERVER['REMOTE_ADDR'], $local);
?>
@stop


