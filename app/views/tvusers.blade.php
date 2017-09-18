@extends('master')

@section('content')

    @include('navbar')

   	<link href="css/feedback.css" rel="stylesheet" type="text/css"> 

    <div id="page-wrapper">
        <h1>Tvusers List </h1>

        <input class="btn btn-default" id="add-user-btn" type="button" value="Add User">

        <div id ="addClient"></div>

        <div class="boostrap-table">
            <table id="allClients" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><b>Username</b></th>
                        <th><b>Email</b></th>
                        <th><b>Password</b></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>

        <div id="editClient"></div>

    </div> <!-- end #page-wrapper-->


    <!-- client templates -->
    <script id="allClientsTemplate" type="text/template">
        <td><%= username %></td>
        <td><%= email %></td>
        <td><%= password_unencrypted %></td>
        
        <td><a href="#clients/<%= id%>/edit" class="edit">Edit</a></td>
        <td><a href="#clients/<%= id%>" class="delete">Delete</a></td>
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
    <script src="js/models/models-tvusers.js"></script>
    <script src="js/collections/collections-tvusers.js"></script>
    <script src="js/views/views-tvusers.js"></script>
    <script src="js/controllers/controllers-tvusers.js"></script>
@stop
