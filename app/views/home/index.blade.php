@extends('master')

@section('content')
    
    @include('navbar')

    <link href="css/feedback.css" rel="stylesheet" type="text/css">

    <?php
        function isRunning($pid){
            $result = shell_exec(sprintf("ps %d", $pid));
            if (preg_match("/([1-9])\d+/", $result)){
                return true;
            }else{
                return false;
            }
        }
    ?>

    <?php 
        $video = DB::table('videos')->where('video_title', $_GET["video"] )->first(); 
        
        $pid_file = $video->pid_file;
        $pid = trim(file_get_contents($pid_file));

        $clients_ids = explode(',', $video->clients_ids);
        $access_granted = false;
    ?>

    <div id="page-wrapper">
        <div id="page-inner">

            <!-- This foreach loop is garantee to iterate over $access_clients_ids at least once -->
            @foreach ( $clients_ids as $client_id )

                @if ( Auth::client()->check() )
                    <?php $client_id = Auth::client()->get()->id; ?>
                @else
                    <?php $client_id = NULL; ?>
                @endif

                @if ( ($client_id == $client_id && $video->active) || Auth::tvtvuser()->check() ) 
                    <!-- <h1>Video Feedback<h1> -->
                    <h1>
                        <?php
                            $title = explode("_", $_GET["video"]);
                            $title = array_slice($title, 1);
                            echo implode("_", $title);
                        ?>
                    </h1>
                    <input type="hidden" id="video-title" value="<?php echo $_GET["video"]; ?>"/>
                    <input type="hidden" id="video_id" value="{{ $video->id }}" />
                    
                    @if ( Auth::tvtvuser()->check() )
                            <div>
                                <a class="clients-more-less more-less-toggle" href="javascript:void(0)">-</a>
                                <span class='clients-more-less'>Clients</span>
                            </div>

                        <div id="client">
                            <input class="btn btn-default" id="add-client-btn" type="button" value="Add Client">
                            <div id="addClient"></div>

                            <div class="boostrap-table">
                                <table id="allClients" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><b>Username</b></th>
                                            <th><b>Email</b></th>
                                            <th><b>Password</b></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div id="editClient"></div>

                        </div> <!-- end .client -->
                    @endif

                    <!-- <h1>Video Feedback Prototype</h1> -->
                    @if ( isRunning($pid) )
                        <h2>Conversion In Progress</h2>
                        <div id="progress-wrap">
                            <p>Your video is currently being converted to HTML5 video formats. Please, check back later.</p>
                            <div class="progress">
                                <div id="conversion-progress-bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <input type="hidden" id="progress_file" value="<?php echo $video->progress_file; ?>">
                            <input type="hidden" id="pid_file" value="<?php echo $video->pid_file; ?>">
                            <input type="hidden" id="total_frames" value="<?php echo $video->total_frames; ?>">
                        </div>
                    @endif

                    <div class="controls">
                        <div class="menu move" ><img src="img/move.png" data-toggle="tooltip" title="Move tool" data-placement="left"/></div>
                        <div class="menu pen current-tool"><img src="img/pen.png" data-toggle="tooltip" title="Pen tool" data-placement="left"/></div>
                        <div class="menu rect"><img src="img/rect.png" data-toggle="tooltip" title="Rect tool" data-placement="left"/></div>
                        <div class="menu ellipse"><img src="img/ellipse.png" data-toggle="tooltip" title="Ellipse tool" data-placement="left"/></div>

                        <div id="current-color" class="menu" data-toggle="tooltip" title="Choose color" data-placement="left"><img src="img/black.png"/></div>
                        <div class="flyout-colors">
                            <div class="flyout-menu black"><img src="img/black.png"/></div>
                            <div class="flyout-menu blue"><img src="img/blue.png"/></div>
                            <div class="flyout-menu green"><img src="img/green.png"/></div>
                            <div class="flyout-menu red"><img src="img/red.png"/></div>
                            <div class="flyout-menu yellow"><img src="img/yellow.png"/></div>
                        </div>

                        <div id="current-stroke" class="menu"><img src="img/stroke-one.png" data-toggle="tooltip" title="Pen size" data-placement="left"/></div>
                        <div class="flyout-stroke">
                            <div class="flyout-menu stroke-one"><img src="img/stroke-one.png"/></div>
                            <div class="flyout-menu stroke-two"><img src="img/stroke-two.png"/></div>
                            <div class="flyout-menu stroke-three"><img src="img/stroke-three.png"/></div>
                        </div>

                        <div class="menu clear"><img src="img/eraser.png" data-toggle="tooltip" title="Erase all" data-placement="left"/></div>

                    </div><!-- end #controls -->

                    <div id="video-wrapper">
                        <div id="video-container">

                        @unless ( isRunning($pid) )
                            <video id="<?php echo $_GET['video']?>" class="vjs-quicktime-skin video-js" controls preload="auto" width="640" height="360">
                                <source src="video_review/<?php echo $_GET["video"]."/".$_GET["video"].'.mp4'; ?>" type='video/mp4'>
                                <source src="video_review/<?php echo $_GET["video"]."/".$_GET["video"].'.webm'; ?>" type='video/webm'>
                            </video>
                        @endunless

                        </div>
                        <div id="svg-container"></div>

                        <div id="playback-controls-container"></div>

                        <div id="new-comment-container"></div>

                        <!-- <div id="list&#45;feedback&#45;container"></div> -->
                        <div id="comments-tree"></div>

                        <!-- <br/><center><a href="{{ URL::to('viewpdf') }}" id="print&#45;pdf&#45;button" class="btn&#45;primary btn&#45;large"> View PDF</a></center> -->
                        <br/><center><a href="viewpdf?video=<?php echo $_GET['video']; ?>" id="print-pdf-button" class="btn btn-info"> View PDF</a></center>

                    </div>

                    <?php $access_granted = true; break; ?> 
                
                @endif

            @endforeach

            <!-- if access is not granted display this h3 -->
            @unless ($access_granted) 
                <h3>
                    <span style="color:red;"><?php echo Auth::client()->get()->username; ?></span> doesn't have access to <?php echo $_GET["video"]; ?>, or the video file no longer available, please ask admin for access <a href="{{ URL::to('login') }}" >login</a>
                </h3>
            @endunless
        </div><!-- end #page-ineer -->
    </div><!-- end #video-wrapper -->
 
    <!-- start client templates -->
    <script id="allClientsTemplate" type="text/template">
        <td><%= username %></td>
        <td><%= email %></td>
        <td><%= password_unencrypted %></td>
        <td>
            <a href="#clients/<%= id%>/edit" class="edit">Edit</a>&nbsp;&nbsp;
            <a href="#clients/<%= id%>" class="delete">Delete</a>
        </td>
    </script>

    <script id="taskTemplate" type="text/template">
        <span><%= title %></span><button class="edit">Edit</button> <button class="delete">Delete</button>  
    </script>

    <script id="addClientTemplate" type="text/template">
        <form id='add-client-form'>
            <div class="form-group">
                <label for="username"> Username: </label>	
                <input class="form-control" type="text" id="username">
            </div>

            <div class="form-group">
                <label for="email">Email: </label>	
                <input class="form-control" type="text" id="email">
            </div>

            <div class="form-group">
                <label for="password_unencrypted">Password: </label>	
                <input class="form-control" type="text" id="password_unencrypted" placeholder="default: feedback">
            </div>
            <div>
                <input class="btn btn-default" type="submit" value="Add Client">
                <button class="cancel btn btn-default"  type="button">Cancel</button>

            </div>
        </form>
    </script>

    <script id="editClientTemplate" type="text/template">
        <form id='editClient'>
            <div class="form-group">
                <label for="edit_username">Username: </label>	
                <input class="form-control" type="text" id="edit_username" name="edit_username" value="<%= username %>">
            </div>

            <div class="form-group">
                <label for="edit_email">Email: </label>	
                <input class="form-control" type="text" id="edit_email" name="edit_email" value="<%= email %>">
            </div>

            <div class="form-group">
                <label for="edit_password_unencrypted">Password: </label>	
                <input class="form-control" type="text" id="edit_password_unencrypted" name="edit_password_unencrypted" value="<%= password_unencrypted %>">
            </div>
            <div>
                <input class="btn btn-default" type="submit" value="Edit Client">
                <button class="cancel btn btn-default"  type="button">Cancel</button>
            </div>
        </form>
    </script>
    <!-- end client templates -->
                <!-- <video controls preload="auto"> -->
                <!--     <source src="video_review/<?php echo $_GET["video"]."/".$_GET["video"].'.mp4'; ?>" type='video/mp4'> -->
                <!--     <source src="video_review/<?php echo $_GET["video"]."/".$_GET["video"].'.webm'; ?>" type='video/webm'> -->
                <!-- </video> -->

   <!-- <% if (parentComment.attachemts === null) { %> inactive<% } %> -->

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
                                <a class="<%=file_name_underscore%> delete-attachment" href='javascript:void(0)'><img src="/img/close.png" /></a>
                                <a class="<%=file_name_underscore%> attached-file" href="/video_review/<%= url %>" target="_blank" ><img src="/img/image-icon.png"/><figcaption><%=file_name_period%></figcaption></a>
                            </figure>
                        <%}%>
                        <% if (type == "video" ) { %>
                            <figure>
                                <a class="<%=file_name_underscore%> delete-attachment" href='javascript:void(0)'><img src="/img/close.png" /></a>
                                <a class="<%=file_name_underscore%> attached-file" href="/video_review/<%= url %>" target="_blank"><img src="/img/video-icon.png"/><figcaption><%=file_name_period%></figcaption></a>
                            </figure>
                        <%}%>
                        <% if (type == "text" ) { %>
                            <figure>
                                <a class="<%=file_name_underscore%> delete-attachment" href='javascript:void(0)'><img src="/img/close.png" /></a>
                                <a class="<%=file_name_underscore%> attached-file" href="/video_review/<%= url %>" target="_blank"><img src="/img/text-icon.png"/><figcaption><%=file_name_period%></figcaption></a>
                            </figure>
                        <%}%>
                        <% if (type == "pdf" ) { %>
                            <figure>
                                <a class="<%=file_name_underscore%> delete-attachment" href='javascript:void(0)'><img src="/img/close.png" /></a>
                                <a class="<%=file_name_underscore%> attached-file" href="/video_review/<%= url %>" target="_blank"><img src="/img/pdf-icon.png"/><figcaption><%=file_name_period%></figcaption></a>
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
                            <!-- date = date.split(" "); -->
                    <!-- <%= date[1] %>&#38;nbsp;<%= date[2] %> -->

    <script id="attach-file-form-template" type="text/template">
        <input class="attach-file" type="file" name="attach-file" />
        <input class="submit-attach-file btn btn-default btn-xs" type="submit" value="Submit" />
        <input class="cancel-attach-file btn btn-default btn-xs" type="button" value="Cancel" />
    </script>

    <!-- feedback templates --> <!--playback controls-->
    <script type="text/template" id="playback-controls-template">
        <nav class="cuepoint-nav">
            <ul>
                <li>
                    <a href="#prev" class="prev<% if (prev_cuepoint === null) { %> inactive<% } %>" data-toggle="tooltip" data-placement="bottom" data-original-title="previous">&laquo;</a>
                </li>
                <li class="cuepoint-icons">
                    <ul class="cuepoints"></ul>
                </li>
                <li>
                    <a href="#next" class="next<% if (next_cuepoint === null) { %> inactive<% } %>" data-toggle="tooltip" data-placement="bottom" data-original-title="next">&raquo;</a>
                </li>
            </ul>
        </nav>
        <p>
            <a href="#add-note" class="add-note btn btn-info">New Note</a>
        </p>
    </script>
            <!-- <a href="#add&#45;note" class="add&#45;note btn btn&#45;primary btn&#45;large">New Note</a> -->

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

    <script src="js/main.js"></script>

    <!-- svg -->
    <!-- <script src="js/libs/svg&#45;js/svg.min.js"></script> -->
    <script src="js/libs/svg-js/svg.js"></script>
    <!-- <script src="js/libs/svg&#45;js/svg.export.js"></script> -->
    <!-- <script src="js/libs/svg&#45;js/svg.parser.js"></script> -->
    <!-- <script src="js/libs/svg&#45;js/svg.import.js"></script> -->
    <script src="js/libs/svg-js/svg.draggable.js"></script>

    <script src="js/controllers/controllers-svg.js"></script>
    <script src="js/libs/jquery/jquery.form.js"></script>
    

    <!-- feedback -->
    <script src="js/models/models-feedback.js"></script>
    <script src="js/collections/collections-feedback.js"></script>
    <script src="js/views/views-feedback.js"></script>
    <script src="js/controllers/controllers-feedback.js"></script>

    
    <script src="js/models/models-clients.js"></script>
    <script src="js/collections/collections-clients.js"></script>
    <script src="js/views/views-clients.js"></script>
    <script src="js/controllers/controllers-clients.js"></script>

@stop


