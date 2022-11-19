<?php
  include('../includes/connection.php');
  $started = false;
  $agent   = $_SERVER['HTTP_USER_AGENT'];
  $device  = "web";
  if (strstr($agent, 'iPad') || strstr($agent, 'iPhone')  || strstr($agent, 'Android 4.4')  || strstr($agent, 'Android 5.0') )
  {
    $device = "apple";
  }
  else if (strstr($agent, 'Android'))
  {
    $device = "android";
  }
  if(isset($_POST['name'])){
     $name  = mysql_real_escape_string($_POST['name']);
     $query = "INSERT INTO `college_name` (`name`, `device`) VALUES ('$name','$device')";
     mysql_query($query);
    }
  if($device == "apple")
  {
?>
<video src="http://54.255.134.170/ktulive/demo2/playlist.m3u8" controls width="640" height="480"></video>
<?php

  }
  else if($device == "android")
  {
      echo "<h2>Device not supported";die;
  }
  else
  {
?>
<head>
  <title>LIVE Event Streaming : A. P. J. Abdul Kalam Technological University</title>
  <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="/css/custom.css">
  <link rel="icon" type="image/x-icon" href="/images/ktu-fav.ico?" />
  <script src="jwplayer.js"></script>
  <script src="meta-xml.js"></script>
  <style>
  /* JW player css*/
     #player_wrapper{
        margin: 0 auto;
        margin-top: 20px;
     }
  </style>
</head>
<body>
<nav class="navbar top-menu-bg">
  <div class="container-fluid">
    <div class="navbar-header">
      <img src="/images/logo_final.png"/>
      <img style="vertical-align: top; padding-top: 22px;" src="/images/text-logo.png"> 
    </div>
  </div>
</nav>
<?php if($started){?>
<h3 align="center">WELCOMING FRESHERS</h3>
<div id="player"></div>
<?php } else{?>
  <div class="col-xs-12" style="text-align:center">
          <h4 align="center" class="event"> EVENT DETAILS ( EVENT NOT YET STARTED)</h4>
          <p class="title">Address by Prof. C. Ravindranath (Pro Chancellor & Honourable Minister of Education) </p><p>to First year students of A. P. J. Abdul Kalam Technological University.</p>
          <p class="date">Date: 03.08.2016 (Wednesday)</p>
          <p class="time">Time: 11.00 AM</p>
        </div>
 <?php } ?>
<script type="text/javascript">
/* JW player setup*/
var playerInstance2=jwplayer("player");
playerInstance2.setup({
  width:'640',
  height:'360',
  playlist: [{
           sources: [{ 
            file: "rtmp://54.255.134.170/ktulive/demo2"
        }]
    }],
  bufferlength:20,
  primary: "flash",
  autostart: true
});
</script>
<footer class="footer">
      <div class="container">
        <p class="text-muted"  align="center">Powered by <a href="http://enfintechnologies.com/">Enfin Technologies</a></p>
      </div>
 </footer>
</body>
<?php
}
?>