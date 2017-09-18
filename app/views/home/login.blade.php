<!-- app/views/login.blade.php -->
@extends('master')
@section('content')

    <link href="css/feedback.css" rel="stylesheet" type="text/css">

    <div id="page-wrapper">
        <div id="login-wrapper">

            <h1>Video Feedback Login</h1>
            {{ Form::open(array('url' => 'login', 'class' => 'login-form')) }}

            <!-- if there are login errors, show them here -->
            <div class="form-group">
                {{ $errors->first('email') }}
                {{ $errors->first('password') }}
            </div>

            <div class="form-group">
                {{ Form::text('email', Input::old('email'), array('autofocus' => 'autofocus', 'class' => 'form-control', 'placeholder' => 'Email')) }}
            </div>

            <div class="form-group">
                {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) }}
            </div>

            <p>{{ Form::submit('Submit!', array('class' => 'btn btn-default login-submit-btn') ) }}</p>
            {{ Form::close() }}

        </div> <!-- end #login-wrapper -->
    </div> <!-- end #page-wrapper -->

@stop

