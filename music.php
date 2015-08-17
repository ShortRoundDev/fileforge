<!DOCTYPE HTML>
<html>
    <head>
          <link rel="stylesheet" type="text/css" href="style.css">
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    </head>
    
    <body>
            <!-- Table for grid layout -->
        <table style="height: 100%">
            <tr>
                    <!-- Title -->
                <td style="width: 10%; vertical-align: top;">
                    <a href="index.php"><div class="title">
                        fileforge
                    </div></a>
                </td>
                
                    <!-- Music webapp area -->
                <td rowspan="2" style="vertical-align: top">
                        <!-- Search bar -->
                    <div id="searchbar">
                        <b>Song Listings</b>                                
                            <form method="GET" action="music.php" style="display: inline-block; float: right;">
                                <input type="text" name="searchparam" placeholder="Search Parameters">
                                <input type="submit" name="search" value="Search"> (Will Refresh Window)
                            </form>                            
                    </div>
                    
                        <!-- Main listings area -->
                    <div id="listings">
                    <?php                        
                        //comment this:
                        echo "<h1>YOU HAVEN'T DEFINED MYSQL CONNECTION INFO. OPEN MUSIC.PHP AND TRY AGAIN</h1>"
                        //uncomment this and insert password here:
                        //$musicdb = new mysqli("127.0.0.1", "root", [PASSWORD] , "fileforge");
                    
                        //get all songs by default
                    $musicQuery = "SELECT * FROM music";
                    
                        //if search is set, then search for those parameters.
                        //Search by path OR by song id
                    if(isset($_GET['search'])){
                        $musicQuery .= ' WHERE path LIKE "%' . mysqli_real_connect($musicdb, $_GET['searchparam']) . '%" OR id = "' . mysqli_real_escape_string($musicdb, $_GET['searchparam']) . '"';
                    }
                    
                    $musicQuery .= ';';
                    
                    $result = $musicdb->query($musicQuery);
                    
                        //echo results
                    while($row = mysqli_fetch_assoc($result)){
                        echo '<a style="color: inherit" href="#" onclick="playnew(this.parentNode)"><div class="musicListing" data-src="' . $row['path'] . '" song-id="'. $row['id'] . '">' . "\n" . $row['id'] . " - " . $row['path'] . '</a>' . "\n\n" . '<a style="float: right; color: inherit; text-decoration: none; font-size: 16px" onclick="addToPlaylist(this.parentNode)" href="#"  id="' . $row['id'] . '">' . "\n" . '<div class="musicAdd">+</div>' . "\n" . '</a>' . "\n</div>";
                    }
                    
                    ?>
                    </div>
                        <!-- Playlist Lookup Bar -->
                    <div id="searchbar">
                        <b>Playlist - </b>
                        
                            <!-- Search for playlist -->
                        <form style="display: inline-block" action="music.php" method="post">
                            <input type="password" name="playlistid" placeholder="Playlist ID">
                            <input type="submit" name="load" value="Load Playlist"> (Will Refresh Window)
                        </form>
                        
                            <!-- Save playlist.
                                
                                ***PASSWORD NOT SECURE***,
                                
                            but I'm not worried about the security of playlist information -->
                            
                        <form id="savePlaylist" style="display: inline-block; float:right" method="post">
                            <input type="text" id="playlistData" name="songids" style="display: none">
                            <input type="password" id="playlistId" name="playlistid" placeholder="Playlist Password">
                            <input type="button" name="save" value="Save Playlist" onclick="sendPlaylistData(this)"> (Will Redirect)
                        </form>
                    </div>
                    <div id="playlist">          
                        <?php
                                //Find playlist
                            if(isset($_POST['playlistid'])){
                                    //Probably a union way of doing this, but this works too.
                                    //  Get Id and path from table MUSIC and also query playlist_songs where the playlist_id
                                    //  is the password entered, and the id from music = the song_id from playlist_songs
                                    //  somehow this works
                                $playListQuery = 'SELECT id, path FROM music, playlist_songs WHERE playlist_id = "' . mysqli_real_escape_string($musicdb, $_POST['playlistid']) . '" AND id = song_id;';
                                if(!($playlistResults = $musicdb->query($playListQuery))){
                                    echo $musicdb->error;
                                }
                                
                                    //display results
                                while($playlistRow = mysqli_fetch_assoc($playlistResults)){
                                    echo '<div class="musicListing" data-src="' . $playlistRow['path'] . '" song-id="'. $playlistRow['id'] . '">' . "\n" . '<a style="color: inherit" href="#" onclick="playnew(this.parentNode)">' . "\n" . $playlistRow['id'] . " - " . $playlistRow['path'] . '</a>' . "\n\n" . '<a style="float: right; color: inherit; text-decoration: none; font-size: 16px" onclick="removeFromPlaylist(this.parentNode)" href="#"  id="' . $playlistRow['id'] . '">' . "\n" . '<div class="musicAdd">-</div>' . "\n" . '</a>' . "\n</div>";
                                }
                            }  
                                                
                        ?>
                    </div>                    
                </td>
            </tr>
            <tr>
                    <!--Navigation Menu-->
                <td style="vertical-align: top; height: 90%; z-index: -1">
                    <div class="menubox" style="z-index: -1">                
                        <a href="index.php"  style="text-decoration: none"><div class="menuoption">Upload</div></a>
                        <a href="music.php"  style="text-decoration: none"><div class="menuoption">Music</div></a>
                        <a href="about.html" style="text-decoration: none"><div class="menuoption">About</div></a>
                    </div>
                </td>                 
            </tr>
            <tr>
                    <!-- Music Player 
                         Colspan 2 to stretch across screen -->
                <td colspan="2">
                    <div id="player">
                            <!-- Skip back -->
                        <a href="#" id="skipback" data-src="" onclick="playnew(prevSong)" style="color:inherit; text-decoration: none; ">
                            <div class="musicButton" id = "skip">
                                <b>|</b>&#9664;
                            </div>
                        </a>
                        
                            <!-- Play -->
                        <a href="#" id="play" data-src="" onclick="play()" style="color: inherit; text-decoration: none;">
                            <div class="musicButton" id="play">
                                &#9658;
                            </div>
                        </a>             
                        
                            <!-- Pause -->
                        <a href="#" id="pause" data-src="" onclick="pause()" style="color: inherit; text-decoration: none;  height: 100%">
                            <div class="musicButton" id="play" style="font-size: 22px;  height: 100%">
                                &#9646;&#9646;
                            </div>
                        </a>
                        
                        <!-- Skip Forward -->
                        <a href="#" id="skipnext" data-src="" onclick="playnew(nextSong)" style="color:inherit; text-decoration: none;">
                            <div class="musicButton" id = "skip" style="font-size: 22px; line-height: 2; left: 50px; letter-spacing: -6px">
                                &#9658;<b style="line-height: 2">|</b>
                            </div>
                        </a> 
                        
                            <!-- Display song name. Not used right now -->
                        <div id="songname" style="float: left; padding-left: 1.5em">
                        </div>                                                        
                            
                            <!-- Change volume -->
                        <input type="range" oninput="changevol(this)" onchange="changevol(this)" id="volume" style="float: right; position: absolute; right: 20px; margin-top: 10px" value="100">                             
                    </div>
                </td>
            </tr>
                                
            <script>
                    //currentSong is music object
                var currentSong = 0;
                    //currentDiv is document element
                var currentDiv = 0;
                    //nextSong is document element
                var nextSong = 0;
                    //prevSong is document element
                var prevSong = 0;
                    //duration doesn't work on chrome so it's not used right now
                var duration = 0;                    
                var currentTime = 0;
                
                                
                    /** Ends previous song,
                        * de-colorizes currently playing div
                        * colorizes new div
                        * plays new div song
                        * gets next song and previous song
                        * plays song */
                function playnew(obj){
                        //revert color of current div to normal
                    $(currentDiv).css("color", "#354458");
                    currentDiv = obj;
                        
                        //end song being played
                    if(currentSong){
                        currentSong.pause();
                    }                    
                        
                        //load new song from div 
                    currentSong = new Audio($(obj).attr("data-src"));
                    currentTime = 0;
                        
                        //load data-src into music player. does nothing since play vars are global not local to each div. should remove.
                    $("#play").attr("data-src", $(obj).attr("data-src"));                    
                        
                        //play song
                    currentSong.play();     
                    
                        //change colors of surrounding divs to normal
                    $(obj).prev().css("color", "#354458");
                    $(obj).next().css("color", "#354458");
                        
                        //change new playing div to highlighted
                    $(obj).css("color", "#eb7260");
                        
                        //get previous and next songs
                    nextSong = $(obj).next();
                    prevSong = $(obj).prev();
                    
                        // if song has ended, play next in line (playlist or otherwise)
                    currentSong.addEventListener("ended", function(){
                        playnew(nextSong);
                    });
                    
                }
                
                    /** Plays song without starting new one.
                            * Makes sure song is at appropriate time*/
                function play(){
                    currentSong.currentTime = currentTime;
                    currentSong.play();                    
                }
                
                    /** pauses song and records current time to make sure not to lose place */
                function pause(){
                    currentTime = currentSong.currentTime;
                    currentSong.pause();
                }
                    
                /** changes volume to range of slider */
                function changevol(obj){
                    currentSong.volume = obj.value/100;
                }
                
                    /** moves music listing selected to playlist area*/
                function addToPlaylist(obj){
                    $("#playlist").append('<div class="musicListing" song-id="' + $(obj).attr("song-id") + '" data-src="' + $(obj).attr("data-src") + '"><a style="color: inherit" href="#" onclick="playnew(this.parentNode)">' + $(obj).attr("song-id") + ' - ' + $(obj).attr("data-src") + '</a><a style="float: right; color: inherit; text-decoration: none; font-size: 16px; cursor: pointer" onclick="removeFromPlaylist(this.parentNode)"><div class="musicAdd">-</div></a></div>');
                }
                
                    /** removes music listing selected from playlist area*/
                function removeFromPlaylist(obj){
                    $(obj).remove();
                }
                
                    /** get each id from songs in playlist to POST as csv for uploading playlist*/
                function getPlaylistSongs(){
                    var songs = "";
                    $("#playlist").children().each(function(index){
                        songs += $(this).attr("song-id") + ",";
                    });                                        
                    return songs;
                }
                    
                    /** POST playlist data to upload php script*/
                function sendPlaylistData(){
                    $("#playlistData").attr("value", getPlaylistSongs());                                
                    $("#savePlaylist").submit();
                }
            </script>            
            
    </body>

</html>