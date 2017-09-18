<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
                <!-- <a class="navbar&#45;brand" href="{{ URL::to('admin') }}">Video Feedback</a> -->
                <!-- <a class="navbar&#45;brand" href="#">Video Feedback</a> -->
            <!-- <a class="" href="{{ URL::to('admin') }}#">Video Feedback</a> -->
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <!-- <li class="active"><a href="fee">Home</a></li> -->

                @if ( Auth::tvtvuser()->check() )
                    <li class="<?php if (Request::path() == 'admin'){ echo 'active'; }?>">
                        <a href="{{ URL::to('admin') }}">Video Feedback</a>
                    </li>
                    <li class="<?php if (Request::path() == 'videoform'){ echo 'active'; }?>">
                        <a href="{{ URL::to('videoform') }}">Upload Video</a>
                    </li>                                <!-- <li><a href="{{ URL::to('admin') }}">View Videos</a></li> -->
                    <li class="<?php if (Request::path() == 'tvusers'){ echo 'active'; }?>">
                        <a href="{{ URL::to('tvusers') }}">Tvusers</a>
                    </li>
                @else
                    <li>
                        @if (Request::path() == 'viewpdf')
                            <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Video Feedback</a>
                        @else
                            <p>Video Feedback</p>
                        @endif
                    </li>

                @endif 

                <!-- <li class="dropdown"> -->
                <!--     <a href="#" class="dropdown&#45;toggle" data&#45;toggle="dropdown" role="button" aria&#45;expanded="false">Dropdown <span class="caret"></span></a> -->
                <!--     <ul class="dropdown&#45;menu" role="menu"> -->
                <!--         <li><a href="#">Action</a></li> -->
                <!--         <li><a href="#">Another action</a></li> -->
                <!--         <li><a href="#">Something else here</a></li> -->
                <!--         <li class="divider"></li> -->
                <!--         <li class="dropdown&#45;header">Nav header</li> -->
                <!--         <li><a href="#">Separated link</a></li> -->
                <!--         <li><a href="#">One more separated link</a></li> -->
                <!--     </ul> -->
                <!-- </li> -->
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <!-- <li><a href="../navbar/">Default</a></li> -->
                <!-- <li class="active"><a href="./">Static top <span class="sr&#45;only">(current)</span></a></li> -->
                <!-- <li><a href="../navbar&#45;fixed&#45;top/">Fixed top</a></li> -->
                <li>
                    <p id="user">
                        @if ( Auth::tvtvuser()->check() ) 
                            {{ Auth::tvtvuser()->get()->email }}
                        @elseif ( Auth::client()->check() )
                            {{ Auth::client()->get()->email }}
                        @endif
                    </p>
                </li>
                <li><a id="logout-btn" href="{{ URL::to('logout') }}" class="btn btn-default" >Logout</a></li>

            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

