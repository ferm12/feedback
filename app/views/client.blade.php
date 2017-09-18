@extends('master')
@section('content')
    @include('navbar')

    <link rel="stylesheet" href="css/feedback.css" type="text/css">

    <div id="page-wrapper">

        <h1>Client Video List</h1>
        <?php
            $client = Client::find(Input::get('c'));
            $video = Video::find($client->video_id);
        ?>
        <div class="boostrap-table">
            <table id="allVideos" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Video</th>
                        <th><b>Duration</b></th>
                        <th><b>Description</b></th>
                        <th><b>Url</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $video->video_title; ?></td>
                        <td><?php echo $video->duration; ?></td>
                        <td><?php echo $video->description; ?></td>
                        <td>
                            <a href='<?php echo $video->url; ?>'><?php echo $video->url; ?></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop
