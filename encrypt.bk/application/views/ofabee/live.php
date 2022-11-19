<?php
if($role == 'presenter'){ 
  include $_SERVER['DOCUMENT_ROOT'].'/application/views/admin/header.php';
}else{
  include 'header.php';
}  
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
   if($device == "apple" || $device == "android")
   {
   ?>
<!DOCTYPE html>
<html>
   <head>
      <meta name="viewport" content="width=device-width, initial-scale=1">
   </head>
   <body>
      <h2>Virtual Class only supported in windows,ubuntu,mac desktop machine with flash player installed browsers</h2>
   </body>
</html>
<?php
   }else
   {
   ?>
<!DOCTYPE html>
<html>
   <head>
      <style>
         .player-div{margin:20px 0 20px 0;}
         object{
          margin-top:5px;
          margin-bottom: 5px;
          
         }
         .container{
          width: 100%;
         }
         .flash-player{
            margin:0 auto;
            width: 1019px;
         }
      </style>
     <?php if($role == 'presenter'){?>
      <style type="text/css">
        .flash-player{
          margin-top: 60px !important;
        }
      </style>
      <?php } ?>
      <script type="text/javascript">
          var __live_id = '<?php echo $live_id ?>';
      </script>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <script src="<?php echo assets_url('live') ?>join/jwplayer.js"></script>
      <script src="<?php echo assets_url('live') ?>join/join_live.js"></script>
      <script src="<?php echo assets_url('live') ?>join/meta-xml.js"></script>
   </head>
   <body>
      <div class="container">
        <h4 class="my-italic font-bold"><?php echo $course_name." - ".$lecture_name ?></h4>
      <div class="flash-player">
         <object type="application/x-shockwave-flash" id="OfabeeFlipClassRoom" name="OfabeeFlipClassRoom" align="middle" data="<?php echo assets_url('live') ?>OfabeeFlipClassRoom.swf" width="1019" height="488"><param name="quality" value="high"><param name="bgcolor" value="#ffffff"><param name="allowscriptaccess" value="always"><param name="allowfullscreen" value="true"><param name="flashvars" value="v1=<?php echo $v1;?>&amp;v2=<?php echo $v2;?>&amp;v3=<?php echo $v3;?>&amp;v4=<?php echo $v4;?>&amp;v5=<?php echo $v5;?>&amp;v6=<?php echo $v6;?>&amp;v7=<?php echo $v7;?>&amp;v8=<?php echo $v8;?>&amp;v9=<?php echo $v9;?>&amp;v10=<?php echo $v10;?>&amp;v11=<?php echo $v11;?>&amp;v12=<?php echo $v12;?>&amp;v13=<?php echo $v13;?>&amp;v14=<?php echo $v14;?>&amp;v15=<?php echo $v15;?>">
         </object>
         </div>
      </div>
      <?php 
      if($role != 'presenter')
        include_once('footer.php');
      ?>
   </body>
</html>
<?php
   }
   ?>