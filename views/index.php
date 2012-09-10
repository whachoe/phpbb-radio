<html>
  <head>
    <title><?php echo SITE_TITLE ?></title> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
    <meta http-equiv="Content-Language" content="en-us"> 
    <meta name="description" content="The Breakzforum.be Radio: Featuring the tracks posted on our forum">

    <link href='http://fonts.googleapis.com/css?family=Josefin+Sans:400,600' rel='stylesheet' type='text/css'></link>
    <link href='http://fonts.googleapis.com/css?family=Russo+One' rel='stylesheet' type='text/css'></link>
    <link href="css/jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" media="screen"  />
    <link href="js/jqgrid/css/ui.jqgrid.css" rel="stylesheet" type="text/css" media="screen"  />
    <link href="css/style.css" rel="stylesheet" type="text/css"></link>

    <script src="http://connect.soundcloud.com/sdk.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
    <script src="js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
    <script src="js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
    <script src="js/jplayer/jquery.jplayer.min.js"></script>
    <script src="js/radio.js.php"></script>

  </head>
  <body>
    <div id="wrapper">
      <h1><?php echo SITE_TITLE ?></h1>
      <div id="musicplayer">
        <div id="information">
          <div id="song-title"><span>Song Title</span>&nbsp; </div>
          <div id="song-artist"><span>Artist</span>&nbsp; </div>
          <div id="poster">
            <span id="posted_by_label">Posted by:</span> 
            <div id="post_link"><a href="#" target="_blank" title="Link to Post">cjpa</a> </div>
            <span>in </span> <div id="forum_link"><a href="#" target="_blank" title="Link to Forum">Dubstep</a> </div>
            <span>on</span> <span id="date_span">31 Aug 2012 08:49</span>

          </div>

          <div class="clear"></div>
        </div>

        <div id="player" class="jp-jplayer"></div>

        <div id="jp_container_1" class="jp-audio">
          <div class="jp-type-single">
            <div class="jp-gui jp-interface">

              <div class="jp-progress">
                <div id="times" class="jp-time-holder">
                  <div class="jp-current-time"></div>
                  <div class="jp-duration"></div>
                </div>
                <div class="jp-seek-bar">
                  <div class="jp-play-bar"></div>
                </div>

                <a id="original_url" href="#" target="_blank" title="Link to original media file">
                  <img id="track_type" width="16" height="16" src="img/type_mp3.png" />
                </a>
              </div>

            </div>
            <div class="jp-no-solution">
              <span>Update Required</span>
              To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
            </div>

          </div>

          <div id="jp-controls">
            <div id="play" class="jp-play"><img src="img/play.png" width="64" height="64"></div>
            <div id="pause" class="jp-pause"><img src="img/pause.png" width="64" height="64"></div>
            <div id="next"><img src="img/next.png" width="64" height="64"></div>
            <div class="clear"></div>
          </div>
        </div>

        <div id="filters">
          <div class="ui-widget-header ui-corner-top ui-helper-clearfix">
              <span class="ui-jqgrid-title">Filter</span>
          </div>

          <div id="filters_formwrapper">
            <form action="" method="POST">
              <?php echo $forum_selectbox; ?>
            </form>  
            <center><a href="#">∨ Close ∨</a></center>
          </div>
        </div>

        <div id="playlist">
          <table id="playlisttable"></table>
          <center><a href="#">∨ Close ∨</a></center>
        </div>

        <div id="anchorbar">
          <a id="playlistanchor" href="#" class="slideanchor">∧ Playlist</a>
          <a id="filteranchor" href="#" class="slideanchor">∧ Filter</a>
        </div>
      </div>

      <div class="clear" style="height: 0px"></div>
      <div id="footer">
        <span>#Active: <strong><?php echo $totalActiveCount ?></strong></span>
        <span>#Inactive: <strong><?php echo $totalInactiveCount ?></strong></span>
        <span>#Total: <strong><?php echo $totalTrackCount ?></strong></span>
        <a href="mailto:<?php echo CONTACTEMAIL ?>" title="<?php echo CONTACTTITLE ?>" target="_blank" style="color: #B2BAC5; float: right; margin-right: 25px">Contact</a>
      </div>

    </div>
  </body>
</html>