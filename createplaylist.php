
    <?php
            
        
        if(isset($_POST['playlistid']) && isset($_POST['songids'])){
        //mysql db to catalogue music paths for music webapp
                //comment this:
                echo "<h1>YOU HAVEN'T DEFINED MYSQL CONNECTION INFO. OPEN MUSIC.PHP AND TRY AGAIN</h1>"
                //uncomment this and insert password:                                    
            //$playlistdb = new mysqli("127.0.0.1", "root", [password], "fileforge");
            if($playlistdb->connect_errno){
                echo "error connecting<br>";
            }
            else{                
            }
            
                //one query per song id
            $query = [];
            
                //turn csv song ids into array of ids 
            $song_ids = explode(',', $_POST['songids']);            
            
                //delete old playlist and replace with new
            $playlistdb->query('DELETE FROM playlist_songs WHERE playlist_id="' . $_POST['playlistid'] . '";');
            for($i = 0; $i < count($song_ids)-1; $i++){   
                
                    //query each id individually. Don't know if this is the best way to do this since this is my first PHP/MySQL project
                $query[$i] = 'INSERT INTO playlist_songs(playlist_id, song_id) VALUES("' . $_POST['playlistid'] . '", ' . $song_ids[$i] . ' escape "\"); ';
            }            
                                    
            
                //display error for each query if it exists
            for($i = 0; $i < count($query); $i++){
                if(!$playlistdb->query($query[$i])){                    
                    echo $playlistdb->error;
                }
            }

            $playlistdb->close();
                
                //redirect to music page. Should fix this so it loads up playlist automatically
            header("Location: http://127.0.0.1/music.php");
            die();
        }
    ?>
