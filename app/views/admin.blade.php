@extends('master')
@section('content')
    @include('navbar')

    <link rel="stylesheet" href="css/feedback.css" type="text/css">

    <div id="page-wrapper">

        <h1>Video Feedback List</h1>
        <div class="boostrap-table">
            <table id="allVideos" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Video</th>
                        <th><b>Duration</b></th>
                        <th><b>Description</b></th>
                        <th><b>Url</b></th>
                        <th><b>Active</b></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>

        <div id="editClient"></div>

    </div>

    <!-- client templates -->
    <script id="all-videos-template" type="text/template">
        <td>
            <%var video_title =video_title.split("_")%>
            <% video_title.splice(0,1)%>
            <%= video_title.join("_") %>
        </td>
        <td><%= duration %></td>
        <td><%= description %></td>
        <td class="url"><a href="<%=url%>"><%=url%></a> </td>
        <td><input type="checkbox" id="<%= id %>" class="video-active"  <% if (active) {%>checked="checked"<%}%>></td>
        <td><a href="#videos/<%= id%>" class="delete">Delete</a></td>
    </script>

    <script id="task-template" type="text/template">
        <span><%= title %></span><button class="edit">Edit</button> <button class="delete">Delete</button>
    </script>

    <script id="add-videos-template" type="text/template">
        <form id='add-client-form'>
            <div class="form-group">
                <label for="username"> Username: </label>	
                <input class="form-control" type="text" id="username">
            </div>

            <div class="form-group">
                <label for="email">Email: </label>	
                <input class="form-control" type="text" id="email" placeholder="default: username@transvideo.com">
            </div>

            <div class="form-group">
                <label for="password_unencrypted">Password: </label>	
                <input class="form-control" type="text" id="password_unencrypted" placeholder="default: feedback">
            </div>
            <div>
                <input class="btn btn-default" type="submit" value="Add User">
                <button type="button" class="cancel btn btn-default">Cancel</button>

            </div>
        </form>
    </script>

    <script id="edit-videos-template" type="text/template">
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
                <input type="text" class="form-control" id="edit_password_unencrypted" name="edit_password_unencrypted" value="<%= password_unencrypted %>">
            </div>
            <div>
                <input type="submit" class="btn btn-default" value="Edit User">
                <button type="button" class="cancel btn btn-default">Cancel</button>
            </div>
        </form>
    </script>


    <script src="js/main.js"></script>

    <!-- clients -->
    <script src="js/models/models-videos.js"></script>
    <script src="js/collections/collections-videos.js"></script>
    <script src="js/views/views-videos.js"></script>
    <script src="js/controllers/controllers-videos.js"></script>

@stop
