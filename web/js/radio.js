// Global Variables
var current_track = null;
var apiurl = 'api';

// Initialize Soundcloud API
SC.initialize({
  client_id: sc_client_id
});

// This gets called when we get back a track from the backend
function checkData(data) {
    if (data && data.url) {
      if (data.type == "mp3") {
        $('#player')
        .jPlayer('setMedia', {
          mp3: data.url
        })
        .jPlayer('play');

        updateViews(data);
        return;
      }

      if (data.type == "soundcloud" || data.type == "soundcloud_embed") {
        if (data.stream_url && data.streamable) {
          $('#player')
            .jPlayer('setMedia', {
              mp3: data.stream_url+"?client_id="+data.soundcloud_api_key
            })
            .jPlayer('play');

            updateViews(data);
            return;
        }

        // Failsafe if we don't have cached soundcloud-data in our db
        $.ajax({
          url:  'http://api.soundcloud.com/resolve.json',
          data: {
            "url" : data.url,
            "client_id" : data.soundcloud_api_key
          },
          success : function (soundcloud_data) {
            if (soundcloud_data && soundcloud_data.streamable == true) {
              $('#player')
              .jPlayer('setMedia', {
                mp3: soundcloud_data.stream_url+"?client_id="+data.soundcloud_api_key
              })
              .jPlayer('play');

              //data.soundcloud_data = soundcloud_data;
              data.artist = soundcloud_data.user.username;
              data.songtitle = soundcloud_data.title;
              updateViews(data);
            } else {
              getNext(false);
            }
          },
        statusCode: {
          404: function() {
            getNext(false);
          }
        }
        });
      }
    }
  }

function getNext(skipped) {
  var current_track_id = (current_track ? current_track.id : "");

  $.get(apiurl+'/next', {
    "previous_track_id" : current_track_id,
    "skipped" : skipped,
    "forum_id": $("#filter_forum").val()
  }, function(data) { checkData(data) }, "json");
}

function play(id) {
  var current_track_id = (current_track ? current_track.id : "");

  $.get(apiurl+'/play', {
    "previous_track_id" : current_track_id,
    "id" : id
  }, function(data) { checkData(data) }, "json");
}

function updateViews(data) {
  // Keep a global reference to our current track
  current_track = data;

  // Setting the values
  poster_name = data.poster_name;
  time = data.post_time;
  slash = data.forum_name.lastIndexOf('/');
  if (slash > -1) forum_name = data.forum_name.substr(slash+1);
  else            forum_name = data.forum_name;

  if (data.type != "mp3") {
    songtitle = data.songtitle;
    artist    = data.artist;
  } else {
    songtitle = data.songtitle;
    artist    = "Unknown";
  }

  // Showing all that info
  $("#song-title span")
  .fadeOut('fast').hide()
  .html(songtitle)
  .attr('title', songtitle)
  .fadeIn('fast');

  $("#song-artist span")
  .fadeOut('fast').hide()
  .html(artist)
  .fadeIn('fast');

  $("#post_link a")
  .fadeOut('fast').hide()
  .attr("href", data.post_url)
  .html(poster_name)
  .fadeIn('fast');

  $("#forum_link a")
  .fadeOut('fast').hide()
  .attr("href", data.forum_url)
  .html(forum_name.substring(0,22))
  .fadeIn('fast');

  $("#date_span")
  .fadeOut('fast').hide()
  .html(time)
  .fadeIn('fast');

  $("#original_url")
  .attr('href', data.url)

  $("#track_type")
  .fadeOut('fast').hide()
  .attr('src', data.type_img)
  .on("click", function () {
    window.open($("#original_url").attr("href"), '_blank');
    return true;
  })
  .fadeIn('fast');

  // Adding to playlist
    playlistline = {"songtitle": '<a href="#" trackid="'+data.id+'" title="'+songtitle+'">'+songtitle+'</a>',
      "artist": artist, "poster_name": poster_name, "forum_name": forum_name};
  jQuery("#playlisttable").jqGrid('addRowData',0,playlistline);


}

$(document).ready(function(){
  // The Player initialization: Must be done after we got the client-id
  $("#player").jPlayer({
    ready: function () {
      getNext(false);
    },
    ended: function() {
      getNext(false);
    },
    swfPath: "/js/jplayer",
    solution: "html,flash",
    supplied: "mp3"
  });

  // Fast forward button
  $("#next img").on("click", function(event) {
    getNext(true);
  });

  // Show filter screen
  $("#filteranchor").on("click", function(event) {
    $("#filters").slideDown(500);
    event.preventDefault();
  });

  // Hide filter screen
  $("#filters a").on("click", function(event) {
    event.preventDefault();
    $("#filters").slideUp(500);

    // When we change forum, lets see if we need to get a new tune
    forum_filter_id = $("#filter_forum").val();
    if (forum_filter_id != 0 && current_track.forum_id != forum_filter_id) {
      getNext();
    }
  });

  // Show playlist screen
  $("#playlistanchor").on("click", function(event) {
    $("#playlist").slideDown(500);
    event.preventDefault();
  });

  // Hide playlist screen
  $("#playlist a").on("click", function(event) {
    event.preventDefault();
    $("#playlist").slideUp(500);
  });

  // Click on a song in the playlist
  $(document).on("click", "a[trackid]", function(event) {
    event.preventDefault();
    play($(event.target).attr("trackid"));
    $("#playlist").slideUp(500);
  });

  // JQGrid playlist
  jQuery("#playlisttable").jqGrid({
    datatype: "local",
    width: 486,
    height: 200,
    altRows: true,
   	colNames:['Title','Artist', 'Poster', 'Forum'],
   	colModel:[
   		{name:'songtitle',index:'songtitle', width:130, sorttype:"string"},
   		{name:'artist',index:'artist', width:60},
   		{name:'poster_name',index:'poster_name', width:60},
   		{name:'forum_name',index:'forum_name', width:80}
   	],
   	multiselect: false,
   	caption: "Playlist"
  });


});
