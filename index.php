<!DOCTYPE HTML>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

        <!-- Using table for grid layout-->
    <table style="height: 100%">
        <tr>
        
                <!-- Title -->
            <td style="width: 10%; vertical-align: top;">
                <a href="index.php"><div class="title">
                    fileforge
                </div></a>
            </td>
            
                <!-- Upload Window. rowspan is 2 to encompass entire document -->
            <td rowspan="2">
                <div id="uploadwindow" style="padding: 2em">
                    <table>
                        <tr>
                            <td>
                            Select file
                            </td>                            
                        </tr>
                        <tr><form method="post" enctype="multipart/form-data">
                            <td>
                                <input type="file" name="fileUpload" style="border-right: 1px solid white">                                
                            </td>
                            <td style="padding: 1em">
                                <input type="submit" class="upload" name="submit">
                            </td>
                        </form></tr>
                        <?php                                   
                                //Submit procedures
                            if(isset($_POST["submit"])) {                                                                                                
                                    //get file extension
                                $ext            = pathinfo($_FILES["fileUpload"]["name"], PATHINFO_EXTENSION);  
                                    
                                    //select folder for file to copy into based on extension
                                $filepath = "";
                                if ($ext === "mp3"){
                                    $filepath   = "music/";
                                }
                                    //TODO: handle misc files (non-images)
                                else{
                                    $filepath   = "images/";
                                }                                                                
                                    //combine filepath with filename
                                $outputfile     = $filepath  . basename($_FILES["fileUpload"]["name"]);
                                    //final check to see if upload is allowed
                                $uploadAllowed  = 1;
                                $imageFileType  = $_FILES["fileUpload"]["type"];
                                           
                                    //allow smaller size for images
                                if($_FILES["fileUpload"]["size"] > 500000 && $ext != "mp3"){
                                    echo "<tr><td>Sorry, file is too large</td></tr>";
                                    $uploadAllowed = 0;
                                }
                                    //larger size for images. Probably should change this but I kind of just threw it in there
                                if($_FILES["fileUpload"]["size"] > 50000000 && $ext == "mp3"){
                                    echo "<tr><td>Sorry, mp3 file is too large</td></tr>";
                                    $uploadAllowed = 0;
                                }
                                                                
                                    //copy file to server.
                                if(move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $outputfile) && $uploadAllowed == 1){
                                        
                                        //display path of file for download/sharing
                                    echo "<tr><td style=\"border-top: 1px solid white; border-right: 1px solid white\">Upload Complete - </td><td style=\"border-top: 1px solid white\">&nbsp;http://" . $_SERVER['HTTP_HOST'] . "/" .$outputfile . "</td></tr>";                                                                                                                
                                        
                                        //mysql db to catalogue music paths for music webapp
                                        //comment this:
                                         echo "<h1>YOU HAVEN'T DEFINED MYSQL CONNECTION INFO. OPEN MUSIC.PHP AND TRY AGAIN</h1>"
                                        //uncomment this and insert password:
                                    //$musicListDB = new mysqli("127.0.0.1", "root", [password], "fileforge");
                                        
                                    if ($musicListDB->connect_error) {
                                        die();
                                    }                                    
                                        //only record mp3 paths
                                    if($ext == "mp3"){
                                            //if any rows are returned for path, then path is already catalogued
                                        $duplicate = $musicListDB->query("SELECT id FROM music WHERE path = \"" . $outputfile . "\";");
                                        if($duplicate->num_rows == 0 ){
                                            
                                            $musicQuery = "INSERT INTO music(path) values(\"" . $outputfile . "\");";
                                                    
                                            $musicListDB->query($musicQuery);  
                                        }
                                        else{                                                
                                            echo "<tr><td>Duplicate detected</td></tr>";
                                        }
                                    }
                                            
                                    $musicListDB->close();
                                }
                            }
                        ?>
                    </table>
                </div>
            </td>    
        </tr>
        
            <!-- navigation box -->
        <tr>
            <td style="vertical-align: top; height: 90%;">
                <div class="menubox" style="display: inline-block">                
                    <a href="index.php"  style="text-decoration: none"><div class="menuoption">Upload</div></a>
                    <a href="music.php"  style="text-decoration: none"><div class="menuoption">Music</div></a>
                    <a href="about.html" style="text-decoration: none"><div class="menuoption">About</div></a>
                </div>
            </td>            
        </tr>        
    </table>
</body>