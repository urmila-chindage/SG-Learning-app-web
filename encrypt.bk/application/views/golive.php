<?php
//room username email userid mode
session_start();
include("config.php");
if(isset($_GET['id'])){
      $data        = $_GET['id'];
      $data        = base64_decode($data);
      $arr         = explode('#', $data);
      $room        = $arr[0];
      $user_name   = $arr[1];
      $user_email  = $arr[2];
      $user_id     = $arr[3];
      $mode        = $arr[4];

      // Find course and subtopic
      $session    = explode('_',$room);
      $course     = mysql_fetch_assoc(mysql_query("SELECT * FROM course_details WHERE course_id=".$session[0]." AND deleted=0"));
      $subtopic   = mysql_fetch_assoc(mysql_query("SELECT * FROM section WHERE course_id=".$session[0]." AND se_id=".$session[2].""));
      // 
   }else{
      echo '<h2>INVALID PARAMETERS ! </h2>';
      die;
   }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
      <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
      <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <link href='/live/assets/css/style.css?t=20079' rel='stylesheet' type='text/css'>
      <link href='/live/assets/css/sweet-alert.css' rel='stylesheet' type='text/css'>
      <link href="/live/assets/css/video-js.css" rel="stylesheet">
      <title>Live Class</title>
      <script>
         var room         = "<?php echo $room;?>";
         var user_name    = "<?php echo $user_name;?>";
         var user_email   = "<?php echo $user_email;?>";
         var user_id      = "<?php echo $user_id;?>";
         var socket_id    = "0";
         var mode         = "<?php echo $mode;?>";
         var avatar_path  = "http://live.nagathanschoolcollege.org/images/thumb/default.jpg";
         var device       = "<?php echo $device;?>";
         var flash        = 0;

      </script>
   </head>
   <body>
      <div class="pagewrap">
      <?php if($mode == '0'){?>
        <div id="left width100">
      <?php }else{?>
        <div id="left">
      <?php } ?>
      <?php print_r($subtopic); ?>
         <div class="top-strip"><div class="course-name"><?php echo $course['course_name']; echo " ( " .((strlen($subtopic['tittle']) > 25)?substr($subtopic['tittle'],0,23).'..':$subtopic['tittle'])." )"; ?></div></div>
            <div class="video-area">
               <div id="player" style="height:100%;"></div>
            </div>
            <div id="admin_instr" class="banner instr-bg">
               <div class="instructions">
                  <div class="left-part" style="width:20%;">
                     <i class="material-icons info-icon">info</i>
                  </div>
                  <div class="right-part" style="width:80%;">
                     <p style="font-size:28px;margin-top: 0; line-height: 36px;">Please publish video using your desktop encoder application</p>
                     <p><b>RTMP URL : <span id="rtmp-url"></span></b></p>
                     <p><b>STREAM NAME : <span id="stream-name"></b></p>
                     <p>Once you have successfully publish your camera using the desktop encoder then this information will automatically hide and your video will be shown to all users</p>
                     <p>1. should give above mensioned rtmp url and stream name</p>
                     <p>2. should select working microphone and camera in the encoder</p>
                     <p>3. recomended encoder is open broadcaster software <a href="https://github.com/jp9000/obs-studio/releases/download/0.16.6/OBS-Studio-0.16.6-Full-Installer.exe">download</a></p>
                  </div>
               </div>
            </div>
            <div id="user_instr" class="banner instr-bg">
               <div class="instructions">
                  <div class="left-part" style="width:20%;">
                     <i class="material-icons info-icon">info</i>
                  </div>
                  <div class="right-part" style="width:80%;">
                     <p style="font-size:28px;margin-top: 0; line-height: 36px;">Presenter Not Publishing Camera</p>
                     <p> Presneter not publishing the camera . Please wait here until he setup and publishing  the live session</p>
                     <p> Make sure your internet and playback devices working properly.</p>
                  </div>
               </div>
            </div>
            <span class="btn-panel">
            <div class="start-butn" style="right: 10px;display: none;" id="record-start">Start Recording<span class="timer"></span></div>
            <div class="start-butn rec-active" style="left: 10px;" id="leave">Leave Session<span class="timer"></span></div>
         </div>
      </div>
      <?php if($mode == '0'){?>
        <div id="right" style="display: none;">
      <?php }else{?>
        <div id="right" >
      <?php } ?>
         <ul  class="nav nav-pills livevideotabs">
            <li class="active"  id="tabtwo" style="width: 100%;"><a>Participants</a></li>
            <li id="tabone" style="display: none;"><a>Questions</a></li>
         </ul>
         <div style="display:none" id="box-one" class="chat-section">
            <div class="smallpad">
               <ul id="chat_list" class="list-unstyled media-block">
               </ul>
               <input id="chat-input" type="text" class="form-control" placeholder="Type message here..."/>
               <button class="btn send-chat"></button>
            </div>
         </div>
         <div id="box-two"  class="chat-section">
            <div class="chat-participants">
            </div>
         </div>
      </div>
      </div>
      <script type="text/javascript" src="/live/assets/js/jquery.min.js"></script>
      <script src="/live/assets/js/socket.io.js"></script>
      <script src="/live/assets/jwplayer/jwplayer.js"></script>
      <script src="/live/assets/jwplayer/meta-xml.js"></script>
       <script type="text/javascript" src="/live/assets/js/sweet-alert.js"></script>
       <script src="/live/assets/js/video.js"></script>
       <script src="/live/assets/js/videojs-hls.js"></script>
       <script src="/live/assets/js/videojs-resolution-switcher.js"></script>
      <script type="text/javascript" src="/live/assets/js/main.js?t=453453"></script>  
   </body>
</html>